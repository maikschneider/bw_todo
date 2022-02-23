<?php

namespace Blueways\BwTodo\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class Task extends AbstractEntity
{

    protected string $title = '';

    protected string $description = '';

    protected ?\DateTime $dueDate = null;

    protected ?Profile $profile = null;
}
