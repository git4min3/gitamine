<?php

declare(strict_types=1);

namespace Gitamine\Deprecated\Core\Query\Handler;

use Gitamine\Deprecated\Core\Domain\Directory;
use Gitamine\Deprecated\Core\Exception\InvalidSubversionDirectoryException;
use Gitamine\Deprecated\Core\Infrastructure\GitamineConfig;
use Gitamine\Deprecated\Core\Infrastructure\SubversionRepository;
use Gitamine\Deprecated\Core\Query\GetGitamineConfigurationQuery;

/**
 * Class GetGitamineConfigurationQueryHandler.
 */
class GetGitamineConfigurationQueryHandler
{
    /**
     * @var SubversionRepository
     */
    private $repository;

    /**
     * @var GitamineConfig
     */
    private $gitamine;

    /**
     * GetGitamineDirectoryQueryHandler constructor.
     *
     * @param SubversionRepository $repository
     * @param GitamineConfig       $gitamine
     */
    public function __construct(SubversionRepository $repository, GitamineConfig $gitamine)
    {
        $this->gitamine   = $gitamine;
        $this->repository = $repository;
    }

    /**
     * @param GetGitamineConfigurationQuery $query
     *
     * @throws InvalidSubversionDirectoryException
     *
     * @return array
     */
    public function __invoke(GetGitamineConfigurationQuery $query): array
    {
        $dir = new Directory($query->dir());

        if (!$this->repository->isValidSubversionFolder($dir)) {
            throw new InvalidSubversionDirectoryException($dir);
        }

        $root = $this->repository->getRootDir($dir);

        return $this->gitamine->getConfiguration($root);

        /*if (!is_file($root->dir() . '/gitamine.yaml')) {
            throw new MissingGitamineConfigurationFileException('Missign .gitanime/gitamine.yaml file in root folder');
        }*/
    }
}
