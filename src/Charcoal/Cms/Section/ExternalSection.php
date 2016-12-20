<?php

namespace Charcoal\Cms;

// From 'charcoal-translator'
use Charcoal\Translator\Translation;

// From 'charcoal-cms'
use Charcoal\Cms\AbstractSection;

/**
 * External section may appear in menus and breadcrumbs, but only
 *
 * Unlike all other section types, they do not provide any metadata information.
 */
class ExternalSection extends AbstractSection
{
    /**
     * @var Translation|string|null
     */
    private $externalUrl;

    /**
     * @param  mixed $url The external URL (localized).
     * @return self
     */
    public function setExternalUrl($url)
    {
        $this->externalUrl = $this->translator()->translation($url);
        $this->externalUrl = new TranslationString($url);

        return $this;
    }

    /**
     * @return Translation|string|null
     */
    public function externalUrl()
    {
        return $this->externalUrl;
    }
}
