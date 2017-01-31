<?php

namespace Dgame\GitBot\Github;

use DateTime;
use Dgame\GitBot\Registry;
use Exception;

/**
 * Class PullRequest
 * @package Dgame\GitBot\Github
 */
final class PullRequest extends AbstractIssue
{
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
     * @return string
     */
    public function getSha(): string
    {
        return $this->issue['head']['sha'];
    }

    /**
     * @return DateTime|null
     */
    public function mergedAt(): ?DateTime
    {
        return !empty($this->issue['merged_at']) ? new DateTime($this->issue['merged_at']) : null;
    }

    /**
     * @return bool
     */
    public function isMergeable(): bool
    {
        return (bool) $this->issue['mergeable'];
    }

    /**
     * @return bool
     */
    public function isMerged(): bool
    {
        return $this->issue['merged'] ?? false;
    }

    /**
     * @return string
     */
    public function getMergeableState(): string
    {
        return $this->issue['mergeable_state'] ?? '';
    }

    /**
     * @return string
     */
    public function mergedBy(): string
    {
        return $this->issue['merged_by'] ?? '';
    }

    /**
     * @return int
     */
    public function getAmountOfCommits(): int
    {
        return $this->issue['commits'];
    }

    /**
     * @return int
     */
    public function getAmountOfAdditions(): int
    {
        return $this->issue['additions'];
    }

    /**
     * @return int
     */
    public function getAmountOfDeletions(): int
    {
        return $this->issue['deletions'];
    }

    /**
     * @return int
     */
    public function getAmountOfChangedFiles(): int
    {
        return $this->issue['changed_files'];
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