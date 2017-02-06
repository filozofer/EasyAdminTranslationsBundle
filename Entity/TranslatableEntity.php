<?php

namespace EasyAdminTranslationsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use JsonSerializable;

/**
 * TranslatableEntity
 */
abstract class TranslatableEntity extends Translatable implements JsonSerializable
{

    /**
     * Translation class full name (default = Child class name . 'Translation')
     * This can be override in child class if needed
     */
    const TRANSLATION_CLASS = null;

    /**
     * Technical title use to identify translatable content in backoffice
     * @var string $technicalTitle
     * @ORM\Column(name="technical_title", type="string", length=255)
     */
    protected $technicalTitle;

    /**
     * Property to override in child object if you use annotation on this property
     * @var mixed $translations
     * @Serializer\Exclude()
     */
    protected $translations;

    /**
     * Do we need to translate this entity while Json encoding her ?
     * @var boolean $translate Default value = true
     * @Serializer\Exclude()
     */
    protected $translate;

    /**
     * Set this param to the wanted language before translation
     * @var Language $translationLanguage
     * @Serializer\Exclude()
     */
    protected $translationLanguage;

    /**
     * How to transform a TranslatableEntity into Json ?
     */
    public function jsonSerialize()
    {
        // Default value
        $jsonData = $this;

        // Only translate the entity if translate param is true or null (default = true)
        if(is_null($this->translate) || $this->translate === true) {

            // Merge current object with one of his translations
            $jsonData = (object) array_merge($this->objectToArray($this->getTranslation($this->getTranslationLanguage())), $this->objectToArray($this));

            // Remove all translations related params
            unset($jsonData->translations);
            unset($jsonData->parent);
            unset($jsonData->translate);
            unset($jsonData->translationLanguage);

        }

        // Return data to json_encode
        return $jsonData;
    }

    /**
     * Transform a Complex Object into a simple associative array (not like native array cast) recursively
     * @param mixed $translatableEntityObject
     * @return array
     */
    private function objectToArray($translatableEntityObject)
    {
        // Init default result
        $result = $translatableEntityObject;

        // Convert object to array if it's a TranslatableEntity object
        if(is_object($translatableEntityObject) && is_subclass_of($translatableEntityObject, 'EasyAdminTranslationsBundle\Entity\Translatable')) {
            $result = [];
            foreach ($translatableEntityObject->getTranslatableEntityObjectValues() as $key => $value) {
                $result[$key] = (is_array($value) || is_object($value)) ? $this->objectToArray($value) : $value;
            }
        }

        // Return convert object as simple associative array
        return $result;
    }

    /**
     * Get translations
     * @param Language $language language to retrieve
     * @return string
     * @throws \Exception Don't forget to set the language before calling this function !
     */
    public function getTranslation(Language $language = null)
    {
        // Allow to set language as global value
        global $defaultTranslationLanguage;
        $language = (!is_null($language)) ? $language : $defaultTranslationLanguage;

        // Exception: You need to set the language !
        if(is_null($language)) {
            throw new \Exception('You forgot to explicity set the language of translation. ' .
                'Please set the value into the TranslatableEntity you want to translate by using setTranslationLanguage() method ' .
                'or define the global variable $defaultTranslationLanguage before calling this function'
            );
        }

        // Look for the needed translation
        foreach ($this->translations as $translation) {
            if($translation->getLanguage()->getIsoKey() == $language->getIsoKey()) {
                return $translation;
            }
        }

        // Get classname of translation object
        $className = (!is_null(self::TRANSLATION_CLASS)) ? self::TRANSLATION_CLASS : get_class($this) . 'Translation';
        $emptyTranslation = new $className();
        $emptyTranslation->setLanguage($language);

        // Return empty translation object if no translation found
        return $emptyTranslation;
    }

    /**
     * Default toString function for TranslatableEntity
     * @return string
     */
    public function __toString() {
        return $this->getTechnicalTitle();
    }

    /**
     * Set translations
     *
     * @param array $translations
     *
     * @return mixed
     */
    public function setTranslations($translations)
    {
        $this->translations = $translations;
        return $this;
    }

    /**
     * Get translations
     *
     * @return Translation[]
     */
    public function getTranslations()
    {
        $translations = [];
        foreach ($this->translations as $translation) {
            $translations[] = $translation;
        }
        usort($translations, function($a, $b){
            return strcmp($a->getLanguage()->getIsoKey(), $b->getLanguage()->getIsoKey());
        });
        return $translations;
    }

    /**
     * Is this entity is currently translated on json_encode call ?
     * @return boolean
     */
    public function isTranslate()
    {
        return $this->translate;
    }

    /**
     * Set translate status
     * @param boolean $translate
     * @return TranslatableEntity
     */
    public function setTranslate($translate)
    {
        $this->translate = $translate;
        return $this;
    }

    /**
     * @return Language
     */
    public function getTranslationLanguage()
    {
        return $this->translationLanguage;
    }

    /**
     * @param Language $translationLanguage
     * @return TranslatableEntity
     */
    public function setTranslationLanguage($translationLanguage)
    {
        $this->translationLanguage = $translationLanguage;
        return $this;
    }

    /**
     * @return string
     */
    public function getTechnicalTitle()
    {
        return $this->technicalTitle;
    }

    /**
     * @param string $technicalTitle
     * @return TranslatableEntity
     */
    public function setTechnicalTitle($technicalTitle)
    {
        $this->technicalTitle = $technicalTitle;
        return $this;
    }

}


