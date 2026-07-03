<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\RunShop\migrations;

use Besnovatyj\Kernel\migration\BaseMigration;

/** 'm<YYMMDD_HHMMSS>_<Name>' */
class m230518_141670_create_run_shop_discounts_tables extends BaseMigration
{
  public const string TABLE_NAME = '{{%run_shop_discounts}}';

    public function safeUp(): void
    {
        parent::safeUp();

        if ($this->existTable(static::TABLE_NAME)) {
            return;
        }

        $this->createTable(static::TABLE_NAME, [
            'id' => $this->primaryKey(),
            'percent' => $this->integer(10)->notNull()
                ->comment('Процент скидки'),
            'name' => $this->string(255)->notNull()
                ->comment('Название скидки'),
            'from_date' => $this->timestamp()->notNull()
                ->comment('Дата начала действия скидки'),
            'to_date' => $this->timestamp()->notNull()
                ->comment('Дата окончания действия скидки'),
            'active' => $this->tinyInteger(3)->notNull()
                ->comment('Активность скидки'),
            'sort' => $this->tinyInteger(3)->notNull()
                ->comment('Сортировка скидок'),
        ], $this->tableOptions);
        $this->addCommentOnTable(static::TABLE_NAME, 'Скидки');

        parent::safeUp();
    }

    public function safeDown(): void
    {
        parent::safeDown();
    }

}
