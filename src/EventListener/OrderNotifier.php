<?php

namespace App\EventListener;

use App\Entity\Order;
use App\Message\EmailNotification;
use App\Message\EmailTypeEnum;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Messenger\MessageBusInterface;


#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: Order::class)]
#[AsEntityListener(event: Events::postUpdate, method: 'postUpdate', entity: Order::class)]
class OrderNotifier
{

    private MessageBusInterface $bus;

    public function __construct(MessageBusInterface $bus)
    {
        $this->bus = $bus;
    }
    public function postPersist(Order $order, PostPersistEventArgs $event): void
    {
        $this->bus->dispatch(new EmailNotification(
            $order->getId(),
            EmailTypeEnum::WELCOME
        ));
    }
    public function postUpdate(Order $order , PostUpdateEventArgs $event): void
    {
        $uow = $event->getObjectManager()->getUnitOfWork();
        $changes = $uow->getEntityChangeSet($order);

        if (isset($changes['status'])) {
            [$oldStatus, $newStatus] = $changes['status'];

            $newStatusName = $newStatus->getName();

            if (in_array($newStatusName, [
                EmailTypeEnum::DELIVERED->value,
                EmailTypeEnum::SHIPPED->value,
            ], true)) {
                $this->bus->dispatch(new EmailNotification(
                    $order->getId(),
                    EmailTypeEnum::from($newStatusName)
                ));
            }
        }

    }
}
