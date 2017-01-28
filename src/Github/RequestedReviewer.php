<?php

namespace Dgame\GitBot\Github;

use Dgame\GitBot\Registry;
use Exception;

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
     * @param string $json
     *
     * @return RequestedReviewer
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