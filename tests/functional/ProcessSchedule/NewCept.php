<?php
$I = new FunctionalTester($scenario);
$I->reloadFixtures();

$I->wantTo('add new schedule');

$I->amOnPage('/schedules');
$I->click('Create new');

$I->seeResponseCodeIs(200);

$I->see('Add scheduled process', 'h1');

$I->see('Enabled', 'label');
$I->see('Min');
$I->see('Hour');
$I->see('Day of Month');
$I->see('Month');
$I->see('Day of Week');
$I->see('Type');
$I->see('Description');
$I->see('Parameters');

$I->see('Create', '.btn');