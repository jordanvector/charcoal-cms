<?php

namespace Charcoal\Cms;

// From 'charcoal-cms'
use Charcoal\Cms\AbstractEvent;
use Charcoal\Cms\EventCategory;

/**
 * CMS Event
 */
final class Event extends AbstractEvent
{
    /**
     * CategorizableTrait > category_type()
     *
     * @return string
     */
    public function categoryType()
    {
        return EventCategory::class;
    }

    /**
     * MetatagTrait > canonical_url
     *
     * @todo
     * @return string
     */
    public function canonicalUrl()
    {
        return '';
    }
}
