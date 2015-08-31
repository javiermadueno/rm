<?php

namespace RM\LdapBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class LdapExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

    }

    public function prepend(ContainerBuilder $container)
    {
        //Se utiliza la misma configuracion que IMAG\LdapBundle
        //$ldap_config = $container->getExtensionConfig('imag_ldap.connection.params');
        //$container->prependExtensionConfig($this->getAlias(), $ldap_config);

        //$configs = $container->getExtensionConfig($this->getAlias());
        //$config = $this->processConfiguration(new Configuration(), $configs);

    }

}
