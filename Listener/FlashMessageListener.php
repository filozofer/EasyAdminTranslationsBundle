<?php

namespace EasyAdminTranslationsBundle\Listener;

use JavierEguiluz\Bundle\EasyAdminBundle\Event\EasyAdminEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class FlashMessageListener implements EventSubscriberInterface
{
    private $translator;
    private $session;

    /**
     * FlashMessageListerner constructor.
     * @param $translator
     */
    public function __construct($translator, $session)
    {
        $this->translator = $translator;
        $this->session = $session;
    }

    /**
     * Declare which events we are listening
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            EasyAdminEvents::POST_PERSIST => ['flashMessageForPersist'],
            EasyAdminEvents::POST_UPDATE  => ['flashMessageForUpdate'],
        ];
    }

    /**
     * Flash message when entity his persist (created)
     * @param GenericEvent $event
     */
    public function flashMessageForPersist(GenericEvent $event)
    {
        if(is_subclass_of($event->getSubject(), 'EasyAdminTranslationsBundle\Entity\Translation')) {
            $this->session->getFlashBag()->add('success', $this->translator->trans('flash_messages.persist_translation', [], 'EasyAdminTranslationsBundle'));
        }
        else if(is_subclass_of($event->getSubject(), 'EasyAdminTranslationsBundle\Entity\TranslatableEntity')) {
            $this->session->getFlashBag()->add('success', $this->translator->trans('flash_messages.persist_entity', [], 'EasyAdminTranslationsBundle'));
        }
    }

    /**
     * Flash message when entity his updated
     * @param GenericEvent $event
     */
    public function flashMessageForUpdate(GenericEvent $event)
    {
        if(is_subclass_of($event->getSubject(), 'EasyAdminTranslationsBundle\Entity\Translation')) {
            $this->session->getFlashBag()->add('success', $this->translator->trans('flash_messages.update_translation', [], 'EasyAdminTranslationsBundle'));
        }
        else if(is_subclass_of($event->getSubject(), 'EasyAdminTranslationsBundle\Entity\TranslatableEntity')) {
            $this->session->getFlashBag()->add('success', $this->translator->trans('flash_messages.update_entity', [], 'EasyAdminTranslationsBundle'));
        }
    }
}