<?php

namespace Dgame\GitBot\Github;

use DateTime;

/**
 * Class Milestone
 * @package Dgame\GitBot\Github
 */
final class Milestone
{
    /**
     * @var array
     */
    private $milestone = [];

    /**
     * Milestone constructor.
     *
     * @param array $milestone
     */
    public function __construct(array $milestone)
    {
        $this->milestone = $milestone;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->milestone['url'];
    }

    /**
     * @return int
     */
    public function getNumber(): int
    {
        return $this->milestone['number'];
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->milestone['title'];
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->milestone['description'];
    }

    /**
     * @return User
     */
    public function getCreator(): User
    {
        return new User($this->milestone['creator']);
    }

    /**
     * @return int
     */
    public function getOpenIssues(): int
    {
        return $this->milestone['open_issues'];
    }

    /**
     * @return int
     */
    public function getClosedIssues(): int
    {
        return $this->milestone['closed_issues'];
    }

    /**
     * @return bool
     */
    public function isOpen(): bool
    {
        return $this->milestone['state'] === 'open';
    }

    /**
     * @return bool
     */
    public function isClosed(): bool
    {
        return $this->milestone['state'] === 'closed';
    }

    /**
     * @return DateTime
     */
    public function createdAt(): DateTime
    {
        return new DateTime($this->milestone['created_at']);
    }

    /**
     * @return DateTime
     */
    public function updatedAt(): DateTime
    {
        return new DateTime($this->milestone['updated_at']);
    }

    /**
     * @return DateTime
     */
    public function dueOn(): DateTime
    {
        return new DateTime($this->milestone['due_on']);
    }

    /**
     * @return DateTime|null
     */
    public function closedAt(): ?DateTime
    {
        return !empty($this->milestone['closed_at']) ? new DateTime($this->milestone['closed_at']) : null;
    }
}