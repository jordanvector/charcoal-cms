<?php

namespace Charcoal\Cms;

use DateTime;
use DateTimeInterface;
use InvalidArgumentException;

// From PSR-7
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

// From 'charcoal-object'
use Charcoal\Object\Content;
use Charcoal\Object\CategorizableInterface;
use Charcoal\Object\CategorizableTrait;
use Charcoal\Object\PublishableInterface;
use Charcoal\Object\PublishableTrait;
use Charcoal\Object\RoutableInterface;
use Charcoal\Object\RoutableTrait;

// From 'charcoal-translator'
use Charcoal\Translator\Translation;

// From 'charcoal-cms'
use Charcoal\Cms\MetatagInterface;
use Charcoal\Cms\NewsInterface;
use Charcoal\Cms\SearchableInterface;
use Charcoal\Cms\SearchableTrait;
use Charcoal\Cms\TemplateableInterface;

// Local dependencies
use Charcoal\Cms\Support\Helpers\DateHelper;

// Pimple dependencies
use Pimple\Container;

/**
 * News
 */
abstract class AbstractNews extends Content implements
    CategorizableInterface,
    MetatagInterface,
    NewsInterface,
    PublishableInterface,
    RoutableInterface,
    SearchableInterface,
    TemplateableInterface
{
    use CategorizableTrait;
    use PublishableTrait;
    use MetatagTrait;
    use RoutableTrait;
    use SearchableTrait;
    use TemplateableTrait;

    /**
     * @var Translation|string|null
     */
    private $title;

    /**
     * @var Translation|string|null
     */
    private $subtitle;

    /**
     * @var Translation|string|null
     */
    private $summary;

    /**
     * @var Translation|string|null
     */
    private $content;

    /**
     * @var Translation|string|null
     */
    private $image;

    /**
     * @var DateTimeInterface|null
     */
    private $newsDate;

    /**
     * @var Translation|string|null
     */
    private $infoUrl;

    /**
     * @var array
     */
    protected $keywords;

    // ==========================================================================
    // INIT
    // ==========================================================================

    /**
     * Section constructor.
     * @param array $data The data.
     */
    public function __construct(array $data = null)
    {
        parent::__construct($data);

        if (is_callable([ $this, 'defaultData' ])) {
            $this->setData($this->defaultData());
        }
    }

    // ==========================================================================
    // FUNCTIONS
    // ==========================================================================

    /**
     * In the datetime attribute of the <time> tag
     * @return string The datetime attribute formatted.
     */
    public function dateTimeDate()
    {
        $newsDate = $this->newsDate();

        return $newsDate->format('Y-m-d H:i:s');
    }

    /**
     * Some dates cannot be null
     * @return void
     */
    public function verifyDates()
    {
        if (!$this->newsDate()) {
            $this->setNewsDate('now');
        }

        if (!$this->publishDate()) {
            $this->setPublishDate('now');
        }
    }

    /**
     * @return string The date filtered for admin dual select input and others.
     */
    public function adminDateFilter()
    {
        return $this->newsDate()->format('Y-m-d');
    }

    // ==========================================================================
    // SETTERS
    // ==========================================================================

    /**
     * @param mixed $title The news title (localized).
     * @return self
     */
    public function setTitle($title)
    {
        $this->title = $this->translator()->translation($title);

        return $this;
    }

    /**
     * @param  mixed $subtitle The news subtitle (localized).
     * @return self
     */
    public function setSubtitle($subtitle)
    {
        $this->subtitle = $this->translator()->translation($subtitle);

        return $this;
    }

    /**
     * @param mixed $summary The news summary (localized).
     * @return self
     */
    public function setSummary($summary)
    {
        $this->summary = $this->translator()->translation($summary);

        return $this;
    }

    /**
     * @param mixed $content The news content (localized).
     * @return self
     */
    public function setContent($content)
    {
        $this->content = $this->translator()->translation($content);

        return $this;
    }

    /**
     * @param mixed $image The section main image (localized).
     * @return self
     */
    public function setImage($image)
    {
        $this->image = $this->translator()->translation($image);

        return $this;
    }

    /**
     * @param mixed $url The info URL (news source or where to find more information; localized).
     * @return self
     */
    public function setInfoUrl($url)
    {
        $this->infoUrl = $this->translator()->translation($url);

        return $this;
    }

    /**
     * @param  string|DateTimeInterface $newsDate The news date.
     * @throws InvalidArgumentException If the timestamp is invalid.
     * @return self
     */
    public function setNewsDate($newsDate)
    {
        if ($newsDate === null || $newsDate === '') {
            $this->newsDate = null;

            return $this;
        }
        if (is_string($newsDate)) {
            $newsDate = new DateTime($newsDate);
        }
        if (!($newsDate instanceof DateTimeInterface)) {
            throw new InvalidArgumentException(
                'Invalid "Revision Date" value. Must be a date/time string or a DateTimeInterface object.'
            );
        }
        $this->newsDate = $newsDate;

        return $this;
    }

    /**
     * Set the object's keywords.
     *
     * @param  string|string[] $keywords One or more entries.
     * @return self
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;

        return $this;
    }

    // ==========================================================================
    // GETTERS
    // ==========================================================================

    /**
     * @return Translation|string|null
     */
    public function title()
    {
        return $this->title;
    }

    /**
     * @return Translation|string|null
     */
    public function subtitle()
    {
        return $this->subtitle;
    }

    /**
     * @return Translation|string|null
     */
    public function summary()
    {
        return $this->summary;
    }

    /**
     * @return Translation|string|null
     */
    public function infoUrl()
    {
        return $this->infoUrl;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function newsDate()
    {
        return $this->newsDate;
    }

    /**
     * @return Translation|string|null
     */
    public function content()
    {
        return $this->content;
    }

    /**
     * @return Translation|string|null
     */
    public function image()
    {
        return $this->image;
    }

    // ==========================================================================
    // META TAGS
    // ==========================================================================

    /**
     * MetatagTrait > canonical_url
     *
     * @return string
     * @todo
     */
    public function canonicalUrl()
    {
        return '';
    }

    /**
     * @return Translation|string|null
     */
    public function defaultMetaTitle()
    {
        return $this->title();
    }

    /**
     * @return Translation|string|null
     */
    public function defaultMetaDescription()
    {
        $content = $this->translator()->translation($this->content());
        if ($content instanceof Translation) {
            $desc = [];
            foreach ($content->data() as $lang => $text) {
                $desc[$lang] = strip_tags($text);
            }

            return $this->translator()->translation($desc);
        }

        return null;
    }

    /**
     * @return Translation|string|null
     */
    public function defaultMetaImage()
    {
        return $this->image();
    }

    /**
     * Retrieve the object's keywords.
     *
     * @return string[]
     */
    public function keywords()
    {
        return $this->keywords;
    }

    // ==========================================================================
    // EVENTS
    // ==========================================================================

    /**
     * {@inheritdoc}
     *
     * @return boolean
     */
    public function preSave()
    {
        $this->verifyDates();
        $this->setSlug($this->generateSlug());
        $this->generateDefaultMetaTags();

        return parent::preSave();
    }

    /**
     * {@inheritdoc}
     *
     * @param array $properties Optional properties to update.
     * @return boolean
     */
    public function preUpdate(array $properties = null)
    {
        $this->verifyDates();
        $this->setSlug($this->generateSlug());
        $this->generateDefaultMetaTags();

        return parent::preUpdate($properties);
    }

    /**
     * @return boolean Parent postSave().
     */
    public function postSave()
    {
        // RoutableTrait
        $this->generateObjectRoute($this->slug());

        return parent::postSave();
    }

    /**
     * @param array|null $properties Properties.
     * @return boolean
     */
    public function postUpdate(array $properties = null)
    {
        // RoutableTrait
        $this->generateObjectRoute($this->slug());

        return parent::postUpdate($properties);
    }

    /**
     * GenericRoute checks if the route is active.
     * Default in RoutableTrait.
     *
     * @return boolean
     */
    public function isActiveRoute()
    {
        return (
            $this->active() &&
            $this->isPublished()
        );
    }
}
