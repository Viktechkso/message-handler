<?php
$I = new FunctionalTester($scenario);
$I->reloadFixtures();

$I->wantTo('add new schedule');

$I->amOnPage('/schedules');
$I->click('Edit');

$I->seeResponseCodeIs(200);

$I->see('Edit scheduled process', 'h1');