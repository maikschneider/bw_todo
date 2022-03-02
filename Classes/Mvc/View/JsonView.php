<?php

namespace Blueways\BwTodo\Mvc\View;

use Blueways\BwTodo\Domain\Model\Task;
use TYPO3\CMS\Extbase\Mvc\View\JsonView as ExtbaseJsonView;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class JsonView extends ExtbaseJsonView
{

    protected $configuration = [
        'profiles' => [
            '_descendAll' => [
                '_only' => ['name', 'uid', 'tasks', 'title', 'description', 'dueDate'],
                '_recursive' => ['tasks'],
                '_descend' => [
                    'dueDate' => []
                ]
            ]
        ],
        'profile' => [
            '_only' => ['name', 'uid', 'tasks', 'title', 'description', 'dueDate'],
            '_recursive' => ['tasks', 'dueDate'],
            '_descendAll' => [
                '_descend' => [
                    'dueDate' => []
                ]
            ],
            '_descend' => [
                'dueDate'
            ]
        ],
        'task' => [
            '_only' => ['title', 'uid', 'description', 'dueDate'],
            '_recursive' => ['tasks', 'dueDate'],
            '_descend' => [
                'dueDate'
            ]
        ]
    ];

    protected function transformValue(mixed $value, array $configuration, $firstLevel = false)
    {
        if ($value instanceof ObjectStorage) {
            $value = $value->toArray();
        }
        return parent::transformValue($value, $configuration, false);
    }
}
