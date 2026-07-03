<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\RunShop\migrations;

use Besnovatyj\Kernel\migration\BaseMigration;
use yii\base\NotSupportedException;

/** 'm<YYMMDD_HHMMSS>_<Name>' */
class m230518_141650_create_run_shop_tags_tables extends BaseMigration
{
  public const string TABLE_NAME = '{{%run_shop_tags}}';

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
            'name' => $this->string(255)->notNull()
                ->comment('Название тега'),
            'slug' => $this->string(255)->notNull()
                ->comment('Slug тега'),
        ], $this->tableOptions);
        $this->addCommentOnTable(static::TABLE_NAME, 'Теги товаров');

        $this->createIndexes(static::TABLE_NAME, 'slug');

        parent::safeUp();
    }

    public function safeDown(): void
    {
        parent::safeDown();
    }
}
