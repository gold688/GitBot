<?php

namespace Dgame\GitBot\Github;

use Dgame\GitBot\Registry;
use Exception;

/**
 * Class Issue
 * @package Dgame\GitBot\Github
 */
final class Issue
{
    /**
     * @var array
     */
    private $issue = [];

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
     * @param string $json
     *
     * @return Issue
     * @throws Exception
     */
    public static function load(string $json): self
    {
        $assoc = json_decode($json, true);
        if (is_array($assoc) && json_last_error() === JSON_ERROR_NONE) {
            return new self($assoc);
        }

        throw new Exception(json_last_error_msg());
    }

    /**
     * @return Issue[]
     */
    public static function all(): array
    {
        $client     = Registry::instance()->getClient();
        $repository = Registry::instance()->getRepositoryName();
        $owner      = Registry::instance()->getRepositoryOwner();

        $issues = [];
        foreach ($client->issues()->all($owner, $repository) as $issue) {
            $issues[] = self::one($issue['number']);
        }

        return $issues;
    }

    /**
     * @param int $id
     *
     * @return Issue
     */
    public static function one(int $id): self
    {
        $client     = Registry::instance()->getClient();
        $repository = Registry::instance()->getRepositoryName();
        $owner      = Registry::instance()->getRepositoryOwner();

        $issue = $client->issues()->show($owner, $repository, $id);

        return new self($issue);
    }

    /**
     * @return int
     */
    public function getNumber(): int
    {
        return $this->issue['number'];
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->issue['url'];
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->issue['title'];
    }

    /**
     * @return Label[]
     */
    public function getLabels(): array
    {
        $labels = [];
        foreach ($this->issue['labels'] as $label) {
            $labels[] = new Label($label);
        }

        return $labels;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasLabel(string $name): bool
    {
        foreach ($this->getLabels() as $label) {
            if ($label->getName() === $name) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return Assignee
     * @throws Exception
     */
    public function getAssignee(): Assignee
    {
        if (is_array($this->issue['assignee'])) {
            return new Assignee($this->issue['assignee']);
        }

        throw new Exception('There is no Assignee');
    }

    /**
     * @return array
     */
    public function getAssignees(): array
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
    public function isOpen(): bool
    {
        return $this->issue['state'] === 'open';
    }

    /**
     * @return bool
     */
    public function isClosed(): bool
    {
        return $this->issue['state'] === 'closed';
    }

    /**
     * @return bool
     */
    public function isPullRequest(): bool
    {
        $repository = Registry::instance()->getRepositoryName();
        $owner      = Registry::instance()->getRepositoryOwner();

        return $this->issue['html_url'] === 'https://github.com/' . implode('/', [$owner, $repository, 'pull', $this->getNumber()]);
    }

    /**
     * @return bool
     */
    public function isIssue(): bool
    {
        $repository = Registry::instance()->getRepositoryName();
        $owner      = Registry::instance()->getRepositoryOwner();

        return $this->issue['html_url'] === 'https://github.com/' . implode('/', [$owner, $repository, 'issues', $this->getNumber()]);
    }

    /**
     * @return PullRequest
     * @throws Exception
     */
    public function asPullRequest(): PullRequest
    {
        if (!$this->isPullRequest()) {
            throw new Exception('That is not a PullRequest');
        }

        return PullRequest::one($this->getNumber());
    }

    /**
     * (Re)open issue
     */
    public function open(): void
    {
        $client     = Registry::instance()->getClient();
        $repository = Registry::instance()->getRepositoryName();
        $owner      = Registry::instance()->getRepositoryOwner();

        $client->issue()->update($owner, $repository, $this->getNumber(), ['state' => 'open']);
        $this->issue['state'] = 'open';
    }

    /**
     * Close issue
     */
    public function close(): void
    {
        $client     = Registry::instance()->getClient();
        $repository = Registry::instance()->getRepositoryName();
        $owner      = Registry::instance()->getRepositoryOwner();

        $client->issue()->update($owner, $repository, $this->getNumber(), ['state' => 'closed']);
        $this->issue['state'] = 'closed';
    }
}