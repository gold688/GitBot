<?php

namespace Dgame\GitBot\Github;

use DateTime;
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
     * @param int $number
     *
     * @return PullRequest
     */
    public static function one(int $number): self
    {
        $client     = Registry::instance()->getClient();
        $repository = Registry::instance()->getRepositoryName();
        $owner      = Registry::instance()->getRepositoryOwner();

        $request = $client->pullRequest()->show($owner, $repository, $number);

        return new self($request);
    }

    /**
     * @param PullRequest $request
     *
     * @return PullRequest
     */
    public static function open(self $request): self
    {
        $client     = Registry::instance()->getClient();
        $repository = Registry::instance()->getRepositoryName();
        $owner      = Registry::instance()->getRepositoryOwner();
        $number     = $request->getNumber();

        $client->pullRequest()->update($owner, $repository, $number, ['state' => 'open']);

        return self::one($number);
    }

    /**
     * @param PullRequest $request
     *
     * @return PullRequest
     */
    public static function close(self $request): self
    {
        $client     = Registry::instance()->getClient();
        $repository = Registry::instance()->getRepositoryName();
        $owner      = Registry::instance()->getRepositoryOwner();
        $number     = $request->getNumber();

        $client->pullRequest()->update($owner, $repository, $number, ['state' => 'closed']);

        return self::one($number);
    }

    /**
     * @param PullRequest $request
     * @param string      $message
     * @param string|null $title
     *
     * @return PullRequest
     */
    public static function merge(self $request, string $message, string $title = null): self
    {
        $client     = Registry::instance()->getClient();
        $repository = Registry::instance()->getRepositoryName();
        $owner      = Registry::instance()->getRepositoryOwner();
        $number     = $request->getNumber();
        $sha        = $request->getSha();

        $client->pullRequest()->merge($owner, $repository, $number, $message, $sha, 'merge', $title);

        return self::one($number);
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
     * @return DateTime
     */
    public function createdAt(): DateTime
    {
        return new DateTime($this->request['created_at']);
    }

    /**
     * @return DateTime
     */
    public function updatedAt(): DateTime
    {
        return new DateTime($this->request['updated_at']);
    }

    /**
     * @return DateTime|null
     */
    public function closedAt(): ?DateTime
    {
        return !empty($this->request['closed_at']) ? new DateTime($this->request['closed_at']) : null;
    }

    /**
     * @return DateTime|null
     */
    public function mergedAt(): ?DateTime
    {
        return !empty($this->request['merged_at']) ? new DateTime($this->request['merged_at']) : null;
    }

    /**
     * @return string
     */
    public function milestone(): string
    {
        return $this->request['milestone'] ?? '';
    }

    /**
     * @return bool
     */
    public function isMergeable(): bool
    {
        return (bool) $this->request['mergeable'];
    }

    /**
     * @return bool
     */
    public function isMerged(): bool
    {
        return $this->request['merged'] ?? false;
    }

    /**
     * @return string
     */
    public function getMergeableState(): string
    {
        return $this->request['mergeable_state'] ?? '';
    }

    /**
     * @return string
     */
    public function mergedBy(): string
    {
        return $this->request['merged_by'] ?? '';
    }

    /**
     * @return int
     */
    public function getAmountOfCommits(): int
    {
        return $this->request['commits'];
    }

    /**
     * @return int
     */
    public function getAmountOfAdditions(): int
    {
        return $this->request['additions'];
    }

    /**
     * @return int
     */
    public function getAmountOfDeletions(): int
    {
        return $this->request['deletions'];
    }

    /**
     * @return int
     */
    public function getAmountOfChangedFiles(): int
    {
        return $this->request['changed_files'];
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
}