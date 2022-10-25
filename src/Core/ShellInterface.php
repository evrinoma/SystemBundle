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

namespace Evrinoma\ShellBundle\Core;

interface ShellInterface
{
    public function rfts($strFileName, $intLines = 0, $intBytes = 4096, $booErrorRep = true): bool;

    public function executeProgram($programName, $args = '', $bootErrorRep = true): bool;

    public function toArrayString(): array;

    public function toUtf8Size(): array;

    public function hasProgram(string $programName): bool;

    /**
     * @param string $programName
     *
     * @return string
     *
     * @throws \Exception
     */
    public function getProgram(string $programName): string;

    public function getError();

    public function getResult();

    public function setClean(): ShellInterface;
}
