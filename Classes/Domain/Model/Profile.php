<?php

namespace Blueways\BwTodo\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class Profile extends AbstractEntity
{

    protected string $name = '';

    protected ?ObjectStorage $tasks = null;
}
