<?php

namespace Dgame\GitBot\Github\Api;

use Github\Api\AbstractApi;

/**
 * Class Review
 * @package Dgame\GitBot\Github\Api
 */
final class Review extends AbstractApi
{
    /**
     * @param string $username
     * @param string $repository
     * @param int    $pull
     * @param int    $review
     *
     * @return array
     */
    public function show(string $username, string $repository, int $pull, int $review): array
    {
        return $this->get(
            '/repos/' . rawurlencode($username) . '/' . rawurlencode($repository) . '/pulls/' . $pull . '/reviews' . $review,
            [],
            ['Accept' => 'application/vnd.github.black-cat-preview+json']
        );
    }

    /**
     * @param string $username
     * @param string $repository
     * @param int    $pull
     *
     * @return array
     */
    public function all(string $username, string $repository, int $pull): array
    {
        return $this->get(
            '/repos/' . rawurlencode($username) . '/' . rawurlencode($repository) . '/pulls/' . $pull . '/reviews',
            [],
            ['Accept' => 'application/vnd.github.black-cat-preview+json']
        );
    }
}