<?php

namespace Charcoal\Cms;

use Exception;

// From 'charcoal-translator'
use Charcoal\Translator\Translation;

// From 'charcoal-cms'
use Charcoal\Object\CategoryInterface;
use Charcoal\Object\CategoryTrait;
use Charcoal\Object\Content;

/**
 * CMS Tag
 */
class Tag extends Content implements
    CategoryInterface
{
    use CategoryTrait;

    /**
     * The tag's name.
     *
     * @var Translation|string|null
     */
    protected $name;

    /**
     * The tag's color.
     *
     * @var string
     */
    protected $color;

    /**
     * @param array $data The object's data options.
     */
    public function __construct(array $data = null)
    {
        parent::__construct($data);

        $this->setData($this->defaultData());
    }

    // ==========================================================================
    // Functions
    // ==========================================================================

    /**
     * @throws Exception If function is called.
     * @return void
     */
    public function loadCategoryItems()
    {
        throw new Exception('Cannot use loadCategoryItems');
    }

    // ==========================================================================
    // GETTERS
    // ==========================================================================

    /**
     * The tag's name.
     *
     * @return Translation|string|null
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * The tag's color.
     *
     * @return mixed
     */
    public function color()
    {
        return $this->color;
    }

    // ==========================================================================
    // SETTERS
    // ==========================================================================

    /**
     * @param  mixed $name The name of the tag.
     * @return self
     */
    public function setName($name)
    {
        $this->name = $this->translator()->translation($name);
        return $this;
    }

    /**
     * @param  string $color The color in HEX format as a string.
     * @return self
     */
    public function setColor($color)
    {
        $this->color = $color;
        return $this;
    }
}
