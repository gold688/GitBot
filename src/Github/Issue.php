<?php

namespace Dgame\GitBot\Github;

use Dgame\GitBot\Registry;
use Exception;

/**
 * Class Issue
 * @package Dgame\GitBot\Github
 */
final class Issue extends AbstractIssue
{
    /**
     * @param string $json
     *
     * @return Issue
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
     * @return Issue[]
     */
    public static function all(): array
    {
        $client     = Registry::instance()->getClient();
        $repository = Registry::instance()->getRepositoryName();
        $owner      = Registry::instance()->getRepositoryOwner();

        $issues = [];
        foreach ($client->issues()->all($owner, $repository) as $issue) {
            $issues[] = self::one($issue['number']);
        }

        return $issues;
    }

    /**
     * @param int $number
     *
     * @return Issue
     */
    public static function one(int $number): self
    {
        $client     = Registry::instance()->getClient();
        $repository = Registry::instance()->getRepositoryName();
        $owner      = Registry::instance()->getRepositoryOwner();

        $issue = $client->issues()->show($owner, $repository, $number);

        return new self($issue);
    }

    /**
     * @param Issue $issue
     *
     * @return Issue
     */
    public static function open(self $issue): self
    {
        $client     = Registry::instance()->getClient();
        $repository = Registry::instance()->getRepositoryName();
        $owner      = Registry::instance()->getRepositoryOwner();
        $number     = $issue->getNumber();

        $client->issue()->update($owner, $repository, $number, ['state' => 'open']);

        return self::one($number);
    }

    /**
     * @param Issue $issue
     *
     * @return Issue
     */
    public static function close(self $issue): self
    {
        $client     = Registry::instance()->getClient();
        $repository = Registry::instance()->getRepositoryName();
        $owner      = Registry::instance()->getRepositoryOwner();
        $number     = $issue->getNumber();

        $client->issue()->update($owner, $repository, $number, ['state' => 'closed']);

        return self::one($number);
    }

    /**
     * @return Label[]
     */
    public function getLabels(): array
    {
        $labels = [];
        foreach ($this->issue['labels'] as $label) {
            $labels[] = new Label($label);
        }

        return $labels;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasLabel(string $name): bool
    {
        foreach ($this->getLabels() as $label) {
            if ($label->getName() === $name) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isPullRequest(): bool
    {
        $repository = Registry::instance()->getRepositoryName();
        $owner      = Registry::instance()->getRepositoryOwner();

        return $this->issue['html_url'] === 'https://github.com/' . implode('/', [$owner, $repository, 'pull', $this->getNumber()]);
    }

    /**
     * @return bool
     */
    public function isIssue(): bool
    {
        $repository = Registry::instance()->getRepositoryName();
        $owner      = Registry::instance()->getRepositoryOwner();

        return $this->issue['html_url'] === 'https://github.com/' . implode('/', [$owner, $repository, 'issues', $this->getNumber()]);
    }

    /**
     * @return PullRequest
     * @throws Exception
     */
    public function getPullRequest(): PullRequest
    {
        if (!$this->isPullRequest()) {
            throw new Exception('That is not a PullRequest');
        }

        return PullRequest::one($this->getNumber());
    }
}