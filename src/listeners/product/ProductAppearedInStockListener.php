<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\RunShop\listeners\product;

use Besnovatyj\RunShop\entities\product\events\ProductAppearedInStock;
use Besnovatyj\RunShop\entities\product\Product;
use Besnovatyj\User\entities\User;
use Besnovatyj\User\repositories\UserRepository;
use yii\base\ErrorHandler;
use yii\mail\MailerInterface;

class ProductAppearedInStockListener
{
    private $users;
    private $mailer;
    private $errorHandler;

    public function __construct(UserRepository $users, MailerInterface $mailer, ErrorHandler $errorHandler)
    {
        $this->users = $users;
        $this->mailer = $mailer;
        $this->errorHandler = $errorHandler;
    }

    public function handle(ProductAppearedInStock $event): void
    {
        if ($event->product->isActive()) {
            foreach ($this->users->getAllByProductInWishList($event->product->id) as $user) {
                if ($user->isActive()) {
                    try {
                        $this->sendEmailNotification($user, $event->product);
                    } catch (\Exception $e) {
                        $this->errorHandler->handleException($e);
                    }
                }
            }
        }
    }

    private function sendEmailNotification(User $user, Product $product): void
    {
        $sent = $this->mailer
            ->compose(
                ['html' => '@Besnovatyj/RunShop/views/mail/available-html', 'text' => '@Besnovatyj/RunShop/views/mail/available-text'],
                ['user' => $user, 'product' => $product]
            )
            ->setTo($user->email)
            ->setSubject('Product is available')
            ->send();
        if (!$sent) {
            throw new \RuntimeException('Email sending error to ' . $user->email);
        }
    }
}
