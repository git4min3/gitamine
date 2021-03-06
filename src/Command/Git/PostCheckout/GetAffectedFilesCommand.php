<?php

declare(strict_types=1);

namespace App\Command\Git\PostCheckout;

use App\Prooph\QueryBus;
use Gitamine\Deprecated\Core\Exception\InvalidSubversionDirectoryException;
use Gitamine\Git\PostCheckout\Query\GetAffectedFiles;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class GetAffectedFilesCommand.
 */
final class GetAffectedFilesCommand extends ContainerAwareCommand
{
    /**
     * @var QueryBus;
     */
    private $bus;

    /**
     * GetAffectedBranchesCommand constructor.
     *
     * @param QueryBus $bus
     */
    public function __construct(QueryBus $bus)
    {
        parent::__construct();

        $this->bus = $bus;
    }

    protected function configure(): void
    {
        $this
            ->setName('git:post-checkout:files')
            ->setDescription('Returns a list of files that have differied from the given branch')
            ->addArgument(
                'source-branch',
                InputArgument::OPTIONAL,
                'Reference branch'
            )
            ->addArgument(
                'status',
                InputArgument::OPTIONAL,
                'A = Added, M = Modified, D = Deleted (you can combine them, ie AM for added and modified)',
                'AM'
            )
            ->addArgument('join', InputArgument::OPTIONAL, 'How to join the files', "\n")
            ->addArgument('filter', InputArgument::OPTIONAL, 'Reference branch', '.*');
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $branch    = $input->getArgument('source-branch');
        $filter    = $input->getArgument('filter');
        $status    = $input->getArgument('status');
        $join      = $input->getArgument('join');

        try {
            /** @var string[] $files */
            $files = $this->bus->dispatch(new GetAffectedFiles($branch, $status, $filter));

            $output->writeln(\implode($join, $files));
        } catch (InvalidSubversionDirectoryException $e) {
            $output->writeln("<error>{$e->getMessage()}</error>");

            return $e->getCode();
        }

        return 0;
    }
}
