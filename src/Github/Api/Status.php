<?php

namespace Dgame\GitBot\Github\Api;

use Github\Api\AbstractApi;

/**
 * Class Status
 * @package Dgame\GitBot\Github\Api
 */
final class Status extends AbstractApi
{
    /**
     * @param string $username
     * @param string $repository
     * @param string $sha
     *
     * @return array
     */
    public function all(string $username, string $repository, string $sha): array
    {
        return $this->get('/repos/' . rawurlencode($username) . '/' . rawurlencode($repository) . '/statuses/' . $sha);
    }
}