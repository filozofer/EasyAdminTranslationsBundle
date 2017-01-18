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

class LanguageSetDefaultCommand extends ContainerAwareCommand {

    /**
     * Command configuration
     */
    protected function configure()
    {
        $this
            ->setName('eatb:language:default')
            ->setDescription('Allow to set the default translation language in command line')
            ->addArgument('isoKey', InputArgument::REQUIRED, 'Language isoKey to set as default translation language')
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

        // Print command title
        $io->title('Set default translation language');

        // Verify if language exist in database
        $language = $em->getRepository('EasyAdminTranslationsBundle:Language')->findOneBy(['isoKey' => $input->getArgument('isoKey')]);
        if(is_null($language)) {
            $io->error('The language with isoKey "' . $language->getIsoKey() . '" does not exist in database.');
            return;
        }

        // Update which language is the default one
        $allLanguage = $em->getRepository('EasyAdminTranslationsBundle:Language')->findAll();
        foreach ($allLanguage as $lang) {
            $lang->setDefaultTranslationLanguage(false);
            $em->persist($lang);
        }
        $em->flush();
        $language->setDefaultTranslationLanguage(true);
        $em->persist($language);
        $em->flush();

        // Success !
        $io->success('The language with isoKey "' . $language->getIsoKey() . '" is now the default translation language.');

        // Exit command
        return;
    }



}
