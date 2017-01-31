<?php

namespace Dgame\GitBot\Github;

use DateTime;
use Dgame\GitBot\Registry;
use Exception;

/**
 * Class AbstractIssue
 * @package Dgame\GitBot\Github
 */
abstract class AbstractIssue
{
    /**
     * @var array
     */
    protected $issue = [];

    /**
     * Issue constructor.
     *
     * @param array $issue
     */
    public function __construct(array $issue)
    {
        $this->issue = $issue;
    }

    /**
     * @return int
     */
    final public function getNumber(): int
    {
        return $this->issue['number'];
    }

    /**
     * @return string
     */
    final public function getUrl(): string
    {
        return $this->issue['url'];
    }

    /**
     * @return string
     */
    final public function getTitle(): string
    {
        return $this->issue['title'];
    }

    /**
     * @return Assignee
     * @throws Exception
     */
    final public function getAssignee(): Assignee
    {
        if (is_array($this->issue['assignee'])) {
            return new Assignee($this->issue['assignee']);
        }

        throw new Exception('There is no Assignee');
    }

    /**
     * @return array
     */
    final public function getAssignees(): array
    {
        $assignees = [];
        foreach ($this->issue['assignees'] as $assignee) {
            $assignees[] = new Assignee($assignee);
        }

        return $assignees;
    }

    /**
     * @return bool
     */
    final public function isOpen(): bool
    {
        return $this->issue['state'] === 'open';
    }

    /**
     * @return bool
     */
    final public function isClosed(): bool
    {
        return $this->issue['state'] === 'closed';
    }

    /**
     * @return DateTime
     */
    final public function createdAt(): DateTime
    {
        return new DateTime($this->issue['created_at']);
    }

    /**
     * @return DateTime
     */
    final public function updatedAt(): DateTime
    {
        return new DateTime($this->issue['updated_at']);
    }

    /**
     * @return DateTime|null
     */
    final public function closedAt(): ?DateTime
    {
        return !empty($this->issue['closed_at']) ? new DateTime($this->issue['closed_at']) : null;
    }

    /**
     * @return string
     */
    final public function milestone(): string
    {
        return $this->issue['milestone'] ?? '';
    }

    /**
     * @return string
     */
    final public function closedBy(): string
    {
        return $this->issue['closed_by'] ?? '';
    }

    /**
     * @return int
     */
    final public function getAmountOfComments(): int
    {
        return $this->issue['comments'];
    }
}