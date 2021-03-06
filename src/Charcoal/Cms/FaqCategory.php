<?php

namespace Charcoal\Cms;

// From 'charcoal-object'
use Charcoal\Object\Content;
use Charcoal\Object\CategoryInterface;
use Charcoal\Object\CategoryTrait;

// From 'charcoal-cms'
use Charcoal\Cms\Faq;

/**
 * FAQ Category
 */
final class FaqCategory extends Content implements CategoryInterface
{
    use CategoryTrait;

    /**
     * CategoryTrait > item_type()
     *
     * @return string
     */
    public function itemType()
    {
        return Faq::class;
    }

    /**
     * @return \Charcoal\Model\Collection|array
     */
    public function loadCategoryItems()
    {
        return [];
    }
}
