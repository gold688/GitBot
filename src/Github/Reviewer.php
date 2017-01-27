<?php

namespace Dgame\GitBot\Github;

use Dgame\GitBot\Registry;

/**
 * Class Reviewer
 * @package Dgame\GitBot\Github
 */
final class Reviewer
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
        $client     = Registry::instance()->getReviewerApi();
        $repository = Registry::instance()->getRepositoryName();
        $owner      = Registry::instance()->getRepositoryOwner();

        $output = [];
        foreach ($client->all($owner, $repository, $pull) as $reviewer) {
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