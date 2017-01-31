<?php

namespace Dgame\GitBot\Github;

use Dgame\GitBot\Registry;
use Exception;

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
     * @param string $json
     *
     * @return Review
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
     * @param int $number
     *
     * @return Review[]
     */
    public static function all(int $number): array
    {
        $api        = Registry::instance()->getReviewApi();
        $repository = Registry::instance()->getRepositoryName();
        $owner      = Registry::instance()->getRepositoryOwner();

        $output = [];
        foreach ($api->all($owner, $repository, $number) as $review) {
            $output[] = new self($review);
        }

        return $output;
    }

    /**
     * @param int $number
     * @param int $id
     *
     * @return Review
     */
    public static function one(int $number, int $id): self
    {
        $api        = Registry::instance()->getReviewApi();
        $repository = Registry::instance()->getRepositoryName();
        $owner      = Registry::instance()->getRepositoryOwner();

        $request = $api->show($owner, $repository, $number, $id);

        return new self($request);
    }

    /**
     * @return RequestedReviewer
     */
    public function getReviewer(): RequestedReviewer
    {
        return new RequestedReviewer($this->review['user']);
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
    public function isComment(): bool
    {
        return $this->review['state'] === 'COMMENT';
    }

    /**
     * @return bool
     */
    public function requestChanges(): bool
    {
        return $this->review['state'] = 'CHANGES_REQUESTED';
    }
}