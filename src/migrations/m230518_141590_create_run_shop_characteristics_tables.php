<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\RunShop\migrations;

use Besnovatyj\Kernel\migration\BaseMigration;

/** 'm<YYMMDD_HHMMSS>_<Name>' */
class m230518_141590_create_run_shop_characteristics_tables extends BaseMigration
{
  public const string TABLE_NAME = '{{%run_shop_characteristics}}';

    public function safeUp(): void
    {
        parent::safeUp();

        if ($this->existTable(static::TABLE_NAME)) {
            return;
        }

        $this->createTable(static::TABLE_NAME, [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull()
                ->comment('Название характеристики'),
            'type' => $this->string(16)->notNull()
                ->comment('Тип характеристики'),
            'required' => $this->tinyInteger(1)->notNull()
                ->comment('Обязательность заполнения'),
            'default' => $this->string(255)->null()
                ->comment('Значение по умолчанию'),
            'variants_json' => $this->text()->notNull()
                ->comment('Варианты для выбора'),
            'sort' => $this->integer(10)->notNull()
                ->comment('Сортировка характеристик'),
        ], $this->tableOptions);
        $this->addCommentOnTable(static::TABLE_NAME, 'Характеристики товаров EAV');

        parent::safeUp();

    }

    public function safeDown(): void
    {
        parent::safeDown();
    }

}
