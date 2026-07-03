<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\RunShop\migrations;

use Besnovatyj\Kernel\migration\BaseMigration;
use yii\base\NotSupportedException;

/** 'm<YYMMDD_HHMMSS>_<Name>' */
class m230518_141680_create_run_shop_cart_items_tables extends BaseMigration
{
  public const string TABLE_NAME = '{{%run_shop_cart_items}}';

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
            'id' => $this->bigPrimaryKey(),
            'user_id' => $this->integer(10)->notNull()
                ->comment('Идентификатор пользователя'),
            'product_id' => $this->integer(10)->notNull()
                ->comment('Идентификатор продукта'),
            'modification_id' => $this->integer(10)->null()
                ->comment('Модификация продукта'),
            'quantity' => $this->integer(10)->notNull()
                ->comment('Количество единиц продукта'),
        ], $this->tableOptions);
        $this->addCommentOnTable(static::TABLE_NAME, 'Элементы корзины');

        $this->createIndexes(static::TABLE_NAME, 'user_id');
        $this->createIndexes(static::TABLE_NAME, 'product_id');

        parent::safeUp();
    }

    public function safeDown(): void
    {
        parent::safeDown();
    }

}
