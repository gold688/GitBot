<?php

namespace Dgame\GitBot\Github;

use Dgame\GitBot\Registry;
use Exception;

final class Issue
{
    private $issue = [];

    public function __construct(array $issue)
    {
        $this->issue = $issue;
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
            $issues[] = new self($issue);
        }

        return $issues;
    }

    public static function one(int $id): self
    {
        $client     = Registry::instance()->getClient();
        $repository = Registry::instance()->getRepositoryName();
        $owner      = Registry::instance()->getRepositoryOwner();

        $issue = $client->issues()->show($owner, $repository, $id);

        return new self($issue);
    }

    public function getId(): int
    {
        return $this->issue['number'];
    }

    public function getTitle(): string
    {
        return $this->issue['title'];
    }

    public function isOpen(): bool
    {
        return $this->issue['state'] === 'open';
    }

    public function isClosed(): bool
    {
        return $this->issue['state'] === 'closed';
    }

    public function isPullRequest(): bool
    {
        $repository = Registry::instance()->getRepositoryName();
        $owner      = Registry::instance()->getRepositoryOwner();

        return $this->issue['html_url'] === 'https://github.com/' . implode('/', [$owner, $repository, 'pull', $this->getId()]);
    }

    public function isIssue(): bool
    {
        $repository = Registry::instance()->getRepositoryName();
        $owner      = Registry::instance()->getRepositoryOwner();

        return $this->issue['html_url'] === 'https://github.com/' . implode('/', [$owner, $repository, 'issues', $this->getId()]);
    }

    public function asPullRequest(): PullRequest
    {
        if (!$this->isPullRequest()) {
            throw new Exception('That is not a PullRequest');
        }

        return PullRequest::one($this->getId());
    }

    public function open(): void
    {
        $client     = Registry::instance()->getClient();
        $repository = Registry::instance()->getRepositoryName();
        $owner      = Registry::instance()->getRepositoryOwner();

        $client->issue()->update($owner, $repository, $this->getId(), ['state' => 'open']);
        $this->issue['state'] = 'open';
    }

    public function close(): void
    {
        $client     = Registry::instance()->getClient();
        $repository = Registry::instance()->getRepositoryName();
        $owner      = Registry::instance()->getRepositoryOwner();

        $client->issue()->update($owner, $repository, $this->getId(), ['state' => 'closed']);
        $this->issue['state'] = 'closed';
    }
}