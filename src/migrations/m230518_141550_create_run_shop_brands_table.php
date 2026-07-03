<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\RunShop\migrations;

use Besnovatyj\Kernel\migration\BaseMigration;
use yii\base\NotSupportedException;

/** 'm<YYMMDD_HHMMSS>_<Name>' */
class m230518_141550_create_run_shop_brands_table extends BaseMigration
{
  public const string TABLE_NAME = '{{%run_shop_brands}}';

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
            'id' => $this->primaryKey(10),
            'name' => $this->string(255)->notNull()
                ->comment('Название бренда'),
            'slug' => $this->string(255)->notNull()
                ->comment('Slug бренда'),
            'meta_json' => $this->text()
                ->comment('JSON of meta-obj'),
        ], $this->tableOptions);
        $this->addCommentOnTable(static::TABLE_NAME, 'Бренды');

        $this->createIndexes(static::TABLE_NAME, 'slug');

    }

    public function safeDown(): void
    {
        parent::safeDown();
    }

}
