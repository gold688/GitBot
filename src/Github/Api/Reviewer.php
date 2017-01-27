<?php

namespace Dgame\GitBot\Github\Api;

use Github\Api\AbstractApi;

/**
 * Class Reviewer
 * @package Dgame\GitBot\Github\Api
 */
final class Reviewer extends AbstractApi
{
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
            '/repos/' . rawurlencode($username) . '/' . rawurlencode($repository) . '/pulls/' . $number . '/requested_reviewers',
            [],
            ['Accept' => 'application/vnd.github.black-cat-preview+json']
        );
    }
}