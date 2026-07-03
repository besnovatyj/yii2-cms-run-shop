<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\RunShop\migrations;

use Besnovatyj\Kernel\migration\BaseMigration;
use yii\base\NotSupportedException;

/** 'm<YYMMDD_HHMMSS>_<Name>' */
class m230518_141690_create_run_shop_order_items_tables extends BaseMigration
{
  public const string TABLE_NAME = '{{%run_shop_order_items}}';

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
            'order_id' => $this->integer(10)->notNull()
                ->comment('Идентификатор заказа'),
            'product_id' => $this->integer(10)->notNull()
                ->comment('Идентификатор товара'),
            'product_name' => $this->string(255)->notNull()
                ->comment('Название товара'),
            'product_code' => $this->string(255)->notNull()
                ->comment('Код товара'),
            'price' => $this->integer(10)->notNull()
                ->comment('Стоимость товара'),
            'quantity' => $this->integer(10)->notNull()
                ->comment('Количество единиц товара'),
            'modification_id' => $this->integer(10)->null()
                ->comment('Идентификатор модификации товара'),
            'modification_name' => $this->string(255)->null()
                ->comment('Название модификации товара'),
            'modification_code' => $this->string(255)->null()
                ->comment('Код модификации товара'),

        ], $this->tableOptions);
        $this->addCommentOnTable(static::TABLE_NAME, 'Элемент заказа');

        $this->createIndexes(static::TABLE_NAME, 'order_id');
        $this->createIndexes(static::TABLE_NAME, 'product_id');

        parent::safeUp();
    }

    public function safeDown(): void
    {
        parent::safeDown();
    }

}
