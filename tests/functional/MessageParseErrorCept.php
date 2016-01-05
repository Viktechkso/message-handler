<?php 
$I = new FunctionalTester($scenario);
$I->reloadFixtures();

$I->wantTo('see JSON parsing errors on message details page');

//$I->amOnPage('/message');
//$I->click('Details', 'table#results tbody tr:nth-child(1)'); // first message on the list
//$I->seeResponseCodeIs(200);
//$I->see('Flow JSON parsing error', '.tab-content .alert-danger');
//$I->dontSeeElement('table#message-steps');

//$I->amOnPage('/message');
//$I->click('Details', 'table#results tbody tr:nth-child(2)'); // second message on the list
//$I->seeResponseCodeIs(200);
//$I->dontSee('JSON parsing error', '.entity-flow-message .alert-danger');
//$I->seeElement('table#message-steps');