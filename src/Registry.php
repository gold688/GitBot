<?php

namespace Dgame\GitBot;

use Dgame\GitBot\Github\Api\Review;
use Dgame\GitBot\Github\Api\Status;
use Github\Client;

/**
 * Class Registry
 * @package Dgame\GitBot
 */
final class Registry
{
    /**
     * @var Registry
     */
    private static $instance;
    /**
     * @var Client
     */
    private $client;
    /**
     * @var array
     */
    private $repository = [];

    /**
     * Registry constructor.
     */
    private function __construct()
    {
    }

    /**
     *
     */
    private function __clone()
    {
    }

    /**
     * @return Registry
     */
    public static function instance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param Client $client
     */
    public function setClient(Client $client): void
    {
        $this->client = $client;
    }

    /**
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * @param string $name
     * @param string $owner
     */
    public function setRepository(string $name, string $owner): void
    {
        $this->repository = [
            'name'  => $name,
            'owner' => $owner
        ];
    }

    /**
     * @return string
     */
    public function getRepositoryName(): string
    {
        return $this->repository['name'];
    }

    /**
     * @return string
     */
    public function getRepositoryOwner(): string
    {
        return $this->repository['owner'];
    }

    /**
     * @return Review
     */
    public function getReviewApi(): Review
    {
        return new Review($this->client);
    }

    /**
     * @return Status
     */
    public function getStatusApi(): Status
    {
        return new Status($this->client);
    }
}