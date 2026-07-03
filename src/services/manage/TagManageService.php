<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\RunShop\services\manage;

use Besnovatyj\RunShop\entities\Tag;
use Besnovatyj\RunShop\forms\backend\TagForm;
use Besnovatyj\RunShop\repositories\TagRepository;

class TagManageService
{
    private $tags;

    public function __construct(TagRepository $tags)
    {
        $this->tags = $tags;
    }

    public function create(TagForm $form): Tag
    {
        $tag = Tag::create(
            $form->name,
            $form->slug
        );
        $this->tags->save($tag);
        return $tag;
    }

    public function edit(int $id, TagForm $form): void
    {
        $tag = $this->tags->get($id);
        $tag->edit(
            $form->name,
            $form->slug
        );
        $this->tags->save($tag);
    }

    public function remove(int $id): void
    {
        $tag = $this->tags->get($id);
        $this->tags->remove($tag);
    }
}
