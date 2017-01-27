<?php

namespace Dgame\GitBot\Github;

use Dgame\GitBot\Registry;

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