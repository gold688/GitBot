<?php

namespace Dgame\GitBot\Github;

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
     * @return string
     */
    public function getName(): string
    {
        return $this->assignee['login'] ?? '';
    }
}