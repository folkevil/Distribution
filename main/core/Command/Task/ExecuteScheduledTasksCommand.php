<?php

/*
 * This file is part of the Claroline Connect package.
 *
 * (c) Claroline Consortium <consortium@claroline.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Claroline\CoreBundle\Command\Task;

use Claroline\CoreBundle\Event\GenericDataEvent;
use Claroline\CoreBundle\Manager\Task\ScheduledTaskManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ExecuteScheduledTasksCommand extends Command
{
    private $taskManager;
    private $eventDispatcher;

    public function __construct(ScheduledTaskManager $taskManager, EventDispatcherInterface $eventDispatcher)
    {
        $this->taskManager = $taskManager;
        $this->eventDispatcher = $eventDispatcher;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Execute scheduled tasks with passed execution date');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Executing scheduled tasks...');
        $tasks = $this->taskManager->getTasksToExecute();

        foreach ($tasks as $task) {
            $output->writeln('['.$task->getType().'] '.$task->getName().' : Requesting execution...');
            $this->eventDispatcher->dispatch(
                new GenericDataEvent($task),
                'claroline_scheduled_task_execute_'.$task->getType()
            );
        }

        return 0;
    }
}
