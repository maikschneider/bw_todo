<?php

namespace Blueways\BwTodoSite\Domain\Model;

use TYPO3\CMS\Core\Exception;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Mvc\Request;

class Task extends AbstractEntity
{
    /**
     * @var string
     * @TYPO3\CMS\Extbase\Annotation\Validate("StringLength", options={"minimum": 1, "maximum": 255})
     */
    protected string $title = '';

    protected string $description = '';

    protected ?\DateTime $dueDate = null;

    protected ?Profile $profile = null;

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return \DateTime|null
     */
    public function getDueDate(): ?\DateTime
    {
        return $this->dueDate;
    }

    /**
     * @param \DateTime|null $dueDate
     */
    public function setDueDate(?\DateTime $dueDate): void
    {
        $this->dueDate = $dueDate;
    }

    /**
     * @return \Blueways\BwTodoSite\Domain\Model\Profile|null
     */
    public function getProfile(): ?Profile
    {
        return $this->profile;
    }

    /**
     * @param \Blueways\BwTodoSite\Domain\Model\Profile|null $profile
     */
    public function setProfile(?Profile $profile): void
    {
        $this->profile = $profile;
    }

    public static function createFromRequest(Request $request, $profile): self
    {
        $task = new self();
        $task->updateFromRequest($request, $profile);

        return $task;
    }

    public function updateFromRequest(Request $request, $profile): void
    {
        $body = $request->getParsedBody();
        $this->profile = $profile;

        if (isset($body['title']) && $body['title']) {
            $this->title = (string)$body['title'];
        }

        if (isset($body['description']) && $body['description']) {
            $this->description = (string)$body['description'];
        }

        if (isset($body['dueDate']) && $body['dueDate']) {
            try {
                $date = \DateTime::createFromFormat('d.m.Y-H:i', ($body['dueDate']));
                $this->dueDate = $date;
            } catch (\Exception $e) {
            }
        }
    }
}
