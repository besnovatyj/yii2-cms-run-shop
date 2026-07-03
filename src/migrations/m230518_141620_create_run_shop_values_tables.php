<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\RunShop\migrations;

use Besnovatyj\Kernel\migration\BaseMigration;
use yii\base\NotSupportedException;

/** 'm<YYMMDD_HHMMSS>_<Name>' */
class m230518_141620_create_run_shop_values_tables extends BaseMigration
{
  public const string TABLE_NAME = '{{%run_shop_values}}';

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
            'product_id' => $this->integer(10)->notNull()
                ->comment('Идентификатор товара'),
            'characteristic_id' => $this->integer(10)->notNull()
                ->comment('Идентификатор характеристики'),
            'value' => $this->text()->null()
                ->comment('Значение характеристики'),
        ], $this->tableOptions);
        $this->addCommentOnTable(static::TABLE_NAME, 'Значение хар-ки EAV');

        $this->createIndexes(static::TABLE_NAME, 'product_id');
        $this->createIndexes(static::TABLE_NAME, 'characteristic_id');

        parent::safeUp();
    }

    public function safeDown(): void
    {
        parent::safeDown();
    }

}
