<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\RunShop\forms\backend;

use yii\base\Model;
use yii\web\UploadedFile;

/**
 * Форма загрузки одиночного изображения категории.
 *
 * Дерево категорий редактируется TreeManager'ом (структура/имя/slug/meta); изображение — отдельно
 * через это форму + {@see \Besnovatyj\RunShop\controllers\backend\CategoryImageController}, т.к.
 * generic TreeController не проводит multipart.
 */
class CategoryPhotoForm extends Model
{
    /** @var UploadedFile|null */
    public $photo;

    public function rules(): array
    {
        return [
            ['photo', 'image'],
        ];
    }

    public function beforeValidate(): bool
    {
        if (!parent::beforeValidate()) {
            return false;
        }
        $this->photo = UploadedFile::getInstance($this, 'photo');
        return true;
    }

    public function attributeLabels(): array
    {
        return [
            'photo' => 'Изображение категории',
        ];
    }
}
