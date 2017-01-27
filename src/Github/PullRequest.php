<?php

namespace Dgame\GitBot\Github;

use Dgame\GitBot\Registry;

final class PullRequest
{
    private $request = [];

    public function __construct(array $request)
    {
        $this->request = $request;
    }

    /**
     * @return PullRequest[]
     */
    public static function all(): array
    {
        $client     = Registry::instance()->getClient();
        $repository = Registry::instance()->getRepositoryName();
        $owner      = Registry::instance()->getRepositoryOwner();

        $requests = [];
        foreach ($client->pullRequests()->all($owner, $repository) as $request) {
            $requests[] = new self($request);
        }

        return $requests;
    }

    public static function one(int $id): self
    {
        $client     = Registry::instance()->getClient();
        $repository = Registry::instance()->getRepositoryName();
        $owner      = Registry::instance()->getRepositoryOwner();

        $request = $client->pullRequest()->show($owner, $repository, $id);

        return new self($request);
    }

    public function getId(): int
    {
        return $this->request['number'];
    }

    public function getTitle(): string
    {
        return $this->request['title'];
    }

    public function isOpen(): bool
    {
        return $this->request['state'] === 'open';
    }

    public function isClosed(): bool
    {
        return $this->request['state'] === 'closed';
    }

    public function getSha(): string
    {
        return $this->request['head']['sha'];
    }

    public function isMergeable(): bool
    {
        return (bool) $this->request['mergeable'];
    }

    /**
     * @return Status[]
     */
    public function getStatus(): array
    {
        return Status::all($this->getSha());
    }

    public function getLastStatus(): Status
    {
        return $this->getStatus()[0];
    }

    /**
     * @return Review[]
     */
    public function getReviews(): array
    {
        return Review::all($this->getId());
    }

    public function isApproved(): bool
    {
        foreach ($this->getReviews() as $review) {
            if (!$review->isApproved()) {
                return false;
            }
        }

        return true;
    }

    public function merge(string $message, string $title): void
    {
        $client     = Registry::instance()->getClient();
        $repository = Registry::instance()->getRepositoryName();
        $owner      = Registry::instance()->getRepositoryOwner();

        $client->pullRequest()->merge($owner, $repository, $this->getId(), $message, $this->getSha(), 'merge', $title);
    }

    public function open(): void
    {
        $client     = Registry::instance()->getClient();
        $repository = Registry::instance()->getRepositoryName();
        $owner      = Registry::instance()->getRepositoryOwner();

        $client->pullRequest()->update($owner, $repository, $this->getId(), ['state' => 'open']);
        $this->request['state'] = 'open';
    }

    public function close(): void
    {
        $client     = Registry::instance()->getClient();
        $repository = Registry::instance()->getRepositoryName();
        $owner      = Registry::instance()->getRepositoryOwner();

        $client->pullRequest()->update($owner, $repository, $this->getId(), ['state' => 'closed']);
        $this->request['state'] = 'closed';
    }
}