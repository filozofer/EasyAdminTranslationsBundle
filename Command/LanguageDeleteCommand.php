<?php

namespace EasyAdminTranslationsBundle\Command;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

class LanguageDeleteCommand extends ContainerAwareCommand {

    /**
     * Command configuration
     */
    protected function configure()
    {
        $this
            ->setName('eatb:language:delete')
            ->setDescription('Allow to delete language from command line')
            ->addOption('title', null, InputOption::VALUE_OPTIONAL, 'Language title to delete')
            ->addOption('isoKey', null, InputOption::VALUE_OPTIONAL, 'Language isoKey to delete')
            ->addOption('all', null, InputOption::VALUE_NONE, 'Delete all language ?')
            ->addOption('force', null, InputOption::VALUE_NONE, 'Do not ask for confirmation before deleting language(s)')
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
        // Get entity manager & declare output component & helper
        $em = $this->getContainer()->get('doctrine')->getManager();
        $io = new SymfonyStyle($input, $output);
        $helper = $this->getHelper('question');

        // Title command
        $io->title('Delete danguage');

        // Try to delete one language using one of the criteria set by user
        $deleteByFields = ['title', 'isoKey'];
        foreach ($deleteByFields as $fieldName) {

            // Delete language by $fieldName
            if(!is_null($input->getOption($fieldName))) {

                // Try to get language in database
                $language = $em->getRepository('EasyAdminTranslationsBundle:Language')->findOneBy([$fieldName => $input->getOption($fieldName)]);

                // Error if there is no language for this $fieldName
                if(is_null($language)) {
                    $io->error('No language found in database with ' . $fieldName . ': ' . $input->getOption($fieldName));
                    return;
                }

                // Ask user before doing bad thing
                $question = new ConfirmationQuestion('This command will delete the language with ' . $fieldName . '="' . $input->getOption($fieldName) . '". Do you confirm your choice ? (y/N)', false);
                if (!$input->getOption('force') && !$helper->ask($input, $output, $question)) {
                    $io->error('Exit command on your choice.');
                    return;
                }

                // Delete language
                $em->remove($language);
                $em->flush();

                // Tell user we succeed and finish
                $io->success('The language with ' . $fieldName . ' "' . $input->getOption($fieldName) . '" has been deleted successfully.');
                return;

            }

        }

        // User want to delete all language ?
        if($input->getOption('all')) {

            // Ask user before doing bad thing
            $question = new ConfirmationQuestion('This command will delete all languages. Do you confirm your choice ? (y/N)', false);
            if (!$input->getOption('force') && !$helper->ask($input, $output, $question)) {
                $io->error('Exit command on your choice.');
                return;
            }

            // Get all languages from database
            $languages = $em->getRepository('EasyAdminTranslationsBundle:Language')->findAll();

            // Remove them all
            foreach ($languages as $language) {
                $em->remove($language);
            }
            $em->flush();

            // Success message & exit
            $io->success('All languages have been deleted successfully.');
            return;

        }

        // If we are here it's because user did not provied any of the criteria
        $io->error('No criteria defined to delete Language. Please refer to the command documentation (--help).');

        // Exit command
        return;
    }

}
