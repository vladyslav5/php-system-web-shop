<?php

namespace App\MessageHandler;

use App\Message\EmailNotification;
use App\Repository\OrderRepository;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Email;

#[AsMessageHandler]
class EmailNotificationHandler
{
    private MailerInterface $mailer;
    private OrderRepository $orderRepository;

    public function __construct(MailerInterface $mailer,OrderRepository $orderRepository)
    {
        $this->mailer = $mailer;
        $this->orderRepository = $orderRepository;
    }

    public function __invoke(EmailNotification $message)
    {

        $order = $this->orderRepository->find($message->getOrderId());
        $email = (new Email())
            ->from('shop@example.com')
            ->to($order->getCustomerEmail())
            ->subject('Hello')
            ->text('texta');

        $this->mailer->send($email);

    }
}
