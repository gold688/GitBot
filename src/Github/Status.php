<?php

namespace Dgame\GitBot\Github;

use Dgame\GitBot\Registry;

final class Status
{
    private $status = [];

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

    public function isFailure(): bool
    {
        return $this->status['state'] === 'failure';
    }

    public function isPending(): bool
    {
        return $this->status['state'] === 'pending';
    }

    public function isSuccess(): bool
    {
        return $this->status['state'] === 'success';
    }
}