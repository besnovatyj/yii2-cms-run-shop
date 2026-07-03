<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\RunShop;

use Besnovatyj\DomainEvents\dispatchers\SimpleEventDispatcher;
use Besnovatyj\RunShop\entities\category\Category;
use Besnovatyj\RunShop\listeners\category\CategoryPersistenceListener;
use Besnovatyj\RunShop\repositories\events\EntityPersisted;
use Yii;
use yii\base\BootstrapInterface;
use yii\base\Event;
use yii\db\ActiveRecord;

/**
 * Bootstrap модуля RunShop.
 *
 * Регистрирует слушателей событий при старте приложения. Инвалидация кеша категорий выполняется
 * через AR-события, т.к. категории управляются через TreeManager (а не CategoryRepository).
 *
 * ПРИМЕЧАНИЕ: слушатели order/search/stock (`listeners/order/*`, `listeners/product/ProductSearch*`,
 * `ProductAppearedInStockListener`) НЕ регистрируются — их инфраструктура пока отсутствует в CMS:
 * поисковый `services\search\ProductIndexer` (Elasticsearch) не портирован, а order/stock-письма
 * ссылаются на несуществующие mail-шаблоны. Подключить после портирования почтовых шаблонов/индексатора.
 */
class Bootstrap implements BootstrapInterface
{
    /**
     * {@inheritdoc}
     */
    public function bootstrap($app): void
    {
        /** @var SimpleEventDispatcher $dispatcher */
        $dispatcher = Yii::$container->get(SimpleEventDispatcher::class);
        $dispatcher->listen(EntityPersisted::class, CategoryPersistenceListener::class);

        // Дерево категорий пишется через AR (TreeManager/NestedSets), поэтому диспетчеризуем
        // EntityPersisted напрямую на AR-события сохранения категории — для инвалидации кеша.
        Event::on(Category::class, ActiveRecord::EVENT_AFTER_INSERT, function ($event) use ($dispatcher): void {
            $dispatcher->dispatch(new EntityPersisted($event->sender));
        });
        Event::on(Category::class, ActiveRecord::EVENT_AFTER_UPDATE, function ($event) use ($dispatcher): void {
            $dispatcher->dispatch(new EntityPersisted($event->sender));
        });
    }
}
