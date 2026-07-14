<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\RunShop;

use Besnovatyj\DomainEvents\dispatchers\SimpleEventDispatcher;
use Besnovatyj\RunShop\entities\category\Category;
use Besnovatyj\RunShop\entities\product\events\ProductAppearedInStock;
use Besnovatyj\RunShop\listeners\category\CategoryPersistenceListener;
use Besnovatyj\RunShop\listeners\order\OrderCanceledListener;
use Besnovatyj\RunShop\listeners\order\OrderCreatedListener;
use Besnovatyj\RunShop\listeners\order\OrderPaidListener;
use Besnovatyj\RunShop\listeners\product\ProductAppearedInStockListener;
use Besnovatyj\RunShop\repositories\events\CategoryPersisted;
use Besnovatyj\RunShop\repositories\events\OrderCanceled;
use Besnovatyj\RunShop\repositories\events\OrderCreated;
use Besnovatyj\RunShop\repositories\events\OrderPaid;
use Yii;
use yii\base\BootstrapInterface;
use yii\base\Event;
use yii\db\ActiveRecord;

/**
 * Bootstrap модуля RunShop.
 *
 * Подписывает слушателей на доменные события. События отложенные (DeferredEventDispatcher → очередь),
 * поэтому письма о заказе/наличии шлются в очередь-воркере, а не в транзакции оформления.
 * Почтовые шаблоны лежат в `src/views/mail` и доступны по alias `@Besnovatyj/RunShop/views/mail/...`
 * (alias регистрируется фреймворком из PSR-4 автозагрузки пакета). Расположение под `views/`
 * НЕ случайно: так письма попадают под ту же карту представлений, что и обычные view, и темизуются
 * оверлеем `@themes/{theme}/modules/RunShop/views/mail/...` (см. ANALYSIS_MODULES_INTEGRATION.MD, §6).
 *
 * НЕ подключены поисковые слушатели (`ProductSearchPersist/RemoveListener`) — Elasticsearch-индексатор
 * (`services\search\ProductIndexer`) в этой сборке не используется (резерв).
 */
class Bootstrap implements BootstrapInterface
{
    /**
     * {@inheritdoc}
     */
    public function bootstrap($app): void
    {
        // Alias `@Besnovatyj/RunShop` регистрируется фреймворком (yii2-composer) из PSR-4 автозагрузки —
        // почтовые шаблоны доступны как `@Besnovatyj/RunShop/views/mail/...`.

        /** @var SimpleEventDispatcher $dispatcher */
        $dispatcher = Yii::$container->get(SimpleEventDispatcher::class);

        // Категории (инвалидация кеша) — управляются через TreeManager (AR).
        $dispatcher->listen(CategoryPersisted::class, CategoryPersistenceListener::class);

        // Заказы (письма покупателю и админу).
        $dispatcher->listen(OrderCreated::class, OrderCreatedListener::class);
        $dispatcher->listen(OrderPaid::class, OrderPaidListener::class);
        $dispatcher->listen(OrderCanceled::class, OrderCanceledListener::class);

        // Появление товара в наличии (уведомление подписчиков вишлиста).
        $dispatcher->listen(ProductAppearedInStock::class, ProductAppearedInStockListener::class);

        // Дерево категорий пишется через AR — диспетчеризуем CategoryPersisted на AR-события категории.
        Event::on(Category::class, ActiveRecord::EVENT_AFTER_INSERT, function ($event) use ($dispatcher): void {
            $dispatcher->dispatch(new CategoryPersisted($event->sender));
        });
        Event::on(Category::class, ActiveRecord::EVENT_AFTER_UPDATE, function ($event) use ($dispatcher): void {
            $dispatcher->dispatch(new CategoryPersisted($event->sender));
        });
    }
}
