<?php

namespace Sba\DirectusBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

class DirectusSymfonyExtension extends ConfigurableExtension
{
    public function loadInternal(array $mergedConfigs, ContainerBuilder $container)
    {
        $yamlLoader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $yamlLoader->load('services.yaml');

        $container->setParameter('sba_directus_symfony.base_url', $mergedConfigs['base_url']);
        $container->setParameter('sba_directus_symfony.project_name', $mergedConfigs['project_name'] ?? null);
        $container->setParameter('sba_directus_symfony.authentication.email', $mergedConfigs['authentication']['email']);
        $container->setParameter('sba_directus_symfony.authentication.password', $mergedConfigs['authentication']['password']);
        $container->setParameter('sba_directus_symfony.authentication.mode', $mergedConfigs['authentication']['mode']);
    }
}
