<?php

namespace EasyAdminTranslationsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;

/**
 * Question
 *
 * @ORM\Table(name="language")
 * @ORM\Entity(repositoryClass="EasyAdminTranslationsBundle\Repository\LanguageRepository")
 * @Vich\Uploadable
 * @ExclusionPolicy("all")
 */
class Language extends Translatable
{

    use ORMBehaviors\Timestampable\Timestampable;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Expose
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     * @Expose
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(name="iso_key", type="string", length=255)
     * @Expose
     */
    protected $isoKey;

    /**
     * Is this Language is the default translation language return by web service ?
     * @var boolean
     * @ORM\Column(name="default_translation_language", type="boolean", nullable=true)
     * @Expose
     */
    protected $defaultTranslationLanguage;

    /**
     * Language flag icon
     * @var File $flag
     *
     * @Vich\UploadableField(mapping="flag_icon", fileNameProperty="flag")
     */
    private $flagFile;

    /**
     * @var string
     *
     * @ORM\Column(name="flag", type="string", length=255, nullable=true )
     * @Expose
     */
    protected $flag;

    /**
     * Convert Language object to string
     * @return string
     */
    public function __toString() {
        return $this->getTitle();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Language
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return Language
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getIsoKey()
    {
        return $this->isoKey;
    }

    /**
     * @param string $isoKey
     * @return Language
     */
    public function setIsoKey($isoKey)
    {
        $this->isoKey = $isoKey;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isDefaultTranslationLanguage()
    {
        return $this->defaultTranslationLanguage;
    }

    /**
     * @param boolean $defaultTranslationLanguage
     * @return Language
     */
    public function setDefaultTranslationLanguage($defaultTranslationLanguage)
    {
        $this->defaultTranslationLanguage = $defaultTranslationLanguage;
        return $this;
    }

    /**
     * @return File
     */
    public function getFlagFile()
    {
        return $this->flagFile;
    }

    /**
     * Set flagFile
     *
     * @param string $flagFile
     * @return Media
     */
    public function setFlagFile($flagFile)
    {
        $this->flagFile = $flagFile;
        if ($flagFile) {
            $this->updatedAt = new \DateTime('now');
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getFlag()
    {
        return $this->flag;
    }

    /**
     * @param string $flag
     * @return Language
     */
    public function setFlag($flag)
    {
        $this->flag = $flag;
        return $this;
    }

}

