# GitBot in PHP

 - Automated references to github issues, trello, bug-tracking systems like bugzilla etc.
 - Automated overviews about code-coverage, tests-coverage etc.
 - Auto-merging approved & passing PRs

Currently supported:
 - Auto-merging approved & passing PRs

## Auto-Merge
```php
<?php

use Dgame\GitBot\Github\Issue;
use Dgame\GitBot\Registry;
use Github\Client;

$client = new Client();
$client->authenticate('USERNAME', 'PASSWORD', Client::AUTH_HTTP_PASSWORD);

Registry::instance()->setClient($client);
Registry::instance()->setRepository('REPOSITORY', 'USERNAME');

foreach (Issue::all() as $issue) {
    if ($issue->isPullRequest()) {
        $request = $issue->asPullRequest();
        if ($request->isMergeable() && $request->passedAnalysis() && $request->isApproved()) {
            print 'Try to merge PR #' . $request->getId() . PHP_EOL;
            $request->merge('Auto-Merge', 'Auto-Merge');
            print 'PR #' . $request->getId() . ' was successfully merged' . PHP_EOL;
        }
    }
}
```
