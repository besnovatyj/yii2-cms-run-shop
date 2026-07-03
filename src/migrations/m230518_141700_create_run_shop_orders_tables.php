<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\RunShop\migrations;

use Besnovatyj\Kernel\migration\BaseMigration;
use yii\base\NotSupportedException;

/** 'm<YYMMDD_HHMMSS>_<Name>' */
class m230518_141700_create_run_shop_orders_tables extends BaseMigration
{
  public const string TABLE_NAME = '{{%run_shop_orders}}';

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
            'created_at' => $this->timestamp()->null()->defaultExpression('CURRENT_TIMESTAMP')
                ->comment('Время создания заказа'),
            'idempotenceKey' => $this->string(64)->notNull() // 64 - ограничение ЮКассы, это заголовок
            ->comment('Ключ идемпотентности для сервиса оплаты'),
            'merchantOrderId' => $this->string(255)->null()
                ->comment('Идентификатор заказа в системе приёма платежей'),
            'user_id' => $this->integer(10)->null()
                ->comment('Идентификатор пользователя'),
            'payment_method' => $this->string(255)->null()
                ->comment('Тип оплаты'),
            'cost' => $this->integer(10)->notNull()
                ->comment('Стоимость заказа'),
            'note' => $this->text()->null()
                ->comment('Заметка о заказе для админа'),
            'current_status' => $this->string(255)->notNull()
                ->comment('Текущий статус'),
            'cancel_reason' => $this->text()->null()
                ->comment('Причина отмены заказа'),
            'statuses_json' => $this->text()->notNull()
                ->comment('История статусов'),
            'customer_firstName' => $this->string(255)->notNull()
                ->comment('Имя пользователя'),
            'customer_lastName' => $this->string(255)->null()
                ->comment('Фамилия пользователя'),
            'customer_phone' => $this->string(255)->notNull()
                ->comment('Телефон пользователя'),
            'customer_email' => $this->string(255)->null()
                ->comment('E-mail пользователя'),
        ], $this->tableOptions);
        $this->addCommentOnTable(static::TABLE_NAME, 'Заказы');

        $this->createIndexes(static::TABLE_NAME, 'user_id');

        parent::safeUp();
    }

    public function safeDown(): void
    {
        parent::safeDown();
    }

}
