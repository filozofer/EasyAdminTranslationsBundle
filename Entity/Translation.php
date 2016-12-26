<?php

namespace EasyAdminTranslationsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Translation
 */
abstract class Translation extends Translatable
{
    /**
     * Property to override in child object if you use annotation on this property
     * @var mixed Parent
     */
    protected $parent;

    /**
     * @var Language
     *
     * @ORM\ManyToOne(targetEntity="EasyAdminTranslationsBundle\Entity\Language", inversedBy="translations")
     * @ORM\JoinColumn(name="language_id", referencedColumnName="id", nullable=false)
     */
    protected $language;

    /**
     * @return Question
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param Question $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
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
        $this->language = $language;
        return $this;
    }

}


