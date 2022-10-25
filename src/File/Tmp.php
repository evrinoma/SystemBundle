<?php

declare(strict_types=1);

namespace Evrinoma\SystemBundle\File;

use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Finder\Finder;

final class Tmp
{
    private string $file;

    private string $path;

    private string $name;

    private ?\Closure $handler = null;

    public function __construct(string $name, string $folderName = null, bool $clean = true, bool $typeTemp = false)
    {
        $this->name = $name;
        $this->path = sys_get_temp_dir() . ((null === $folderName) ? '' : DIRECTORY_SEPARATOR . $folderName);
        $filesystem = new Filesystem();
        if (!$filesystem->exists($this->path)) {
            try {
                $filesystem->mkdir(Path::normalize($this->path));
            } catch (IOExceptionInterface $exception) {
                throw new \Exception('An error occurred while creating your directory at ' . $exception->getPath());
            }
        }

        $this->file = ($typeTemp)
            ? tempnam($this->path, $name) : $this->path . DIRECTORY_SEPARATOR . $name;

        if ($typeTemp) {
            if (false === $this->file) {
                throw new \RuntimeException("Couldn't create a file.");
            }
        }
        if ($clean) {
            $this->registerShutdown();
        }
    }

    public function cleanTempFolder(string $search, string $scanFolder, array $exclude): void
    {
        $finder = new Finder();

        try {
            $finder->name($search)->exclude($exclude)->directories()->in($scanFolder);
            $finder->hasResults();
            $files = [];
            foreach ($finder as $dir) {
                $files[]=$dir->getRealPath();
            }
            foreach ($files as $dir) {
                $this->rm($dir);
            }
        } catch (IOExceptionInterface $exception) {
            throw new \Exception('An error occurred while deleting your directories '.$exception->getPath());
        }
    }

    private function rm(string $path): void
    {
        if (sys_get_temp_dir() !== $path) {
            if (is_dir($path)) {
                $handle = opendir($path);
                while (false !== ($entry = readdir($handle))) {
                    if ($entry != "." && $entry != "..") {
                        if (file_exists($path.DIRECTORY_SEPARATOR.$entry)) {
                            unlink($path.DIRECTORY_SEPARATOR.$entry);
                        }
                    }
                }
                closedir($handle);
                rmdir($path);
            }
        }
    }

    private function registerShutdown(): void
    {
        $this->handler = static function (string $filename, string $path): void {
            if (file_exists($filename)) {
                unlink($filename);
            }
            if (sys_get_temp_dir() !== $path) {
                if (is_dir($path)) {
                    $handle = opendir($path);
                    while (false !== ($entry = readdir($handle))) {
                        if ($entry != "." && $entry != "..") {
                            closedir($handle);

                            return;
                        }
                    }
                    closedir($handle);
                    rmdir($path);
                }
            }
        };

        register_shutdown_function($this->handler, $this->file, $this->path);
    }

    public function getAbsolutePath(): string
    {
        return $this->path;
    }

    public function getFile(): string
    {
        return $this->file;
    }

    public function __toString(): string
    {
        return $this->file;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function __destruct()
    {
        if (null !== $this->handler) {
            ($this->handler)($this->file, $this->path);
        }
    }
}