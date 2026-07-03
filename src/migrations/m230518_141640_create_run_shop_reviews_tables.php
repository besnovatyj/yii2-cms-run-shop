<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\RunShop\migrations;

use Besnovatyj\Kernel\migration\BaseMigration;
use yii\base\NotSupportedException;

/** 'm<YYMMDD_HHMMSS>_<Name>' */
class m230518_141640_create_run_shop_reviews_tables extends BaseMigration
{
  public const string TABLE_NAME = '{{%run_shop_reviews}}';

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
            'created_at' => $this->integer(10)->null()
                ->comment('Дата и время создания отзыва'),
            'user_id' => $this->integer(10)->null()
                ->comment('Идентификатор пользователя оставившего отзыв'),
            'product_id' => $this->integer(10)->null()
                ->comment('Идентификатор товара на который оставлен отзыв'),
            'vote' => $this->tinyInteger(3)->notNull()->defaultValue(0)
                ->comment('Оценка товара'),
            'text' => $this->text()->notNull()
                ->comment('Текст отзыва'),
            'active' => $this->tinyInteger(3)->notNull()->defaultValue(0)
                ->comment('Активность отзыва'),
        ], $this->tableOptions);
        $this->addCommentOnTable(static::TABLE_NAME, 'Отзывы');

        $this->createIndexes(static::TABLE_NAME, 'user_id');

        parent::safeUp();
    }

    public function safeDown(): void
    {
        parent::safeDown();
    }

}
