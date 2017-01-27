<?php

namespace Dgame\GitBot\Github;

use Dgame\GitBot\Registry;

final class Review
{
    private $review = [];

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

    public static function one(int $pull, int $id): self
    {
        $api        = Registry::instance()->getReviewApi();
        $repository = Registry::instance()->getRepositoryName();
        $owner      = Registry::instance()->getRepositoryOwner();

        $request = $api->show($owner, $repository, $pull, $id);

        return new self($request);
    }

    public function getCommitId(): int
    {
        return $this->review['commit_id'];
    }

    public function isApproved(): bool
    {
        return $this->review['state'] === 'APPROVED';
    }

    public function isPending(): bool
    {
        return $this->review['state'] === 'PENDING';
    }

    public function isDismissed(): bool
    {
        return $this->review['state'] === 'DISMISSED';
    }
}