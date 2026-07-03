<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\RunShop\migrations;

use Besnovatyj\Kernel\migration\BaseMigration;
use yii\base\NotSupportedException;

/** 'm<YYMMDD_HHMMSS>_<Name>' */
class m230518_141630_create_run_shop_related_asgmt_tables extends BaseMigration
{
  public const string TABLE_NAME = '{{%run_shop_related_asgmt}}';

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
                ->comment('Идентификатор продукта'),
            'related_id' => $this->integer(10)->notNull()
                ->comment('Идентификатор продукта'),
        ], $this->tableOptions);
        $this->addCommentOnTable(static::TABLE_NAME, 'Связанные товары');

        $this->createIndexes(static::TABLE_NAME, ['product_id', 'related_id'], true);
        $this->createIndexes(static::TABLE_NAME, 'product_id');
        $this->createIndexes(static::TABLE_NAME, 'related_id');

        parent::safeUp();
    }

    public function safeDown(): void
    {
        parent::safeDown();
    }
}
