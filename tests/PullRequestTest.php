<?php

use Dgame\GitBot\Github\PullRequest;
use PHPUnit\Framework\TestCase;

class PullRequestTest extends TestCase
{
    /**
     * @var PullRequest
     */
    private $request;

    public function setup()
    {
        $filename      = __DIR__ . '/files/pull.json';
        $this->request = PullRequest::load(file_get_contents($filename));
    }

    public function testValues()
    {
        $this->assertEquals('a423717aba62d0418ce8cf7d2a86f33716461424', $this->request->getSha());
        $this->assertEquals(1, $this->request->getNumber());
        $this->assertEquals('EOL', $this->request->getTitle());
        $this->assertEquals('https://api.github.com/repos/Dgame/test/pulls/1', $this->request->getUrl());
        $this->assertTrue($this->request->isOpen());
    }

    public function testAssignee()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('There is no Assignee');

        $this->request->getAssignee();
    }

    public function testAssignees()
    {
        $this->assertEmpty($this->request->getAssignees());
    }
}