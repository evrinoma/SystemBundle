<?php

declare(strict_types=1);

/*
 * This file is part of the package.
 *
 * (c) Nikolay Nikolaev <evrinoma@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Evrinoma\SystemBundle;

use Evrinoma\SystemBundle\DependencyInjection\EvrinomaSystemBundleExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class EvrinomaSystemBundle extends Bundle
{
    public const SHELL_BUNDLE = 'shell';

    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new EvrinomaSystemBundleExtension();
        }

        return $this->extension;
    }
}
