<?php

namespace Blueways\BwTodo\Controller;

use Blueways\BwTodo\Domain\Model\Profile;
use Blueways\BwTodo\Domain\Repository\ProfileRepository;
use Blueways\BwTodo\Mvc\View\JsonView;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

class ProfileController extends ActionController
{
    protected $defaultViewObjectName = JsonView::class;

    protected PersistenceManager $persistenceManager;

    protected ProfileRepository $profileRepository;

    private LoggerInterface $logger;

    public function __construct(
        ProfileRepository $profileRepository,
        PersistenceManager $persistenceManager,
        LoggerInterface $logger
    ) {
        $this->profileRepository = $profileRepository;
        $this->persistenceManager = $persistenceManager;
        $this->logger = $logger;
    }

    public function indexAction(): ResponseInterface
    {
        $method = $this->request->getMethod();

        if ($method === 'GET') {
            return $this->list();
        }

        if ($method === 'POST') {
            return $this->create();
        }

        return $this->jsonResponse('[]')->withStatus(405, 'nope');
    }

    public function detailAction(Profile $profile): ResponseInterface
    {
        $method = $this->request->getMethod();

        if ($method === 'GET') {
            return $this->show($profile);
        }

        if ($method === 'PATCH') {
            return $this->update($profile);
        }

        if ($method === 'DELETE') {
            return $this->delete($profile);
        }

        return $this->jsonResponse()->withStatus(405);
    }

    protected function list(): ResponseInterface
    {
        $profiles = $this->profileRepository->findAll();
        $this->view->setVariablesToRender(['profiles']);
        $this->view->assign('profiles', $profiles);
        return $this->jsonResponse();
    }

    protected function create(): ResponseInterface
    {
        $profile = Profile::createFromRequest($this->request);

        try {
            $this->profileRepository->add($profile);
            $this->persistenceManager->persistAll();
        } catch (\Exception $e) {
            $this->logger->error('Could not create profile', $e->getTrace());
            return $this->jsonResponse('{}')->withStatus(500);
        }

        return $this->show($profile);
    }

    protected function update(Profile $profile): ResponseInterface
    {
        $profile->updateFromRequest($this->request);

        try {
            $this->profileRepository->update($profile);
            $this->persistenceManager->persistAll();
        } catch (\Exception $e) {
            $this->logger->error('Could not update profile', $e->getTrace());
            return $this->jsonResponse('{}')->withStatus(500);
        }

        return $this->show($profile);
    }

    protected function show(Profile $profile): ResponseInterface
    {
        $this->view->setVariablesToRender(['profile']);
        $this->view->assign('profile', $profile);
        return $this->jsonResponse();
    }

    protected function delete(Profile $profile): ResponseInterface
    {
        try {
            $this->profileRepository->remove($profile);
            $this->persistenceManager->persistAll();
        } catch (\Exception $e) {
            $this->logger->error('Could not delete profile', $e->getTrace());
            return $this->jsonResponse()->withStatus(500);
        }

        return $this->jsonResponse();
    }
}
