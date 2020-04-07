<?php


namespace Evrinoma\ShellBundle\Core;


interface ShellInterface
{
//region SECTION: Public
    public function rfts($strFileName, $intLines = 0, $intBytes = 4096, $booErrorRep = true): bool;

    public function executeProgram($programName, $args = '', $bootErrorRep = true): bool;

    public function toArrayString(): array;

    public function toUtf8Size(): array;

    public function hasProgram(string $programName): bool;
//endregion Public

//region SECTION: Getters/Setters
    /**
     * @param string $programName
     *
     * @return string
     * @throws \Exception
     */
    public function getProgram(string $programName): string;

    public function getError();

    public function getResult();

    public function setClean(): ShellInterface;
//endregion Getters/Setters
}