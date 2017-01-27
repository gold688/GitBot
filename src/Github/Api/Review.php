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
     * @param int    $number
     * @param int    $id
     *
     * @return array
     */
    public function show(string $username, string $repository, int $number, int $id): array
    {
        return $this->get(
            '/repos/' . rawurlencode($username) . '/' . rawurlencode($repository) . '/pulls/' . $number . '/reviews/' . $id,
            [],
            ['Accept' => 'application/vnd.github.black-cat-preview+json']
        );
    }

    /**
     * @param string $username
     * @param string $repository
     * @param int    $number
     *
     * @return array
     */
    public function all(string $username, string $repository, int $number): array
    {
        return $this->get(
            '/repos/' . rawurlencode($username) . '/' . rawurlencode($repository) . '/pulls/' . $number . '/reviews',
            [],
            ['Accept' => 'application/vnd.github.black-cat-preview+json']
        );
    }
}