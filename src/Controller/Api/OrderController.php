<?php

namespace App\Controller\Api;

use App\Entity\Order;
use App\Entity\OrderStatus;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/orders', name: 'order_')]
final class OrderController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(EntityManagerInterface $em, SerializerInterface $serializer): JsonResponse
    {
        $data = $em->getRepository(Order::class)->findAll();

        return $this->json(
            $data,
        );
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(EntityManagerInterface $em, Order $order): JsonResponse
    {
        return $this->json(
            $order
        );
    }

    #[Route('', name: 'store', methods: ['POST'])]
    public function store(EntityManagerInterface $em, Request $request, SerializerInterface $serialize): JsonResponse
    {
        $content = $request->getContent();

        $data = json_decode($content, true);

        $pendingStatus = $em->getRepository(OrderStatus::class)->findOneBy(['name' => 'pending']);


        $order = $serialize->deserialize($content, Order::class, 'json');
        if ($pendingStatus) {
            $order->setStatus($pendingStatus);
        }

        $order->setCreatedAt(new \DateTimeImmutable());
        $order->setUpdatedAt(new \DateTimeImmutable());

        foreach ($order->getOrderItems() as $orderItem) {
            $em->persist($orderItem);
        }

        $em->persist($order);
        $em->flush();

        return $this->json($order);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(Order                  $order,
                           Request                $request,
                           EntityManagerInterface $em,
                           SerializerInterface    $serialize,
                           OrderRepository        $orderRepository): JsonResponse
    {

        $serialize->deserialize($request->getContent(), Order::class, 'json', ["object_to_populate" => $order]);
        $order->setUpdatedAt(new \DateTimeImmutable());
        $em->flush();
        return $this->json([
            'data' => $order,
        ]);
    }

    #[Route('/{id}', name: 'destroy', methods: ['DELETE'])]
    public function destroy(EntityManagerInterface $em, Order $order): JsonResponse
    {
        $em->remove($order);
        $em->flush();
        return $this->json([
        ]);
    }

    #[Route('/{id}/status', name: 'update_status', methods: ['PATCH'])]
    public function updateStatus(EntityManagerInterface $em,Order $order, Request $request, SerializerInterface $serialize): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!isset($data['statusId'])) {
            throw new \InvalidArgumentException('Status ID is missing from the request.');
        }

        $newStatus = $em->getRepository(OrderStatus::class)->find($data['statusId']);

        if (!$newStatus) {
            throw new \InvalidArgumentException('Invalid status ID provided.');
        }

        $order->setStatus($newStatus);
        $order->setUpdatedAt(new \DateTimeImmutable());

        return $this->json(
            $order
        );
    }
}
