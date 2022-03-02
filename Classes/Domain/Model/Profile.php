<?php

namespace Blueways\BwTodo\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Mvc\Request;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class Profile extends AbstractEntity
{
    /**
     * @var string
     * @TYPO3\CMS\Extbase\Annotation\Validate("StringLength", options={"minimum": 1, "maximum": 255})
     */
    protected string $name = '';

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Blueways\BwTodo\Domain\Model\Task>|null
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade("remove")
     */
    protected ?ObjectStorage $tasks = null;

    public function __construct()
    {
        $this->tasks = new ObjectStorage();
    }

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

    public static function createFromRequest(Request $request): self
    {
        $profile = new self();
        $profile->updateFromRequest($request);

        return $profile;
    }

    public function updateFromRequest(Request $request): void
    {
        $body = $request->getParsedBody();

        // @TODO dirty fix to parse the malformed post body (due to wrong content-type?)
        if (is_array($body) && strpos(array_key_first($body), 'WebKitFormBoundary')) {
            $malformedBody = array_pop($body);
            preg_match_all('/\"(\w+)\"\r\n\r\n(.+)\r/', $malformedBody, $body);
            $body = (isset($body[2][0])) ? [$body[1][0] => $body[2][0]] : $body;
        }

        if (isset($body['name']) && $body['name']) {
            $this->name = (string)$body['name'];
        }
    }
}
