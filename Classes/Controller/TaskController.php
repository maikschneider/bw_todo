<?php

namespace Blueways\BwTodo\Controller;

use Blueways\BwTodo\Domain\Repository\ProfileRepository;
use Blueways\BwTodo\Domain\Repository\TaskRepository;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class TaskController extends ActionController
{

    protected $defaultViewObjectName = \TYPO3\CMS\Extbase\Mvc\View\JsonView::class;

    protected TaskRepository $taskRepository;

    public function __construct(TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

}
