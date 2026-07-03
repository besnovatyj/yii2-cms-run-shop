<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\RunShop\services;

use Besnovatyj\RunShop\cart\Cart;
use Besnovatyj\RunShop\cart\CartItem;
use Besnovatyj\RunShop\repositories\ProductRepository;

class CartService
{
    private $cart;
    private $products;

    public function __construct(Cart $cart, ProductRepository $products)
    {
        $this->cart = $cart;
        $this->products = $products;
    }

    public function getCart(): Cart
    {
        return $this->cart;
    }

    public function add(int $productId, $modificationId, int $quantity): void
    {
        $product = $this->products->get($productId);
        $modId = $modificationId ? $product->getModification($modificationId)->id : null;
        $this->cart->add(new CartItem($product, $modId, $quantity));
    }

    public function set(string $id, int $quantity): void
    {
        $this->cart->set($id, $quantity);
    }

    public function remove(string $id): void
    {
        $this->cart->remove($id);
    }


    public function changeQuantity(mixed $quantity_data)
    {
        foreach ($quantity_data as $id => $quantity) {
            $this->set($id, $quantity);
        }
    }

    public function clear(): void
    {
        $this->cart->clear();
    }
}
