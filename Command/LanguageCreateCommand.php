<?php

namespace EasyAdminTranslationsBundle\Command;

use EasyAdminTranslationsBundle\Entity\Language;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

class LanguageCreateCommand extends ContainerAwareCommand {

    // Language list we can create when user ask for it
    const LANGUAGE_LIST = [
        'fr_FR' => ['title' => 'Français', 'isoKey' => 'fr_FR'],
        'en_US' => ['title' => 'English', 'isoKey' => 'en_US'],
        'de_DE' => ['title' => 'Deutsch', 'isoKey' => 'de_DE'],
        'es_ES' => ['title' => 'Español', 'isoKey' => 'es_ES'],
        // To continue ...
    ];

    // Flag files path
    const FLAG_FILES_PATH = '../../bundles/easyadmintranslations/images/flags/small/';

    // Flag files list of images
    const FLAGS_FILES_LIST = [
        'fr_FR' => 'fr.png',
        'en_US' => 'us.png',
        'de_DE' => 'de.png',
        'es_ES' => 'es.png',
        // To continue ...
    ];

    /**
     * Command configuration
     */
    protected function configure()
    {
        $this
            ->setName('eatb:language:create')
            ->setDescription('Allow to create language in command line')
            ->addOption('title', null, InputOption::VALUE_OPTIONAL, 'Language title')
            ->addOption('isoKey', null, InputOption::VALUE_OPTIONAL, 'Language isoKey')
            ->addOption('flag', null, InputOption::VALUE_OPTIONAL, 'Language flag file path')
            ->addOption('defaultTranslationLanguage', null, InputOption::VALUE_OPTIONAL, 'Default translation language ?')
            ->addArgument('languages', InputArgument::IS_ARRAY | InputArgument::OPTIONAL, 'List of language to create using iso_key (separate multiple iso_key with a space)')
            ->addOption('choice-list', null, InputOption::VALUE_NONE, 'Ask user for which language he want to create')
        ;
    }

    /**
     * Command execution: Allow to create language in command line
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Get entity manager & declare output component & helper
        $em = $this->getContainer()->get('doctrine')->getManager();
        $io = new SymfonyStyle($input, $output);
        $helper = $this->getHelper('question');

        // Print command title
        $io->title('Create language');

        // Handle languages list to create
        if(!empty($input->getArgument('languages'))) {

            // Create language from so key
            $this->createLanguageFromIsoKeyList($io, $input->getArgument('languages'));

            // Exit command
            return;

        }

        // Handle choice-list option
        if($input->getOption('choice-list')) {

            // Build question
            $question = new ChoiceQuestion('Please select the language(s) you want to create: (separate your choice by using ",")', array_keys(self::LANGUAGE_LIST));
            $question->setMultiselect(true);
            $languages = $helper->ask($input, $output, $question);

            // Create a language for each selected language
            $this->createLanguageFromIsoKeyList($io, $languages);

            // Exit command
            return;

        }

        // Create new Language
        $language = new Language();

        // Get title
        $language->setTitle($input->getOption('title'));
        if(is_null($language->getTitle())) {
            $question = new Question('Language title ? ');
            $language->setTitle($helper->ask($input, $output, $question));
        }

        // Get isoKey
        $language->setIsoKey($input->getOption('isoKey'));
        if(is_null($language->getIsoKey())) {
            $question = new Question('Language isoKey ? ');
            $language->setIsoKey($helper->ask($input, $output, $question));
        }

        // Get flag
        $language->setFlag($input->getOption('flag'));
        if(is_null($language->getFlag())) {
            $question = new Question('Language flag path ? ');
            $language->setFlag($helper->ask($input, $output, $question));
        }

        // Get is default translation language
        $language->setDefaultTranslationLanguage(($input->getOption('defaultTranslationLanguage')) == 'true' ? true : false);
        if(is_null($input->getOption('defaultTranslationLanguage'))) {
            $question = new Question('Is language the default translation language ? ');
            $language->setDefaultTranslationLanguage(($helper->ask($input, $output, $question) == 'true') ? true : false);
        }

        // Verify if language already exist in database
        $languageExist = $em->getRepository('EasyAdminTranslationsBundle:Language')->findOneBy(['isoKey' => $language->getIsoKey()]);
        if(!is_null($languageExist)) {
            $io->error('The language with isoKey "' . $language->getIsoKey() . '" already exist in database. Please delete it first to create a new one.');
            return;
        }

        // Persist in database
        $em->persist($language);
        $em->flush();

        // Success !
        $io->success('The language with isoKey "' . $language->getIsoKey() . '" has been created successfully');

        // Exit command
        return;
    }

    /**
     * Create language from isoKeys list
     * @param SymfonyStyle $io
     * @param $isoKeys
     * @return boolean
     */
    private function createLanguageFromIsoKeyList(SymfonyStyle $io, $isoKeys) {

        // Get entity manager
        $em = $this->getContainer()->get('doctrine')->getManager();

        // Create a language corresponding to each iso key
        foreach ($isoKeys as $languageIsoKey) {

            // Verify if we know this isoKey and continue to next iteration otherwise
            $languageList = self::LANGUAGE_LIST;
            $flagsFilesList = self::FLAGS_FILES_LIST;
            if(!isset($languageList[$languageIsoKey]) || !isset($flagsFilesList[$languageIsoKey])) {
                $io->warning('No language found for isoKey: ' . $languageIsoKey . '. No language has been created for this isoKey.');
                continue;
            }

            // Verify if language already exist in database
            $language = $em->getRepository('EasyAdminTranslationsBundle:Language')->findOneBy(['isoKey' => $languageIsoKey]);
            if(!is_null($language)) {
                $io->warning('The language with isoKey "' . $languageIsoKey . '" already exist in database. Please delete it first to create a new one.');
                continue;
            }

            // Create language from our data
            $language = new Language();
            $languageData = self::LANGUAGE_LIST[$languageIsoKey];
            $language->setTitle($languageData['title']);
            $language->setIsoKey($languageData['isoKey']);
            $language->setFlag(self::FLAG_FILES_PATH . self::FLAGS_FILES_LIST[$languageIsoKey]);
            $language->setDefaultTranslationLanguage(false);
            $em->persist($language);
            $io->success('The language with isoKey "' . $languageIsoKey . '" has been created successfully');

        }

        // Commit the inserts
        $em->flush();

        // Everything is OK !
        return true;
    }

}
