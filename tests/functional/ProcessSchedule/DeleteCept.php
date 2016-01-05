<?php
$I = new FunctionalTester($scenario);
$I->reloadFixtures();

$I->wantTo('add new schedule');

$I->amOnPage('/schedules');
$I->click('Delete');

$I->seeResponseCodeIs(200);

$I->see('Schedules', 'h1');

$I->see('Scheduled process successfully removed.', '.alert-success');