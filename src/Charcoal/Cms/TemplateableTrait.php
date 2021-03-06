<?php

namespace Charcoal\Cms;

use RuntimeException;

// From 'charcoal-core'
use Charcoal\Model\Model;
use Charcoal\Model\ModelInterface;
use Charcoal\Source\StorableTrait;

// From 'charcoal-property'
use Charcoal\Property\PropertyInterface;
use Charcoal\Property\SelectablePropertyInterface;
use Charcoal\Property\TemplateOptionsProperty;
use Charcoal\Property\TemplateProperty;

// From 'charcoal-cms'
use Charcoal\Cms\TemplateableInterface;

/**
 * Default implementation, as Trait, of the {@see TemplateableInterface}.
 *
 * Note: Call {@see self::saveTemplateOptions()} in {@see StorableTrait::preSave()}
 * and {@see StorableTrait::preUpdate()} when using {@see TemplateOptionsProperty}.
 */
trait TemplateableTrait
{
    /**
     * The object's template identifier.
     *
     * @var mixed
     */
    protected $templateIdent;

    /**
     * The object's template controller identifier.
     *
     * @var mixed
     */
    protected $controllerIdent;

    /**
     * The customized template options.
     *
     * @var array
     */
    protected $templateOptions = [];

    /**
     * Track the state of the template options structure.
     *
     * @var boolean
     */
    protected $areTemplateOptionsFinalized = false;



    // Properties
    // =========================================================================

    /**
     * Set the renderable object's template identifier.
     *
     * @param  mixed $template The template ID.
     * @return self
     */
    public function setTemplateIdent($template)
    {
        $this->areTemplateOptionsFinalized = false;

        $this->templateIdent = $template;

        return $this;
    }

    /**
     * Retrieve the renderable object's template identifier.
     *
     * @return mixed
     */
    public function templateIdent()
    {
        return $this->templateIdent;
    }

    /**
     * Set the renderable object's template controller identifier.
     *
     * @param  mixed $ident The template controller identifier.
     * @return self
     */
    public function setControllerIdent($ident)
    {
        $this->areTemplateOptionsFinalized = false;

        $this->controllerIdent = $ident;

        return $this;
    }

    /**
     * Retrieve the renderable object's template controller identifier.
     *
     * @return mixed
     */
    public function controllerIdent()
    {
        return $this->controllerIdent;
    }

    /**
     * Customize the template's options.
     *
     * @param  mixed $options Template options.
     * @return self
     */
    public function setTemplateOptions($options)
    {
        $this->areTemplateOptionsFinalized = false;

        if (is_numeric($options)) {
            $options = null;
        } elseif (is_string($options)) {
            $options = json_decode($options, true);
        }

        $this->templateOptions = $options;

        return $this;
    }

    /**
     * Retrieve the template's customized options.
     *
     * @return mixed
     */
    public function templateOptions()
    {
        return $this->templateOptions;
    }



    // Utilities
    // =========================================================================

    /**
     * Asserts that the templateable class meets the requirements,
     * throws an Exception if not.
     *
     * Requirements:
     * 1. The class implements the {@see TemplateableInterface} and
     * 2. The model's "template_options" property uses the {@see TemplateOptionsProperty}.
     *
     *
     * @throws RuntimeException If the model does not implement its requirements.
     * @return void
     */
    final protected function assertValidTemplateStructureDependencies()
    {
        if (!$this instanceof TemplateableInterface) {
            throw new RuntimeException(sprintf(
                'Class [%s] must implement [%s]',
                get_class($this),
                TemplateableInterface::class
            ));
        }

        $prop = $this->property('template_options');
        if (!$prop instanceof TemplateOptionsProperty) {
            throw new RuntimeException(sprintf(
                'Property "%s" must use [%s]',
                $prop->ident(),
                TemplateOptionsProperty::class
            ));
        }
    }

    /**
     * Retrieve the default template propert(y|ies).
     *
     * @return string[]
     */
    protected function defaultTemplateProperties()
    {
        return [ 'template_ident' ];
    }

    /**
     * Retrieve the template's structure interface(s).
     *
     * @see    TemplateProperty::__toString()
     * @see    \Charcoal\Admin\Widget\FormGroup\TemplateOptionsFormGroup::finalizeStructure()
     * @todo   Migrate `MissingDependencyException` from 'mcaskill/charcoal-support' to 'mcaskill/charcoal-core'.
     * @param  PropertyInterface|string ...$properties The properties to lookup.
     * @return string[]|null
     */
    protected function extractTemplateInterfacesFrom(...$properties)
    {
        $interfaces = [];
        foreach ($properties as $property) {
            if (!$property instanceof PropertyInterface) {
                $property = $this->property($property);
            }

            $key = $property->ident();
            $val = $this->propertyValue($key);
            if ($property instanceof SelectablePropertyInterface) {
                if ($property->hasChoice($val)) {
                    $choice = $property->choice($val);
                    $keys   = [ 'controller', 'template', 'class' ];
                    foreach ($keys as $key) {
                        if (isset($choice[$key])) {
                            $interfaces[] = $choice[$key];
                            break;
                        }
                    }
                }
            } else {
                $interfaces[] = $val;
            }
        }

        return $interfaces;
    }

    /**
     * Prepare the template options (structure) for use.
     *
     * @uses   self::assertValidTemplateStructureDependencies() Validates that the model meets requirements.
     * @param  (PropertyInterface|string)[]|null $properties The template properties to parse.
     * @return boolean
     */
    protected function prepareTemplateOptions(array $properties = null)
    {
        $this->assertValidTemplateStructureDependencies();

        if ($properties === null) {
            $properties = $this->defaultTemplateProperties();
        }

        $interfaces = $this->extractTemplateInterfacesFrom(...$properties);
        if (empty($interfaces)) {
            return false;
        }

        $this->property('template_options')->addStructureInterfaces($interfaces);

        return true;
    }

    /**
     * Save the template options structure.
     *
     * @param  (PropertyInterface|string)[]|null $properties The template properties to parse.
     * @return void
     */
    protected function saveTemplateOptions(array $properties = null)
    {
        if ($properties === null) {
            $properties = $this->defaultTemplateProperties();
        }

        $this->prepareTemplateOptions($properties);

        $prop = $this->property('template_options');
        if ($prop->structureModelClass() === Model::class) {
            $struct = $this->propertyValue('template_options');
            $struct = $prop->structureVal($struct);
            foreach ($struct->properties() as $propertyIdent => $property) {
                $val = $struct[$propertyIdent];
                if ($property->l10n()) {
                    $val = $this->translator()->translation($struct[$propertyIdent]);
                }

                $struct[$propertyIdent] = $property->save($val);
            }
        }
    }

    /**
     * Retrieve the template options as a structured model.
     *
     * @return ModelInterface|ModelInterface[]|null
     */
    public function templateOptionsStructure()
    {
        if ($this->areTemplateOptionsFinalized === false) {
            $this->areTemplateOptionsFinalized = true;
            $this->prepareTemplateOptions();
        }

        $key  = 'template_options';
        $prop = $this->property($key);
        $val  = $this->propertyValue($key);

        return $prop->structureVal($val);
    }
}
