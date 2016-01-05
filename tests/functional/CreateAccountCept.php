<?php
$I = new FunctionalTester($scenario);
$I->mockMuleConnectorServiceForVatStatus();

$I->reloadFixtures();

$I->wantTo('create account');

$I->amOnPage('/account/create');
$I->seeResponseCodeIs(200);

$I->see('Create Account', '#top-menu');

$I->seeElement('form');
$I->see('VAT country', 'form');
$I->see('VAT number', 'form');
$I->see('DK', 'form select option');
$I->click('Create Account', 'form button');

$I->seeResponseCodeIs(200);
$I->see('This value should not be blank.');

$I->submitForm('form', [
    'create_account' => [
        'vatCountry' => 'DK',
        'vatNumber' => '00000000'
    ]
]);

$I->see('This value should not be equal to &quot;00000000&quot;.');

$I->submitForm('form', [
    'create_account' => [
        'vatCountry' => 'DK',
        'vatNumber' => '12345'
    ]
]);

//$I->see('VAT Status', 'h2');
//$I->seeElement('#status-table');
//$I->see('Error', '#status-table td');
//$I->see('Duplicate', '#status-table td');
//$I->see('Success', '#status-table td');
//$I->see('Back', 'a.btn');