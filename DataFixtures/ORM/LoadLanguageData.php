<?php

namespace EasyAdminTranslationsBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use EasyAdminTranslationsBundle\Entity\Language;

/**
 * Class LoadLanguageData
 * @package EasyAdminTranslationsBundle\DataFixtures\ORM
 */
class LoadLanguageData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        // Add default english language
        $enLanguage = new Language();
        $enLanguage
            ->setDefaultTranslationLanguage(true)
            ->setTitle('English')
            ->setIsoKey('en_US')
            ->setFlag('../../bundles/easyadmintranslations/images/flags/small/us.png')
        ;
        $manager->persist($enLanguage);

        // Add spain language
        $esLanguage = new Language();
        $esLanguage
            ->setTitle('Español')
            ->setIsoKey('es-ES')
            ->setFlag('../../bundles/easyadmintranslations/images/flags/small/es.png')
        ;
        $manager->persist($esLanguage);

        // Add german language
        $esLanguage = new Language();
        $esLanguage
            ->setTitle('Deutsch')
            ->setIsoKey('de-DE')
            ->setFlag('../../bundles/easyadmintranslations/images/flags/small/de.png')
        ;
        $manager->persist($esLanguage);

        // Add french language
        $frLanguage = new Language();
        $frLanguage
            ->setTitle('Français')
            ->setIsoKey('fr_FR')
            ->setFlag('../../bundles/easyadmintranslations/images/flags/small/fr.png')
        ;
        $manager->persist($frLanguage);

        $manager->flush();
    }
}