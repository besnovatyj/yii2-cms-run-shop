<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\RunShop\migrations;

use Besnovatyj\Kernel\migration\BaseMigration;

/** 'm<YYMMDD_HHMMSS>_<Name>' */
class m170529_120617_create_run_shop_delivery_methods_table extends BaseMigration
{
  public const string TABLE_NAME = '{{%run_shop_delivery_methods}}';

    public function safeUp(): void
    {
        parent::safeUp();

        if ($this->existTable(static::TABLE_NAME)) {
            return;
        }

        $this->createTable(static::TABLE_NAME, [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull()
                ->comment('Название метода доставки'),
            'cost' => $this->integer(10)->notNull()
                ->comment('Стоимость доставки'),
            'min_weight' => $this->integer(10)->null()
                ->comment('Минимальный вес для данного типа доставки'),
            'max_weight' => $this->integer(10)->null()
                ->comment('Максимальный вес для данного типа доставки'),
            'sort' => $this->integer(10)->notNull()
                ->comment('Сортировка методов доставки'),
        ], $this->tableOptions);
        $this->addCommentOnTable(static::TABLE_NAME, 'Методы доставки');

        parent::safeUp();

    }

    public function safeDown(): void
    {
        parent::safeDown();
    }
}
