<?php

use Dgame\GitBot\Github\Issue;
use PHPUnit\Framework\TestCase;

class IssueTest extends TestCase
{
    /**
     * @var Issue
     */
    private $issue;

    public function setup()
    {
        $filename    = __DIR__ . '/files/issue.json';
        $this->issue = Issue::load(file_get_contents($filename));
    }

    public function testUrl()
    {
        $this->assertEquals('https://api.github.com/repos/Dgame/test/issues/1', $this->issue->getUrl());
    }

    public function testLabels()
    {
        $this->assertCount(3, $this->issue->getLabels());
        foreach (['bug', 'duplicate', 'question'] as $label) {
            $this->assertTrue($this->issue->hasLabel($label));
        }
    }
}