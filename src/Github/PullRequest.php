<?php

namespace Dgame\GitBot\Github;

use Dgame\GitBot\Registry;

/**
 * Class PullRequest
 * @package Dgame\GitBot\Github
 */
final class PullRequest
{
    /**
     * @var array
     */
    private $request = [];

    /**
     * PullRequest constructor.
     *
     * @param array $request
     */
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
            $requests[] = self::one($request['number']);
        }

        return $requests;
    }

    /**
     * @param int $id
     *
     * @return PullRequest
     */
    public static function one(int $id): self
    {
        $client     = Registry::instance()->getClient();
        $repository = Registry::instance()->getRepositoryName();
        $owner      = Registry::instance()->getRepositoryOwner();

        $request = $client->pullRequest()->show($owner, $repository, $id);

        return new self($request);
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->request['number'];
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->request['title'];
    }

    /**
     * @return array
     */
    public function getAssignees(): array
    {
        $assignees = [];
        foreach ($this->request['assignees'] as $assignee) {
            $assignees[] = new Assignee($assignee);
        }

        return $assignees;
    }

    /**
     * @return array
     */
    public function getRequestedReviewers(): array
    {
        return RequestedReviewer::all($this->getId());
    }

    /**
     * @return bool
     */
    public function hasReviewRequests(): bool
    {
        return !empty($this->getRequestedReviewers());
    }

    /**
     * @return bool
     */
    public function isOpen(): bool
    {
        return $this->request['state'] === 'open';
    }

    /**
     * @return bool
     */
    public function isClosed(): bool
    {
        return $this->request['state'] === 'closed';
    }

    /**
     * @return string
     */
    public function getSha(): string
    {
        return $this->request['head']['sha'];
    }

    /**
     * @return bool
     */
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

    /**
     * @return Review[]
     */
    public function getReviews(): array
    {
        return Review::all($this->getId());
    }

    /**
     * @return bool
     */
    public function isApproved(): bool
    {
        if ($this->hasReviewRequests()) {
            return false;
        }

        $reviews = [];
        foreach ($this->getReviews() as $review) {
            $reviews[$review->getReviewer()->getName()][] = $review->isApproved();
        }

        foreach ($reviews as $name => $review) {
            if (!array_pop($review)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return bool
     */
    public function passedAnalysis(): bool
    {
        $status = $this->getStatus();

        return empty($status) || $status[0]->isSuccess();
    }

    /**
     * @param string $message
     * @param string $title
     */
    public function merge(string $message, string $title): void
    {
        $client     = Registry::instance()->getClient();
        $repository = Registry::instance()->getRepositoryName();
        $owner      = Registry::instance()->getRepositoryOwner();

        $client->pullRequest()->merge($owner, $repository, $this->getId(), $message, $this->getSha(), 'merge', $title);
    }

    /**
     * (re)open pull-request
     */
    public function open(): void
    {
        $client     = Registry::instance()->getClient();
        $repository = Registry::instance()->getRepositoryName();
        $owner      = Registry::instance()->getRepositoryOwner();

        $client->pullRequest()->update($owner, $repository, $this->getId(), ['state' => 'open']);
        $this->request['state'] = 'open';
    }

    /**
     * close pull-request
     */
    public function close(): void
    {
        $client     = Registry::instance()->getClient();
        $repository = Registry::instance()->getRepositoryName();
        $owner      = Registry::instance()->getRepositoryOwner();

        $client->pullRequest()->update($owner, $repository, $this->getId(), ['state' => 'closed']);
        $this->request['state'] = 'closed';
    }
}