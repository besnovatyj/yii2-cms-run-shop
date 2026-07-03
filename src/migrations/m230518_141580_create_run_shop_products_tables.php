<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\RunShop\migrations;

use Besnovatyj\Kernel\migration\BaseMigration;
use yii\base\NotSupportedException;

/** 'm<YYMMDD_HHMMSS>_<Name>' */
class m230518_141580_create_run_shop_products_tables extends BaseMigration
{
  public const string TABLE_NAME = '{{%run_shop_products}}';

    /**
     * @throws NotSupportedException
     */
    public function safeUp(): void
    {
        parent::safeUp();

        if ($this->existTable(static::TABLE_NAME)) {
            return;
        }

        $this->createTable(static::TABLE_NAME, [
            'id' => $this->primaryKey(),
            'category_id' => $this->integer(10)->notNull()
                ->comment('Идентификатор категории товара'),
            'brand_id' => $this->integer(10)->null()
                ->comment('Идентификатор бренда товара'),
            'created_at' => $this->timestamp()->null()->defaultExpression('CURRENT_TIMESTAMP')
                ->comment('Дата и время создания товара'),
            'code' => $this->string(255)->null()
                ->comment('Код товара'),
            'name' => $this->string(255)->notNull()
                ->comment('Название товара'),
            'description' => $this->text()->null()
                ->comment('Описание товара'),
            'price_old' => $this->integer(10)->null()
                ->comment('Старая цена'),
            'price_new' => $this->integer(10)->null()
                ->comment('Новая цена'),
            'rating' => $this->decimal(3, 2)->null()
                ->comment('Рейтинг товара'),
            'meta_json' => $this->text()->null()
                ->comment('JSON meta'),
            'main_photo_id' => $this->integer(10)->null()
                ->comment('Идентификатор главной фотографии товара'),
            'status' => $this->smallInteger(5)
                ->comment('Статус товара'),
            'email_additional_text_html' => $this->text()->null()
                ->comment('Текст добавляемый к письму, при оформлении заказа (HTML)'),
            'email_additional_text_plain_text' => $this->text()->null()
                ->comment('Текст добавляемый к письму, при оформлении заказа (Plain text)'),
            'mail_attach' => $this->text()->null()
                ->comment('Путь к файлу вложения для письма об оплате заказа (допустимы алиасы Yii2)'),
            'weight' => $this->integer(10)->null()
                ->comment('Масса единицы товара'),
            'quantity' => $this->integer(10)->null()
                ->comment('Количество оставшегося товара'),
        ], $this->tableOptions);
        $this->addCommentOnTable(static::TABLE_NAME, 'Товары');

        $this->createIndexes(static::TABLE_NAME, 'code');
        $this->createIndexes(static::TABLE_NAME, 'category_id');
        $this->createIndexes(static::TABLE_NAME, 'brand_id');
        $this->createIndexes(static::TABLE_NAME, 'main_photo_id');

        parent::safeUp();
    }

    public function safeDown(): void
    {
        parent::safeDown();
    }
}
