<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\RunShop\migrations;

use Besnovatyj\Kernel\migration\BaseMigration;
use Yii;

class m230518_141720_create_run_shop_foreign_key_constraints extends BaseMigration
{

    public function safeUp(): void
    {
        parent::safeUp();

        Yii::$app->getDb()->createCommand("SET foreign_key_checks = 0")->execute();

        // Фотографии
        $this->createFKs(m230518_141570_create_run_shop_photos_tables::TABLE_NAME, 'product_id', m230518_141580_create_run_shop_products_tables::TABLE_NAME, 'id', 'CASCADE');

        // Товары
        $this->createFKs(m230518_141580_create_run_shop_products_tables::TABLE_NAME, 'category_id', m230518_141560_create_run_shop_categories_tables::TABLE_NAME, 'id');
        $this->createFKs(m230518_141580_create_run_shop_products_tables::TABLE_NAME, 'brand_id', m230518_141550_create_run_shop_brands_table::TABLE_NAME, 'id');
        $this->createFKs(m230518_141580_create_run_shop_products_tables::TABLE_NAME, 'main_photo_id', m230518_141570_create_run_shop_photos_tables::TABLE_NAME, 'id','SET NULL');

        // Связь товаров и категорий
        $this->createFKs(m230518_141600_create_run_shop_cat_prod_asgmt_tables::TABLE_NAME, 'product_id', m230518_141580_create_run_shop_products_tables::TABLE_NAME, 'id','CASCADE');
        $this->createFKs(m230518_141600_create_run_shop_cat_prod_asgmt_tables::TABLE_NAME, 'category_id', m230518_141560_create_run_shop_categories_tables::TABLE_NAME, 'id','CASCADE');

        // Связь товаров и характеристик
        $this->createFKs(m230518_141610_create_run_shop_char_prod_asgmt_tables::TABLE_NAME, 'characteristic_id', m230518_141590_create_run_shop_characteristics_tables::TABLE_NAME, 'id', 'CASCADE');
        $this->createFKs(m230518_141610_create_run_shop_char_prod_asgmt_tables::TABLE_NAME, 'product_id', m230518_141580_create_run_shop_products_tables::TABLE_NAME, 'id', 'CASCADE');

        // Значения характеристик
        $this->createFKs(m230518_141620_create_run_shop_values_tables::TABLE_NAME, 'product_id', m230518_141580_create_run_shop_products_tables::TABLE_NAME, 'id','CASCADE');
        $this->createFKs(m230518_141620_create_run_shop_values_tables::TABLE_NAME, 'characteristic_id', m230518_141590_create_run_shop_characteristics_tables::TABLE_NAME, 'id','CASCADE');

        // Связь похожих продуктов
        $this->createFKs(m230518_141630_create_run_shop_related_asgmt_tables::TABLE_NAME, 'product_id', m230518_141580_create_run_shop_products_tables::TABLE_NAME, 'id','CASCADE');
        $this->createFKs(m230518_141630_create_run_shop_related_asgmt_tables::TABLE_NAME, 'related_id', m230518_141580_create_run_shop_products_tables::TABLE_NAME, 'id','CASCADE');

        // Отзывы
        $this->createFKs(m230518_141640_create_run_shop_reviews_tables::TABLE_NAME, 'user_id', '{{%users}}', 'id', 'SET NULL', 'CASCADE');
        $this->createFKs(m230518_141640_create_run_shop_reviews_tables::TABLE_NAME, 'product_id', m230518_141580_create_run_shop_products_tables::TABLE_NAME, 'id', 'SET NULL', 'CASCADE');

        // Связь с тегами
        $this->createFKs(m230518_141660_create_run_shop_tag_asgmt_tables::TABLE_NAME, 'product_id', m230518_141580_create_run_shop_products_tables::TABLE_NAME, 'id','CASCADE');
        $this->createFKs(m230518_141660_create_run_shop_tag_asgmt_tables::TABLE_NAME, 'tag_id', m230518_141650_create_run_shop_tags_tables::TABLE_NAME, 'id','CASCADE');

        // Корзина
        $this->createFKs(m230518_141680_create_run_shop_cart_items_tables::TABLE_NAME, 'product_id', m230518_141580_create_run_shop_products_tables::TABLE_NAME, 'id','CASCADE');
        $this->createFKs(m230518_141680_create_run_shop_cart_items_tables::TABLE_NAME, 'user_id', '{{%users}}', 'id','CASCADE');

        // Позиции заказа
        $this->createFKs(m230518_141690_create_run_shop_order_items_tables::TABLE_NAME, 'order_id', m230518_141700_create_run_shop_orders_tables::TABLE_NAME, 'id','CASCADE');
        // TODO При удалении продукта позиция в заказе с его идентификатором останется!!!
        $this->createFKs(m230518_141690_create_run_shop_order_items_tables::TABLE_NAME, 'product_id', m230518_141580_create_run_shop_products_tables::TABLE_NAME, 'id');

        // Заказы
        $this->createFKs(m230518_141700_create_run_shop_orders_tables::TABLE_NAME, 'user_id', '{{%users}}', 'id', 'CASCADE');

        Yii::$app->db->createCommand('SET foreign_key_checks = 1')->execute();

    }

    public function safeDown(): void
    {
        // Отменяем действия по умолчанию,
        // так как \Besnovatyj\Kernel\migration\BaseMigration::safeDown() вызывает static::TABLE_NAME,
        // которого в данной миграции не существует.
        // Так же, \Besnovatyj\Kernel\migration\BaseMigration::safeDown() при удалении таблиц сам удалит у них все индексы и внешние ключи.

        // parent::safeDown();
    }

}
