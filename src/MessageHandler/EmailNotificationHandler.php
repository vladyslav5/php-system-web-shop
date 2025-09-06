<?php

namespace App\MessageHandler;

use App\Message\EmailNotification;
use App\Message\EmailTypeEnum;
use App\Repository\OrderRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Email;

#[AsMessageHandler]
class EmailNotificationHandler
{
    private MailerInterface $mailer;
    private OrderRepository $orderRepository;
    private LoggerInterface $logger;


    public function __construct(MailerInterface $mailer, OrderRepository $orderRepository, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->mailer = $mailer;
        $this->orderRepository = $orderRepository;
    }

    public function __invoke(EmailNotification $message)
    {

        $order = $this->orderRepository->find($message->getOrderId());
        if (!$order) {
            $this->logger->warning("Order not found: {$message->getOrderId()}");
            return;
        }
        $email = (new Email())
            ->from('shop@example.com')
            ->to($order->getCustomerEmail());

        switch ($message->getType()) {
            case EmailTypeEnum::WELCOME:
                $email->subject('Welcome!')->text('Thank you for your order! Order id - '.$order->getId());
                break;
            case EmailTypeEnum::SHIPPED:
                $email->subject('Your order'.$order->getId().' has been shipped')->text('Your order is on the way!');
                break;
            case EmailTypeEnum::DELIVERED:
                $email->subject('Order delivered')->text('Thank you for shopping with us!');
                break;
        }
        $this->logger->info("Email sent: {$email->getTextBody()}");
        $this->mailer->send($email);

    }
}
