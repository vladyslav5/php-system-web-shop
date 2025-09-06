<?php

namespace App\Controller\Api;

use App\Dto\CreateOrderRequestDto;
use App\Dto\OrderItemDto;
use App\Dto\UpdateOrderRequestDto;
use App\Dto\ViewOrderDto;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\OrderStatus;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;


#[Route('/orders', name: 'order_')]
final class OrderController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(
        EntityManagerInterface                                                             $em,
        #[MapQueryParameter(filter: FILTER_VALIDATE_INT, options: ['min_range' => 1])] int $page = 1,
        #[MapQueryParameter(filter: FILTER_VALIDATE_INT, options: ['min_range' => 5])] int $limit = 10,
        #[MapQueryParameter] ?string                                                       $status = null,
        #[MapQueryParameter('date_from')] string                                           $dateFrom = null,
        #[MapQueryParameter('date_to')] string                                             $dateTo = null,
        #[MapQueryParameter(filter: FILTER_VALIDATE_EMAIL)] ?string                        $email = null,
    ): JsonResponse
    {
        $orders = $em->getRepository(Order::class)->findOrdersWithFilters($page, $limit, $status, $email, $dateFrom, $dateTo);
        return $this->json(
            $orders
        );
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(EntityManagerInterface $em, Order $order): JsonResponse
    {
        $view = ViewOrderDto::fromEntity($order);
        return $this->json($view);
    }


    #[Route('', name: 'store', methods: ['POST'])]
    public function store(EntityManagerInterface                                                      $em,
                          #[MapRequestPayload(validationFailedStatusCode: 400)] CreateOrderRequestDto $data,
                          ObjectMapperInterface                                                       $objectMapper,
                          SerializerInterface                                                         $serializer,
                          ValidatorInterface                                                          $validator
    ): JsonResponse
    {
        $pendingStatus = $em->getRepository(OrderStatus::class)->findOneBy(['name' => 'pending']);
        $order = new Order();
        $order->setTotalAmount($data->calculateTotal());

        $order->setUpdatedAt(new \DateTimeImmutable());
        $order->setCreatedAt(new \DateTimeImmutable());
        $order->setStatus($pendingStatus);

        $objectMapper->map($data, $order);
        $orderItems = [];

        foreach ($data->getOrderItems() as $itemData) {
            $itemDto = $serializer->denormalize($itemData, OrderItemDTO::class);
            $errors = $validator->validate($itemDto);
            if (count($errors) > 0) {
                throw new ValidationFailedException($itemDto, $errors);
            }
            $orderItems[] = $itemDto;
        }

        foreach ($orderItems as $itemDto) {
            $orderItem = $objectMapper->map($itemDto, OrderItem::class);
            $order->addOrderItem($orderItem);
            $em->persist($orderItem);
        }

        $em->persist($order);
        $em->flush();

        return $this->json($order);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(Order                                                                       $order,
                           #[MapRequestPayload(validationFailedStatusCode: 400)] UpdateOrderRequestDto $data,
                           EntityManagerInterface                                                      $em,
                           SerializerInterface                                                         $serializer,
                           ObjectMapperInterface                                                       $objectMapper,
                           ValidatorInterface                                                          $validator
    ): JsonResponse
    {
//        dd($order->getStatus()->getName());
        if (!in_array($order->getStatus()->getName(), ['pending', 'processing'])) {
            return $this->json(['error' => 'Cannot update order items for this order status'], 400);
        }
        $objectMapper->map($data, $order);
        $order->setUpdatedAt(new \DateTimeImmutable());


        foreach ($data->getOrderItems() as $itemOrder) {
            $item = $em->getRepository(OrderItem::class)->find($itemOrder['id']);
            if (!$item || $item->getOrder()->getId() !== $order->getId()) {
                continue;
            }
            $itemDto = $serializer->denormalize($itemOrder, OrderItemDTO::class);
            $errors = $validator->validate($itemDto);

            if (count($errors) > 0) {
                throw new ValidationFailedException($itemDto, $errors);
            }
//            $serializer->denormalize($itemOrder, OrderItem::class,"",['object_to_populate' => $item]);
            $objectMapper->map($itemDto, $item);
            $em->persist($item);
        }
        $order->setTotalAmount($data->calculateTotal());
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
    public function updateStatus(EntityManagerInterface $em, Order $order, Request $request, SerializerInterface $serialize): JsonResponse
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
        $em->flush();
        return $this->json(
            $order
        );
    }
}
