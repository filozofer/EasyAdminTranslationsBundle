<?php

namespace EasyAdminTranslationsBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Style\SymfonyStyle;

class LanguageListCommand extends ContainerAwareCommand {

    /**
     * Command configuration
     */
    protected function configure()
    {
        $this
            ->setName('eatb:language:list')
            ->setDescription('List of the declare language')
        ;
    }

    /**
     * Command execution: List the declare language
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Get entity manager
        $em = $this->getContainer()->get('doctrine')->getManager();

        // Get language from database
        $languages = $em->getRepository('EasyAdminTranslationsBundle:Language')->findAll();

        // Symfony ouput component
        $io = new SymfonyStyle($input, $output);

        // Display list of language
        $io->title('List of declare language');
        $io->table(['Title', 'IsoKey', 'Default'], array_map(function($language){
            return [$language->getTitle(), $language->getIsoKey(), ($language->isDefaultTranslationLanguage()) ? 'Yes' : 'No'];
        }, $languages));

        // Exit command
        return;
    }

}
