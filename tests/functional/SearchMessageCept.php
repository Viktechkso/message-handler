<?php 
$I = new FunctionalTester($scenario);
$I->reloadFixtures();

$I->wantTo('use filters to show messages with specified statuses on list');
$I->amOnPage('/message');
$I->see('Finished', 'table#results td span.label');
$I->see('In progress', 'table#results td span.label');
$I->see('New', 'table#results td span.label');
$I->see('Halted', 'table#results td span.label');
$I->see('Rerun', 'table#results td span.label');
$I->see('Error', 'table#results td span.label');
$I->checkOption('#message_search_flowStatuses_0'); // "New"
$I->checkOption('#message_search_flowStatuses_4'); // "Finished"
$I->click('Search');

$I->amOnPage('/message');
$I->see('Finished', 'table#results td span.label');
$I->dontSee('In progress', 'table#results td span.label');
$I->dontSee('Halted', 'table#results td span.label');
$I->dontSee('Rerun', 'table#results td span.label');
$I->dontSee('Error', 'table#results td span.label');
$I->dontSee('', 'table#results tr[class="status-finished"] td:last-child a[title="Run message"]');
$I->dontSee('', 'table#results tr[class="status-finished"] td:last-child a[title="Set status Ready"]');
$I->dontSee('', 'table#results tr[class="status-finished"] td:last-child a[title="Halt message"]');
$I->dontSee('', 'table#results tr[class="status-finished"] td:last-child a[title="Cancel message"]');