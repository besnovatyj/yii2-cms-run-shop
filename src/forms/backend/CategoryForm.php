<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\RunShop\forms\backend;

use Besnovatyj\Meta\MetaForm;
use Besnovatyj\RunShop\entities\category\Category;
use Besnovatyj\TreeManager\Manager\forms\TreeNodeFormInterface;
use yii\base\Model;
use yii\helpers\Inflector;

/**
 * Форма создания/редактирования категории товаров (дерево, TreeManager).
 *
 * ПРИМЕЧАНИЕ: загрузка фото категории в этой форме не проводится — generic TreeController не
 * обрабатывает multipart. Колонка `photo` и поддержка в сущности сохранены для будущего отдельного UI.
 *
 * @property MetaForm $meta
 */
class CategoryForm extends Model implements TreeNodeFormInterface
{
    // Поля, требуемые TreeNodeFormInterface (заполняются TreeController)
    public int|string|null $nodeId   = null;
    public int|string|null $parentId = null;
    public int|string      $status   = 0;

    public string  $name        = '';
    public string  $slug        = '';
    public ?string $description = null;

    public MetaForm $meta;

    public function __construct(?Category $category = null, $config = [])
    {
        if ($category) {
            $this->name        = $category->name;
            $this->slug        = $category->slug;
            $this->description = $category->description;
            $this->status      = $category->status;
            $this->meta        = new MetaForm($category->meta);
        } else {
            $this->meta = new MetaForm();
        }
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            [['name'], 'required'],
            [['name', 'slug'], 'string', 'max' => 255],
            ['description', 'string'],
            ['status', 'integer'],
            ['nodeId', 'safe'],
            ['parentId', 'safe'],
            [
                'slug',
                'default',
                'value' => fn() => Inflector::slug($this->name),
            ],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'name'        => 'Название',
            'slug'        => 'Slug',
            'description' => 'Описание',
            'status'      => 'Статус',
        ];
    }
}
