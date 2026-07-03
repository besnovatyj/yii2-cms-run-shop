<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\RunShop\migrations;

use Besnovatyj\Kernel\migration\BaseMigration;
use yii\base\NotSupportedException;

/** 'm<YYMMDD_HHMMSS>_<Name>' */
class m230518_141610_create_run_shop_char_prod_asgmt_tables extends BaseMigration
{
  public const string TABLE_NAME = '{{%run_shop_char_prod_asgmt}}';

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
            'product_id' => $this->integer(10)->notNull()
                ->comment('Идентификатор товара'),
            'characteristic_id' => $this->integer(10)->notNull()
                ->comment('Идентификатор характеристики товара '),
        ], $this->tableOptions);
        $this->addCommentOnTable(static::TABLE_NAME, 'Связь хар-к с товарами EAV');

        $this->createIndexes(static::TABLE_NAME, 'product_id');
        $this->createIndexes(static::TABLE_NAME, 'characteristic_id');

        parent::safeUp();
    }

    public function safeDown(): void
    {
        parent::safeDown();
    }

}
