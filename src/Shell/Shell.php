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

namespace Evrinoma\SystemBundle\Shell;

class Shell implements ShellInterface
{
    /**
     * @var
     */
    protected $error;
    /**
     * @var
     */
    protected $result;
    /**
     * @var array
     */
    protected $programs = [];
    /**
     * @var array
     */
    private $paths = ['/bin', '/sbin', '/usr/bin', '/usr/sbin', '/usr/local/bin', '/usr/local/sbin'];

    /**
     * @param      $strFileName
     * @param int  $intLines
     * @param int  $intBytes
     * @param bool $booErrorRep
     *
     * @return bool
     */
    public function rfts($strFileName, $intLines = 0, $intBytes = 4096, $booErrorRep = true): bool
    {
        $this->setClean();
        $intCurLine = 1;

        if (file_exists($strFileName)) {
            if ($fd = fopen($strFileName, 'r')) {
                while (!feof($fd)) {
                    $this->result .= fgets($fd, $intBytes);
                    if ($intLines <= $intCurLine && 0 !== $intLines) {
                        break;
                    } else {
                        $intCurLine++;
                    }
                }
                fclose($fd);
            } else {
                if ($booErrorRep) {
                    $this->error = 'fopen('.$strFileName.') file can not read by phpsysinfo';
                }

                return false;
            }
        } else {
            if ($booErrorRep) {
                $this->error = 'fopen('.$strFileName.') the file does not exist on your machine';
            }

            return false;
        }

        return true;
    }

    /**
     * @param        $programName
     * @param string $args
     * @param bool   $bootErrorRep
     *
     * @return bool
     */
    public function executeProgram($programName, $args = '', $bootErrorRep = true): bool
    {
        $this->setClean();

        $buffer = '';
        $program = $this->findProgram($programName);

        if (!$program) {
            if ($bootErrorRep) {
                $this->error = 'findProgram('.$programName.') program not found on the machine';
            }

            return false;
        }

        // see if we've gotten a |, if we have we need to do patch checking on the cmd
        if ($args) {
            $args_list = preg_split('/\s/', $args);
            $max = \count($args_list);
            for ($i = 0; $i < $max; $i++) {
                if ('|' === $args_list[$i]) {
                    $cmd = $args_list[$i + 1];
                    $new_cmd = $this->findProgram($cmd);
                    $args = preg_replace("/\| $cmd/", "| $new_cmd", $args);
                }
            }
        }
        // we've finally got a good cmd line.. execute it
        if ($fp = popen("($program $args > /dev/null) 3>&1 1>&2 2>&3", 'r')) {
            while (!feof($fp)) {
                $buffer .= fgets($fp, 4096);
            }
            pclose($fp);
            $buffer = trim($buffer);
            if (!empty($buffer)) {
                if ($bootErrorRep) {
                    $this->error = 'findProgram('.$program.') program not found on the machine';

                    return false;
                }
            }
        }
        if ($fp = popen("$program $args", 'r')) {
            $buffer = '';
            while (!feof($fp)) {
                $buffer .= fgets($fp, 4096);
            }
            pclose($fp);
        }
        $this->result = trim($buffer);

        return true;
    }

    /**
     * @return array
     */
    public function toArrayString(): array
    {
        return explode("\n", $this->getResult());
    }

    /**
     * Got to exit, if executable program have't a valid path.
     *
     * @param string $programName
     *
     * @return bool
     */
    public function hasProgram(string $programName): bool
    {
        return \array_key_exists($programName, $this->programs) && '' !== $this->programs[$programName];
    }

    public function toUtf8size(): array
    {
        $encode = [];

        foreach ($this->result as $item) {
            $encode[] = utf8_encode($item);
        }

        return $encode;
    }

    /**
     * @param $program
     *
     * @return string|null
     */
    private function findProgram($program): ?string
    {
        reset($this->paths);
        if (\function_exists('is_executable')) {
            while ($this_path = current($this->paths)) {
                if (is_executable("$this_path/$program")) {
                    return "$this_path/$program";
                }
                next($this->paths);
            }
        }

        return null;
    }

    /**
     * @param string $programName
     *
     * @return string
     *
     * @throws \Exception
     */
    public function getProgram(string $programName): string
    {
        if (!$this->programs[$programName]) {
            $this->programs[$programName] = $this->findProgram($programName);
        }
        if (!$this->programs[$programName]) {
            throw new \Exception('Shell program ['.$programName.'] does\'t exist');
        }

        return $this->programs[$programName];
    }

    /**
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @return mixed
     */
    public function setClean(): ShellInterface
    {
        $this->result = '';
        $this->error = '';

        return $this;
    }
}
