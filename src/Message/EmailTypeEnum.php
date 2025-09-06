<?php
namespace App\Message;
enum EmailTypeEnum: string
{
    case WELCOME = 'welcome';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';
}
