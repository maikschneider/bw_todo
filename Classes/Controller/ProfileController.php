<?php

namespace Blueways\BwTodo\Controller;

use Blueways\BwTodo\Domain\Model\Profile;
use Blueways\BwTodo\Domain\Repository\ProfileRepository;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class ProfileController extends ActionController
{

    protected $defaultViewObjectName = \TYPO3\CMS\Extbase\Mvc\View\JsonView::class;

    protected ProfileRepository $profileRepository;

    public function __construct(ProfileRepository $profileRepository)
    {
        $this->profileRepository = $profileRepository;
    }

    public function listAction(): ResponseInterface
    {
        $profiles = $this->profileRepository->findAll();
        $this->view->setConfiguration([
            'profiles' => [
                '_descendAll' => [
                    '_only' => ['name', 'uid', 'tasks', 'title', 'description', 'dueDate'],
                    '_recursive' => ['tasks'],
                ]
            ]
        ]);
        $this->view->setVariablesToRender(['profiles']);
        $this->view->assign('profiles', $profiles);
        return $this->jsonResponse();
    }

    public function showAction(Profile $profile): ResponseInterface
    {
        $this->view->setConfiguration([
            'profile' => [
                '_only' => ['name', 'uid', 'tasks', 'title', 'description', 'dueDate'],
                '_recursive' => ['tasks'],
                '_descendAll' => []
            ]
        ]);
        $this->view->setVariablesToRender(['profile']);
        $this->view->assign('profile', $profile);
        return $this->jsonResponse();
    }

}
