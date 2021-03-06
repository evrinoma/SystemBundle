<?php


namespace Evrinoma\ShellBundle;

use Evrinoma\ShellBundle\DependencyInjection\EvrinomaShellBundleExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class EvrinomaShellBundle extends Bundle
{
    public const SHELL_BUNDLE = 'shell';

    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new EvrinomaShellBundleExtension();
        }
        return $this->extension;
    }
}