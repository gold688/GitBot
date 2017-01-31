<?php

namespace Dgame\GitBot\Github;

use DateTime;
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
     * @return string
     */
    public function getUrl(): string
    {
        return $this->status['url'];
    }

    /**
     * @return bool
     */
    public function isError(): bool
    {
        return $this->status['state'] === 'error';
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

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->status['description'];
    }

    /**
     * @return string
     */
    public function getTargetUrl(): string
    {
        return $this->status['target_url'];
    }

    /**
     * @return string
     */
    public function getContext(): string
    {
        return $this->status['context'];
    }

    /**
     * @return DateTime
     */
    public function createdAt(): DateTime
    {
        return new DateTime($this->status['created_at']);
    }

    /**
     * @return DateTime
     */
    public function updatedAt(): DateTime
    {
        return new DateTime($this->status['updated_at']);
    }

    /**
     * @return User
     */
    public function getCreator(): User
    {
        return new User($this->status['creator']);
    }
}