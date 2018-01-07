<?php
declare(strict_types=1);

namespace Gitamine\Git\Test;

use Gitamine\Git\Infrastructure\Git;
use Mockery;
use Mockery\MockInterface;

/**
 * Class GitMock
 *
 * @package Gitamine\Git\Test
 */
class GitMock
{
    /**
     * @var MockInterface
     */
    private $git;

    public function __construct()
    {
        $this->git = Mockery::mock(Git::class);
    }

    /**
     * @return MockInterface|Git
     */
    public function git(): Git
    {
        return $this->git;
    }

}
