<?php

declare(strict_types=1);

namespace App\SubversionRepository;

use Gitamine\Deprecated\Core\Domain\Directory;
use Gitamine\Deprecated\Core\Domain\File;
use Gitamine\Deprecated\Core\Exception\InvalidSubversionDirectoryException;
use Gitamine\Deprecated\Core\Infrastructure\SubversionRepository;

/**
 * Class GitRepository.
 */
class GitRepository implements SubversionRepository
{
    private const GIT_ROOT         = 'git rev-parse --show-toplevel';
    private const GIT_ADDED        = 'git diff --cached --name-status | awk \'$1 == "A" { print $2 }\'';
    private const GIT_MODIFIED     = 'git diff --cached --name-status | awk \'$1 == "M" { print $2 }\'';
    private const GIT_DELETED      = 'git diff --cached --name-status | awk \'$1 == "D" { print $2 }\'';
    private const GIT_BRANCH       = 'git rev-parse --abbrev-ref HEAD';
    private const GIT_BRANCH_FILES = 'git whatchanged --name-only --pretty="" %s..%s | tee';

    /**
     * @param Directory $dir
     *
     * @return bool
     */
    public function isValidSubversionFolder(Directory $dir): bool
    {
        try {
            $this->run($dir, 'git status');

            return true;
        } catch (InvalidSubversionDirectoryException $e) {
            return false;
        }
    }

    /**
     * @param Directory $dir
     *
     * @throws InvalidSubversionDirectoryException
     *
     * @return Directory
     */
    public function getRootDir(Directory $dir): Directory
    {
        return new Directory($this->run($dir, self::GIT_ROOT)[0]);
    }

    /**
     * @param Directory $dir
     *
     * @throws InvalidSubversionDirectoryException
     *
     * @return array
     */
    public function getNewFiles(Directory $dir): array
    {
        return $this->run($this->getRootDir($dir), self::GIT_ADDED);
    }

    /**
     * @param Directory $dir
     *
     * @throws InvalidSubversionDirectoryException
     *
     * @return array
     */
    public function getUpdatedFiles(Directory $dir): array
    {
        return $this->run($this->getRootDir($dir), self::GIT_MODIFIED);
    }

    /**
     * @param Directory $dir
     *
     * @throws InvalidSubversionDirectoryException
     *
     * @return array
     */
    public function getDeletedFiles(Directory $dir): array
    {
        return $this->run($this->getRootDir($dir), self::GIT_DELETED);
    }

    /**
     * @param Directory $dir
     *
     * @throws InvalidSubversionDirectoryException
     *
     * @return array
     */
    public function getBranchName(Directory $dir): array
    {
        return $this->run($this->getRootDir($dir), self::GIT_BRANCH);
    }

    /**
     * @param string $source
     * @param string $destiny
     *
     * @throws InvalidSubversionDirectoryException
     *
     * @return File[]
     */
    public function getFilesModifiedOnBranch(Directory $dir, string $source, string $destiny): array
    {
        $dir = $this->getRootDir($dir);
        $out = $this->run($dir, \sprintf(self::GIT_BRANCH_FILES, $source, $destiny));

        $rawFiles = \explode('\n', $out);
        $ret      = [];
        foreach ($rawFiles as $rawFile) {
            $ret[] = $dir->openFile($rawFile);
        }

        return $ret;
    }

    /**
     * @param Directory $dir
     * @param string    $command
     *
     * @throws InvalidSubversionDirectoryException
     *
     * @return array
     */
    private function run(Directory $dir, string $command): array
    {
        $error  = 0;
        $output = [];

        \exec(\sprintf('cd %s 2> /dev/null ; %s 2> /dev/null', $dir->dir(), $command), $output, $error);

        if ($error) {
            throw new InvalidSubversionDirectoryException($dir);
        }

        return $output;
    }
}
