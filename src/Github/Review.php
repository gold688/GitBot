<?php

namespace Dgame\GitBot\Github;

use Dgame\GitBot\Registry;

/**
 * Class Review
 * @package Dgame\GitBot\Github
 */
final class Review
{
    /**
     * @var array
     */
    private $review = [];

    /**
     * Review constructor.
     *
     * @param array $request
     */
    public function __construct(array $request)
    {
        $this->review = $request;
    }

    /**
     * @param int $pull
     *
     * @return Review[]
     */
    public static function all(int $pull): array
    {
        $api        = Registry::instance()->getReviewApi();
        $repository = Registry::instance()->getRepositoryName();
        $owner      = Registry::instance()->getRepositoryOwner();

        $requests = [];
        foreach ($api->all($owner, $repository, $pull) as $request) {
            $requests[] = new self($request);
        }

        return $requests;
    }

    /**
     * @param int $pull
     * @param int $id
     *
     * @return Review
     */
    public static function one(int $pull, int $id): self
    {
        $api        = Registry::instance()->getReviewApi();
        $repository = Registry::instance()->getRepositoryName();
        $owner      = Registry::instance()->getRepositoryOwner();

        $request = $api->show($owner, $repository, $pull, $id);

        return new self($request);
    }

    /**
     * @return int
     */
    public function getCommitId(): int
    {
        return $this->review['commit_id'];
    }

    /**
     * @return bool
     */
    public function isApproved(): bool
    {
        return $this->review['state'] === 'APPROVED';
    }

    /**
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->review['state'] === 'PENDING';
    }

    /**
     * @return bool
     */
    public function isDismissed(): bool
    {
        return $this->review['state'] === 'DISMISSED';
    }
}