<?php

namespace EasyAdminTranslationsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use DateTime;

/**
 * Class TranslationsComponentType
 * Form component to list and manage translations
 */
class TranslationsComponentType extends AbstractType
{
    protected $em;

    /**
     * TranslationsComponentType constructor.
     * @param $em
     */
    public function __construct($em)
    {
        $this->em = $em;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Nothing to do here
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        // Current entity
        $currentEntity = $view->parent->vars['value'];
        $childEntity = null;

        // Protection
        if(!is_subclass_of($currentEntity, 'EasyAdminTranslationsBundle\Entity\TranslatableEntity')) {
            throw new \Exception('You cannot use the "eatb_translations_component" field for an entity which is not a TranslatableEntity object');
        }

        // Set current entity to parent if we're are on a translation
        if(is_subclass_of($currentEntity, 'EasyAdminTranslationsBundle\Entity\Translation')) {
            $view->vars['current_translation'] = $currentEntity;
            $currentEntity = $currentEntity->getParent();
        }

        // Get all the declare language
        $languages = $this->em->getRepository('EasyAdminTranslationsBundle:Language')->findAll();

        // Build translations status array
        $translationsStatus = [];
        foreach ($languages as $language) {
            $translationsStatus[$language->getIsoKey()] = [
                'language'    => $language,
                'translation' => $currentEntity->getTranslation($language)
            ];
        }

        // Pass translations & current entity to view
        $view->vars['translations'] = $translationsStatus;
        $view->vars['current_entity'] = $currentEntity;

        // Call parent finish view
        parent::finishView($view, $form, $options);
    }

    /**
     * Component name
     * @return string
     */
    public function getName()
    {
        return 'eatb_translations_component';
    }
}
