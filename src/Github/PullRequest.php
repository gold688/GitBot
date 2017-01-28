<?php

namespace Dgame\GitBot\Github;

use Dgame\GitBot\Registry;
use Exception;

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
     * @param string $json
     *
     * @return PullRequest
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
    public function getNumber(): int
    {
        return $this->request['number'];
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->request['url'];
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->request['title'];
    }

    /**
     * @return Assignee
     * @throws Exception
     */
    public function getAssignee(): Assignee
    {
        if (is_array($this->request['assignee'])) {
            return new Assignee($this->request['assignee']);
        }

        throw new Exception('There is no Assignee');
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
        return RequestedReviewer::all($this->getNumber());
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
        return Review::all($this->getNumber());
    }

    /**
     * @return Review[]
     */
    public function getLastReviews(): array
    {
        $assoc = [];
        foreach ($this->getReviews() as $review) {
            $assoc[$review->getReviewer()->getName()][] = $review;
        }

        $reviews = [];
        foreach ($assoc as $name => $review) {
            $reviews[] = array_pop($review);
        }

        return $reviews;
    }

    /**
     * @return bool
     */
    public function isApproved(): bool
    {
        if ($this->hasReviewRequests()) {
            return false;
        }

        foreach ($this->getLastReviews() as $review) {
            if (!$review->isApproved()) {
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
     * @return Issue
     */
    public function getIssue(): Issue
    {
        return Issue::one($this->getNumber());
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

        $client->pullRequest()->merge($owner, $repository, $this->getNumber(), $message, $this->getSha(), 'merge', $title);
    }

    /**
     * (re)open pull-request
     */
    public function open(): void
    {
        $client     = Registry::instance()->getClient();
        $repository = Registry::instance()->getRepositoryName();
        $owner      = Registry::instance()->getRepositoryOwner();

        $client->pullRequest()->update($owner, $repository, $this->getNumber(), ['state' => 'open']);
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

        $client->pullRequest()->update($owner, $repository, $this->getNumber(), ['state' => 'closed']);
        $this->request['state'] = 'closed';
    }
}