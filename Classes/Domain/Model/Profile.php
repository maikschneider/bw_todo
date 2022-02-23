<?php

namespace Blueways\BwTodo\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class Profile extends AbstractEntity
{

    protected string $name = '';

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Blueways\BwTodo\Domain\Model\Task>|null
     */
    protected ?ObjectStorage $tasks = null;

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage|null
     */
    public function getTasks(): ?ObjectStorage
    {
        return $this->tasks;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage|null $tasks
     */
    public function setTasks(?ObjectStorage $tasks): void
    {
        $this->tasks = $tasks;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
