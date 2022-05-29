<?php

namespace Blueways\BwTodoSite\Controller;

use Blueways\BwTodoSite\Domain\Model\Profile;
use Blueways\BwTodoSite\Domain\Model\Task;
use Blueways\BwTodoSite\Domain\Repository\TaskRepository;
use Blueways\BwTodoSite\Mvc\View\JsonView;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

class TaskController extends ActionController
{
    protected $defaultViewObjectName = JsonView::class;

    protected PersistenceManager $persistenceManager;

    protected TaskRepository $taskRepository;

    public function __construct(TaskRepository $taskRepository, PersistenceManager $persistenceManager)
    {
        $this->taskRepository = $taskRepository;
        $this->persistenceManager = $persistenceManager;
    }

    protected function createAction(Profile $profile): ResponseInterface
    {
        $task = Task::createFromRequest($this->request, $profile);

        try {
            $this->taskRepository->add($task);
            $this->persistenceManager->persistAll();
        } catch (\Exception $e) {
            return $this->jsonResponse()->withStatus(500);
        }

        $this->view->setVariablesToRender(['task']);
        $this->view->assign('task', $task);
        return $this->jsonResponse();
    }

    protected function deleteAction(Task $task): ResponseInterface
    {
        try {
            $this->taskRepository->remove($task);
            $this->persistenceManager->persistAll();
        } catch (\Exception $e) {
            return $this->jsonResponse()->withStatus(500);
        }

        return $this->jsonResponse('{}');
    }
}
