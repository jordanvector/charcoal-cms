<?php

namespace Charcoal\Cms\Section;

// From 'charcoal-cms'
use Charcoal\Cms\AbstractSection;

// Parent namespace dependencies
use Charcoal\Cms\Mixin\BlocksSectionInterface;
use Charcoal\Cms\Mixin\Traits\BlocksSectionTrait;

// dependencies from `charcoal-core`
use Charcoal\Loader\CollectionLoader;

// dependencies from `pimple`
use Pimple\Container;

// dependencies from Psr-7
use \RuntimeException;

/**
 * Blocks-content section
 */
class BlocksSection extends AbstractSection implements
    BlocksSectionInterface
{
    use BlocksSectionTrait;
/**
     * @var Collection $blocks
     */
    private $blocks;

    /**
     * Overrides `AbstractSection::section_type()`
     *
     * @return string
     */
    public function sectionType()
    {
        return AbstractSection::TYPE_BLOCKS;
    }

    /**
     * @return Collection List of `Block` objects
     */
    public function blocks()
    {
        if ($this->blocks === null) {
            $this->blocks = $this->loadBlocks();
        }
        return $this->blocks;
    }

    /**
     * @return Collection
     */
    public function loadBlocks()
    {
        // @todo
        return [];
    }
}
