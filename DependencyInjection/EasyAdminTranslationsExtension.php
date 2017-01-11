<?php

namespace EasyAdminTranslationsBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Modify configuration on container compilation
 */
class EasyAdminTranslationsExtension extends Extension
{

    /**
     * Load bundle services & modify easyadmin config
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        // Load this bundle services
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        // Get EasyAdmin configuration
        $easyAdminConfig = $container->getParameter('easyadmin.config');

        // Alter EasyAdmin configurations to add are own configurations
        $easyAdminConfig['design']['assets']['css'][] = 'bundles/easyadmintranslations/stylesheets/easyadmintranslations-all.css';
        $easyAdminConfig['design']['assets']['css'][] = 'eatb/api/hide-easyadmin-menu-in-iframe.css';
        $easyAdminConfig['design']['assets']['js'][]  = 'bundles/easyadmintranslations/javascripts/iframe-form-redirect-to-edit-form.js';
        $easyAdminConfig['design']['assets']['js'][]  = 'bundles/easyadmintranslations/javascripts/send-event-back-to-parent.js';
        $easyAdminConfig['design']['form_theme'][] = '@EasyAdminTranslations/fields/fields.html.twig';

        // Set new altered configurations
        $container->setParameter('easyadmin.config', $easyAdminConfig);
    }

}
