<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\RunShop\migrations;

use Besnovatyj\Kernel\migration\BaseMigration;

/** 'm<YYMMDD_HHMMSS>_<Name>' */
class m230518_141710_create_run_shop_modifications_table extends BaseMigration
{
  public const string TABLE_NAME = '{{%run_shop_modifications}}';

    public function safeUp(): void
    {
        parent::safeUp();

        if ($this->existTable(static::TABLE_NAME)) {
            return;
        }

        $this->createTable(static::TABLE_NAME, [
            'id' => $this->primaryKey(),
            'product_id' => $this->integer(10)->notNull()
                ->comment('Идентификатор связанного товара'),
            'code' => $this->string(255)->notNull()
                ->comment('Код данной модификации товара'),
            'name' => $this->string(255)->notNull()
                ->comment('Название конкретной модификации'),
            'price' => $this->integer(10)->null()
                ->comment('Стоимость конкретной модификации'),
            'quantity' => $this->integer(10)->notNull()
                ->comment('Количество единиц данной модификации товара'),
        ], $this->tableOptions);
        $this->addCommentOnTable(static::TABLE_NAME, 'Модификации');

        parent::safeUp();
    }

    public function safeDown(): void
    {
        parent::safeDown();
    }
}
