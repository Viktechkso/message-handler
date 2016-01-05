<?php 
$I = new FunctionalTester($scenario);
$I->reloadFixtures();

$I->wantTo('see the scheduled processes list');

$I->amOnPage('/schedules');
$I->seeResponseCodeIs(200);

$I->see('Schedules', 'h1');

$I->seeLink('Create new');

$I->see('Cron time definition consists of five values', '.well');

$I->see('ID', 'th');
$I->see('Cron', 'th');
$I->see('Type', 'th');
$I->see('Description', 'th');
$I->see('Last run at', 'th');

$I->seeLink('Edit');
$I->seeLink('Delete');