<?php

namespace Blueways\BwTodo\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class TaskController extends ActionController
{

    public function listAction(): ResponseInterface
    {
        return $this->jsonResponse('{}');
    }

}
