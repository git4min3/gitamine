<?php

declare(strict_types=1);

namespace Gitamine\Deprecated\Core\Tests\Handler;

use Gitamine\Deprecated\Core\Command\InstallPluginCommand;
use Gitamine\Deprecated\Core\Command\RunPluginCommand;
use Gitamine\Deprecated\Core\Common\Test\TestCase;
use Gitamine\Deprecated\Core\Exception\PluginExecutionFailedException;
use Gitamine\Deprecated\Core\Command\Handler\RunPluginCommandHandler;
use Gitamine\Deprecated\Core\Query\GetProjectDirectoryQuery;
use Gitamine\Deprecated\Core\Test\GitamineMock;
use Gitamine\Deprecated\Core\Test\OutputMock;
use Gitamine\Deprecated\Core\Test\QueryBusMock;

/**
 * Class RunPluginCommandHandlerTest.
 *
 * @coversNothing
 */
class RunPluginCommandHandlerTest extends TestCase
{
    /**
     * @throws PluginExecutionFailedException
     */
    public function testShouldRunPluginVerboseZero(): void
    {
        $dir = '/';

        $bus      = QueryBusMock::create();
        $gitamine = GitamineMock::create();
        $output   = OutputMock::create();

        $bus->shouldDispatch(new GetProjectDirectoryQuery(), $dir);

        $gitamine->shouldPluginBeInstalled('test/test', 'master', true);
        $gitamine->shouldGetGithubPluginForPlugin('test', 'test/test', 'master');
        $gitamine->shouldGetOptionsForPlugin('test', 'pre-commit');
        $gitamine->shouldRunPlugin('test/test', 'pre-commit', 0, true);
        $gitamine->shouldGetProjectFolder('/');
        $gitamine->shouldGetConfiguration('/', [
            '_options' => [
                'verbose' => 0
            ]
        ]);

        $handler = new RunPluginCommandHandler($bus->bus(), $gitamine->gitamine(), $output->output());
        $handler(new RunPluginCommand('test', 'pre-commit'));
    }

    /**
     * @throws PluginExecutionFailedException
     */
    public function testShouldRunPluginVerboseOne(): void
    {
        $dir = '/';

        $bus      = QueryBusMock::create();
        $gitamine = GitamineMock::create();
        $output   = OutputMock::create();

        $bus->shouldDispatch(new GetProjectDirectoryQuery(), $dir);

        $gitamine->shouldPluginBeInstalled('test/test', 'master', true);
        $gitamine->shouldGetGithubPluginForPlugin('test', 'test/test', 'master');
        $gitamine->shouldGetOptionsForPlugin('test', 'pre-commit');
        $gitamine->shouldRunPlugin('test/test', 'pre-commit', 1, true);
        $gitamine->shouldGetProjectFolder('/');
        $gitamine->shouldGetConfiguration('/', [
            '_options' => [
                'verbose' => 1
            ]
        ]);

        $handler = new RunPluginCommandHandler($bus->bus(), $gitamine->gitamine(), $output->output());
        $handler(new RunPluginCommand('test', 'pre-commit'));
    }

    /**
     * @throws PluginExecutionFailedException
     */
    public function testShouldRunPluginVerboseTwo(): void
    {
        $dir = '/';

        $bus      = QueryBusMock::create();
        $gitamine = GitamineMock::create();
        $output   = OutputMock::create();

        $bus->shouldDispatch(new GetProjectDirectoryQuery(), $dir);

        $gitamine->shouldPluginBeInstalled('test/test', 'master', true);
        $gitamine->shouldGetGithubPluginForPlugin('test', 'test/test', 'master');
        $gitamine->shouldGetOptionsForPlugin('test', 'pre-commit');
        $gitamine->shouldRunPlugin('test/test', 'pre-commit', 2, true);
        $gitamine->shouldGetProjectFolder('/');
        $gitamine->shouldGetConfiguration('/', [
            '_options' => [
                'verbose' => 2
            ]
        ]);

        $handler = new RunPluginCommandHandler($bus->bus(), $gitamine->gitamine(), $output->output());
        $handler(new RunPluginCommand('test', 'pre-commit'));
    }

    /**
     * @throws PluginExecutionFailedException
     */
    public function testShouldThrowPluginExecutionFailedExceptionZero(): void
    {
        $this->expectException(PluginExecutionFailedException::class);

        $dir = '/';

        $bus      = QueryBusMock::create();
        $gitamine = GitamineMock::create();
        $output   = OutputMock::create();

        $bus->shouldDispatch(new GetProjectDirectoryQuery(), $dir);

        $gitamine->shouldPluginBeInstalled('test/test', 'master', true);
        $gitamine->shouldGetGithubPluginForPlugin('test', 'test/test', 'master');
        $gitamine->shouldGetOptionsForPlugin('test', 'pre-commit');
        $gitamine->shouldRunPlugin('test/test', 'pre-commit', 0, false);
        $gitamine->shouldGetProjectFolder('/');
        $gitamine->shouldGetConfiguration('/', ['_options' => ['verbose' => 0]]);

        $handler = new RunPluginCommandHandler($bus->bus(), $gitamine->gitamine(), $output->output());
        $handler(new RunPluginCommand('test', 'pre-commit'));
    }

    /**
     * @throws PluginExecutionFailedException
     */
    public function testShouldThrowPluginExecutionFailedExceptionOne(): void
    {
        $this->expectException(PluginExecutionFailedException::class);

        $dir = '/';

        $bus      = QueryBusMock::create();
        $gitamine = GitamineMock::create();
        $output   = OutputMock::create();

        $bus->shouldDispatch(new GetProjectDirectoryQuery(), $dir);

        $gitamine->shouldPluginBeInstalled('test/test', 'master', true);
        $gitamine->shouldGetGithubPluginForPlugin('test', 'test/test', 'master');
        $gitamine->shouldGetOptionsForPlugin('test', 'pre-commit');
        $gitamine->shouldRunPlugin('test/test', 'pre-commit', 1, false);
        $gitamine->shouldGetProjectFolder('/');
        $gitamine->shouldGetConfiguration('/', ['_options' => ['verbose' => 1]]);

        $handler = new RunPluginCommandHandler($bus->bus(), $gitamine->gitamine(), $output->output());
        $handler(new RunPluginCommand('test', 'pre-commit'));
    }

    /**
     * @throws PluginExecutionFailedException
     */
    public function testShouldThrowPluginExecutionFailedExceptionTwo(): void
    {
        $this->expectException(PluginExecutionFailedException::class);

        $dir = '/';

        $bus      = QueryBusMock::create();
        $gitamine = GitamineMock::create();
        $output   = OutputMock::create();

        $bus->shouldDispatch(new GetProjectDirectoryQuery(), $dir);

        $gitamine->shouldPluginBeInstalled('test/test', 'master', true);
        $gitamine->shouldGetGithubPluginForPlugin('test', 'test/test', 'master');
        $gitamine->shouldGetOptionsForPlugin('test', 'pre-commit');
        $gitamine->shouldRunPlugin('test/test', 'pre-commit', 2, false);
        $gitamine->shouldGetProjectFolder('/');
        $gitamine->shouldGetConfiguration('/', ['_options' => ['verbose' => 2]]);

        $handler = new RunPluginCommandHandler($bus->bus(), $gitamine->gitamine(), $output->output());
        $handler(new RunPluginCommand('test', 'pre-commit'));
    }

    /**
     * @throws PluginExecutionFailedException
     */
    public function testShouldInstallUninstalledPlugins(): void
    {
        $this->expectException(PluginExecutionFailedException::class);

        $dir = '/';

        $bus      = QueryBusMock::create();
        $gitamine = GitamineMock::create();
        $output   = OutputMock::create();

        $bus->shouldDispatch(new GetProjectDirectoryQuery(), $dir);

        $gitamine->shouldPluginBeInstalled('test/test', 'master', false);
        $gitamine->shouldGetGithubPluginForPlugin('test', 'test/test', 'master');
        $gitamine->shouldGetOptionsForPlugin('test', 'pre-commit');
        $gitamine->shouldRunPlugin('test/test', 'pre-commit', 0, false);
        $gitamine->shouldGetProjectFolder('/');
        $gitamine->shouldGetConfiguration('/', ['_options' => ['verbose' => 0]]);
        $bus->shouldDispatch(new InstallPluginCommand('test/test', 'master'), '');

        $handler = new RunPluginCommandHandler($bus->bus(), $gitamine->gitamine(), $output->output());
        $handler(new RunPluginCommand('test', 'pre-commit'));
    }
}
