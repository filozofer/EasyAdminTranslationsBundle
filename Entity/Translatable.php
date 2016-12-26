<?php

namespace EasyAdminTranslationsBundle\Entity;

/**
 * Class Translatable
 * Allow to retrieve all public and protected when using on a subclass outside of this subclass
 * @package EasyAdminTranslationsBundle\Entity
 */
abstract class Translatable {

    /**
     * Get all PUBLIC and PROTECTED params with theirs associates values in this object
     * @return array
     */
    public function getTranslatableEntityObjectValues() {
        return get_object_vars($this);
    }

}