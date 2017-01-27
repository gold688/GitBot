<?php

namespace Dgame\GitBot\Github;

use Dgame\GitBot\Registry;

/**
 * Class RequestedReviewer
 * @package Dgame\GitBot\Github
 */
final class RequestedReviewer
{
    /**
     * @var array
     */
    private $reviewer = [];

    /**
     * Assignee constructor.
     *
     * @param array $reviewer
     */
    public function __construct(array $reviewer)
    {
        $this->reviewer = $reviewer;
    }

    /**
     * @param int $pull
     *
     * @return array
     */
    public static function all(int $pull): array
    {
        $api        = Registry::instance()->getRequestedReviewerApi();
        $repository = Registry::instance()->getRepositoryName();
        $owner      = Registry::instance()->getRepositoryOwner();

        $output = [];
        foreach ($api->all($owner, $repository, $pull) as $reviewer) {
            $output[] = new self($reviewer);
        }

        return $output;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->reviewer['login'] ?? '';
    }
}