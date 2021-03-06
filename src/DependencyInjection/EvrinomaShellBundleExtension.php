<?php


namespace Evrinoma\ShellBundle\DependencyInjection;

use Evrinoma\ShellBundle\EvrinomaShellBundle;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Class EvrinomaShellBundleExtension
 *
 * @package Evrinoma\ShellBundle\DependencyInjection
 */
class EvrinomaShellBundleExtension extends Extension
{
//region SECTION: Public
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
//endregion Public

//region SECTION: Getters/Setters
    public function getAlias()
    {
        return EvrinomaShellBundle::SHELL_BUNDLE;
    }
//endregion Getters/Setters
}