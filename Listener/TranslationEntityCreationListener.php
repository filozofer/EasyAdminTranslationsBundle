<?php

namespace EasyAdminTranslationsBundle\Listener;

use Doctrine\ORM\EntityManager;
use JavierEguiluz\Bundle\EasyAdminBundle\Event\EasyAdminEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

class TranslationEntityCreationListener implements EventSubscriberInterface
{
    /** @var EntityManager */
    private $em;
    /** @var ConfigManager */
    private $configManager;

    /**
     * TranslationEntityCreationListener constructor.
     * @param EntityManager $em
     * @param ConfigManager $configManager
     */
    public function __construct($em, $configManager)
    {
        $this->em = $em;
        $this->configManager = $configManager;
    }

    /**
     * Declare which events we are listening
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            EasyAdminEvents::POST_NEW => ['translationEntityCreation'],
            EasyAdminEvents::PRE_PERSIST => ['translationEntityPersist'],
            EasyAdminEvents::PRE_UPDATE => ['translationEntityPersist']
        ];
    }

    /**
     * Fill some of the fields base on url params when trying to create a Translation
     * @param GenericEvent $event
     */
    public function translationEntityCreation(GenericEvent $event)
    {
        // Only apply for Translation entity
        if(!is_subclass_of($event->getSubject(), 'EasyAdminTranslationsBundle\Entity\Translation')) {
            return;
        }

        // Get translation, form, config and request references from event
        $translation = $event->getArgument('entity');
        $form = $event->getArgument('form');
        $config = $event->getArgument('config');
        $request = $event->getArgument('request');

        // Quit early if form is already submitted
        if($form->isSubmitted()) {
            return;
        }

        // Get translation class name
        $translationClassName = join('', array_slice(explode('\\', get_class($translation)), -1));

        // Do we need to fill language field ?
        if(!is_null($request->query->get('language_id', null))) {

            // Get language from database
            $language = $this->em->getRepository('EasyAdminTranslationsBundle:Language')->findOneById($request->query->get('language_id', null));

            // Set this language as the field value for current new translation if language exist
            if(!is_null($language) && $form->has('language')) {

                // If field is a hidden field, put the id of the language and not the full entity as value
                $languageFieldConfig = $config['entities'][$translationClassName]['form']['fields']['language'];
                $value = (isset($languageFieldConfig['type']) && $languageFieldConfig['type'] === 'hidden') ? $language->getId() : $language;

                // Set language field value
                $form->get('language')->setData($value);

            }

        }

        // Do we need to fill parent field ?
        if(!is_null($request->query->get('parent_id', null))) {

            // Get parent class name
            $parentClassName = $config['entities'][$translationClassName]['properties']['parent']['targetEntity'];

            // Get parent from database
            $parent = $this->em->getRepository($parentClassName)->findOneById($request->query->get('parent_id', null));

            // Set this parent as the field value for current new translation if parent exist
            if(!is_null($parent) && $form->has('parent')) {

                // If field is a hidden field, put the id of the parent and not the full entity as value
                $parentFieldConfig = $config['entities'][$translationClassName]['form']['fields']['parent'];
                $value = (isset($parentFieldConfig['type']) && $parentFieldConfig['type'] === 'hidden') ? $parent->getId() : $parent;

                // Set parent field value
                $form->get('parent')->setData($value);

            }

        }

    }

    /**
     * Transform language en parent field into real object if there is only theirs ids
     * @param GenericEvent $event
     */
    public function translationEntityPersist(GenericEvent $event) {

        // Only apply for Translation entity
        if(!is_subclass_of($event->getSubject(), 'EasyAdminTranslationsBundle\Entity\Translation')) {
            return;
        }

        // Get translation & config
        $translation = $event->getArgument('entity');

        // Transform language ? (in case of hidden field usage)
        if(!is_a($translation->getLanguage(), 'EasyAdminTranslationsBundle\Entity\Language') || !is_subclass_of($translation->getLanguage(), 'EasyAdminTranslationsBundle\Entity\Language')) {

            // Init language value with current language value
            $language = $translation->getLanguage();

            // Get language from database when language field is the id of the language
            if(is_numeric($translation->getLanguage())) {
                $language = $this->em->getRepository('EasyAdminTranslationsBundle:Language')->findOneById((int) $translation->getLanguage());
            }

            // Set language (even if it's null which allow to clear the field from a wrong string value)
            $translation->setLanguage($language);

        }

        // Transform parent ? (in case of hidden field usage)
        if(!is_subclass_of($translation->getParent(), 'EasyAdminTranslationsBundle\Entity\TranslatableEntity')) {

            // Get parent class full namespace
            $entityConfig = $this->configManager->getEntityConfig(join('', array_slice(explode('\\', get_class($translation)), -1)));
            $parentClassName = $entityConfig['properties']['parent']['targetEntity'];

            // Init parent value with current parent value
            $parent = $translation->getParent();

            // Get parent from database when parent field is the id of the parent
            if(is_numeric($translation->getParent())) {
                $parent = $this->em->getRepository($parentClassName)->findOneById((int) $translation->getParent());
            }

            // Set parent (even if it's null which allow to clear the field from a wrong string value)
            $translation->setParent($parent);

        }

    }

}