<?php

namespace Dgame\GitBot\Github;

use Exception;

/**
 * Class Assignee
 * @package Dgame\GitBot\Github
 */
final class Assignee
{
    /**
     * @var array
     */
    private $assignee = [];

    /**
     * Assignee constructor.
     *
     * @param array $assignee
     */
    public function __construct(array $assignee)
    {
        $this->assignee = $assignee;
    }

    /**
     * @param string $json
     *
     * @return Assignee
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
     * @return string
     */
    public function getName(): string
    {
        return $this->assignee['login'] ?? '';
    }
}