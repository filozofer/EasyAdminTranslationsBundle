<?php

namespace EasyAdminTranslationsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Translation
 */
abstract class Translation extends Translatable
{
    /**
     * Property to override in child object to use annotation on this property
     * @var TranslatableEntity
     */
    protected $parent;

    /**
     * Language of this translation
     * @var Language
     *
     * @ORM\ManyToOne(targetEntity="EasyAdminTranslationsBundle\Entity\Language")
     * @ORM\JoinColumn(name="language_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $language;

    /**
     * @return TranslatableEntity
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param TranslatableEntity|int $parent
     * @return Translation
     */
    public function setParent($parent)
    {
        if(!is_subclass_of($parent, 'EasyAdminTranslationsBundle\Entity\TranslatableEntity') && !is_numeric($parent)) { return $this; }
        $this->parent = $parent;
        return $this;
    }

    /**
     * @return Language
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param Language $language
     * @return Translation
     */
    public function setLanguage($language)
    {
        if(!is_a($language, 'EasyAdminTranslationsBundle\Entity\Language') && !is_numeric($language)) { return $this; }
        $this->language = $language;
        return $this;
    }

}


