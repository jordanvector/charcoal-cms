<?php

namespace Charcoal\Admin\Property\Input\Selectize;

use InvalidArgumentException;

// From 'charcoal-admin'
use Charcoal\Admin\Property\Input\SelectizeInput;

/**
 * Relation Input Selectize
 */
class RelationInput extends SelectizeInput
{
    /**
     * The target object type to build the choices from.
     *
     * @var string
     */
    private $targetObjectType;

    /**
     * Retrieve the target object type to build the choices from.
     *
     * @throws InvalidArgumentException If the target object type was not previously set.
     * @return string
     */
    public function targetObjectType()
    {
        if ($this->targetObjectType === null) {
            $resolved = false;
            if (!$this->resolveTargetObjectType()) {
                throw new InvalidArgumentException(
                    'Target object type could not be properly determined.'
                );
            }
        }

        return $this->targetObjectType;
    }

    /**
     * Resolve the target object type from multiple sources & contexts
     *
     * @return boolean
     */
    private function resolveTargetObjectType()
    {
        $resolved = false;
        $formData = $this->viewController()->formData();
        $param = isset($_GET['target_object_type']) ? $_GET['target_object_type'] : false;

        // Resolving through formData should be the most common occurence for this property input
        if (isset($formData['target_object_type']) &&
            is_string($formData['target_object_type']) &&
            !empty($formData['target_object_type'])
        ) {
            $this->targetObjectType = $formData['target_object_type'];
            $resolved = true;
        // Resolve through URL params
        } elseif (is_string($param) && !empty($param)) {
            $this->targetObjectType = $param;
            $resolved = true;
        }

        return $resolved;
    }
}
