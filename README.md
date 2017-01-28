# GitBot in PHP

 - Automated references to github issues, trello, bug-tracking systems like bugzilla etc.
 - Automated overviews about code-coverage, tests-coverage etc.
 - Auto-merging approved & passing PRs
 - Automated PR reviews for common mistakes
 
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Dgame/GitBot/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Dgame/GitBot/?branch=master)
[![Build Status](https://travis-ci.org/Dgame/GitBot.svg?branch=master)](https://travis-ci.org/Dgame/GitBot)
![PHP Version](https://img.shields.io/badge/PHP-%3E%3D%207.1.0-lightgrey.svg)

## Currently supported:
 - Auto-merging approved & passing PRs

# Auto-Merge
```php
<?php

use Dgame\GitBot\Github\Issue;
use Dgame\GitBot\Registry;
use Github\Client;

$client = new Client();
$client->authenticate('USERNAME', 'PASSWORD', Client::AUTH_HTTP_PASSWORD);

Registry::instance()->setClient($client);
Registry::instance()->setRepository('REPOSITORY', 'USERNAME');

foreach (PullRequest::all() as $request) {
    if ($request->isMergeable() && $request->passedAnalysis() && $request->isApproved()) {
        print 'Try to merge PR #' . $request->getId() . PHP_EOL;
        $request->merge('Auto-Merge', 'Auto-Merge');
        print 'PR #' . $request->getId() . ' was successfully merged' . PHP_EOL;
    }
}
```
