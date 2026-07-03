<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\RunShop\image;

use Besnovatyj\Images\contracts\ImageOwnerInterface;
use Besnovatyj\RunShop\entities\product\Photo;
use Besnovatyj\RunShop\entities\product\Product;
use Besnovatyj\RunShop\repositories\ProductRepository;
use yii\db\Exception;

/**
 * Адаптер Product к ImageOwnerInterface (управление фото товара через модуль yii2-cms-images).
 *
 * Pessimistic lock через PessimisticLockBehavior Product исключает race condition
 * при параллельной загрузке фотографий.
 */
readonly class ProductImageOwner implements ImageOwnerInterface
{
    public function __construct(
        private Product           $product,
        private ProductRepository $repository,
    ) {}

    public function getOwnerId(): int
    {
        return $this->product->id;
    }

    /**
     * @return Photo[]
     */
    public function getOwnedImages(): array
    {
        return $this->product->photos;
    }

    public function getMainImageId(): ?int
    {
        return $this->product->main_photo_id ?: null;
    }

    public function setMainImageId(?int $imageId): void
    {
        $this->product->setMainPhoto($imageId);
    }

    /**
     * @throws Exception
     */
    public function saveOwner(): void
    {
        $this->repository->save($this->product);
    }

    /**
     * @throws Exception
     */
    public function lockOwner(): void
    {
        $this->product->lock();
    }

    public function refreshOwner(): void
    {
        $this->product->refresh();
    }
}
