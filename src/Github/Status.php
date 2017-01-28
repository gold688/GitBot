<?php

namespace Dgame\GitBot\Github;

use Dgame\GitBot\Registry;
use Exception;

/**
 * Class Status
 * @package Dgame\GitBot\Github
 */
final class Status
{
    /**
     * @var array
     */
    private $status = [];

    /**
     * Status constructor.
     *
     * @param array $status
     */
    public function __construct(array $status)
    {
        $this->status = $status;
    }

    /**
     * @param string $json
     *
     * @return Status
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
     * @param string $sha
     *
     * @return Status[]
     */
    public static function all(string $sha): array
    {
        $api        = Registry::instance()->getStatusApi();
        $repository = Registry::instance()->getRepositoryName();
        $owner      = Registry::instance()->getRepositoryOwner();

        $output = [];
        foreach ($api->all($owner, $repository, $sha) as $status) {
            $output[] = new self($status);
        }

        return $output;
    }

    /**
     * @return bool
     */
    public function isFailure(): bool
    {
        return $this->status['state'] === 'failure';
    }

    /**
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->status['state'] === 'pending';
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->status['state'] === 'success';
    }
}