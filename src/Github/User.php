<?php

namespace Dgame\GitBot\Github;

use Exception;

/**
 * Class User
 * @package Dgame\GitBot\Github
 */
class User
{
    /**
     * @var array
     */
    protected $user = [];

    /**
     * User constructor.
     *
     * @param array $user
     */
    public function __construct(array $user)
    {
        $this->user = $user;
    }

    /**
     * @param string $json
     *
     * @return User
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
        return $this->user['login'] ?? '';
    }
}