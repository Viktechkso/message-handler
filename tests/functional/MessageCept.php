<?php 
$I = new FunctionalTester($scenario);
$I->reloadFixtures();

/* MESSAGES LIST */

$I->wantTo('see messages list');
$I->amOnPage('/message');
$I->seeResponseCodeIs(200);
$I->see('Messages');
$I->see('Search', 'div.panel form button.btn-info');
$I->see('Status overview');
$I->seeLink('ID');
$I->seeLink('Created');
$I->seeLink('Type');
$I->seeLink('Status');
$I->see('Action');
$I->seeLink('Details');
$I->see('1.502', 'table tfoot');

# buttons
$I->seeLink('', '/message/set-status'); // for status buttons

# buttons: item with "Ready" status
$I->dontSeeElement('table#results tr.status-new a.btn-success span.glyphicon-check');
$I->seeElement('table#results tr.status-new a.btn-primary span.glyphicon-play');
$I->seeElement('table#results tr.status-new a.btn-warning span.glyphicon-pause');
$I->seeElement('table#results tr.status-new a.btn-danger span.glyphicon-remove');
$I->seeElement('table#results tr.status-new div.icon-button-placeholder-xs');

# buttons: item with "Halted" status
$I->seeElement('table#results tr.status-halted a.btn-primary span.glyphicon-play');
$I->seeElement('table#results tr.status-halted a.btn-success span.glyphicon-check');
$I->dontSeeElement('table#results tr.status-halted a.btn-warning span.glyphicon-pause');
$I->seeElement('table#results tr.status-halted a.btn-danger span.glyphicon-remove');
$I->seeElement('table#results tr.status-halted div.icon-button-placeholder-xs');

// coloured statuses on list
$I->see('New', 'td span[class="label label-default"]');
$I->see('Error', 'td span[class="label label-danger"]');
$I->see('In progress', 'td span[class="label label-primary"]');
$I->see('Halted', 'td span[class="label label-warning"]');
$I->see('New', 'td span[class="label label-default"]');
$I->see('Cancelled', 'td span[class="label label-default"]');


/* DETAILS PAGE */

$I->click('Details', 'table#results tbody tr:nth-child(3)');
$I->seeResponseCodeIs(200);
$I->see('Message');

# top links
$I->seeLink('Back to the list');
$I->seeLink('Set status New');
$I->seeLink('Halt message');
$I->seeLink('Cancel message');
$I->seeLink('Reset GUIDs');

# main fields
$I->see('ID');
$I->see('Type');
$I->see('Created');
$I->see('Message steps');
$I->see('Message status');
$I->see('Message');
$I->see('Completed steps');
$I->see('Run at');

# tabs
$I->seeElement('ul[class="nav nav-tabs"]'); // tabs with steps and step changes
$I->seeLink('Message steps');
$I->seeLink('Step changes');
$I->seeElement('div#message-steps.tab-pane.active');
$I->seeElement('div#message-changes.tab-pane');

# step fields
$I->see('Module name');
$I->see('GUID');
$I->see('Status');
$I->see('Errors');
$I->see('GUID', 'table#message-steps ul li');
$I->see('123-456-789', 'table#message-steps ul li a');
$I->see('Opportunity'); # module name
$I->see('Relation'); # badge for relation
$I->see('show/hide', 'table#message-steps a[data-target=".step-2-errors"]');
$I->dontSeeElement('table#message-steps table.collapsed');

$I->click('Set status New');
$I->seeResponseCodeIs(200);
$I->see('Message');
$I->seeLink('Back to the list');
$I->seeLink('Run message');
$I->dontSeeLink('Set status New');
$I->seeLink('Halt message');
$I->seeLink('Cancel message');
// steps with status "Error" are changed to status "Rerun":
$I->dontSee('Error', 'table#message-steps td.error-status');
$I->dontSee('Completed', 'table#message-steps td.error-status');
$I->dontSee('In progress', 'table#message-steps td.error-status');
$I->see('New', 'table#message-steps td.error-status');

$I->click('Halt message');
$I->seeResponseCodeIs(200);
$I->see('Message');
$I->seeLink('Back to the list');
$I->seeLink('Run message');
$I->seeLink('Set status New');
$I->dontSeeLink('Halt message');
$I->seeLink('Cancel message');

$I->click('Cancel message');
$I->seeResponseCodeIs(200);
$I->see('Message');
$I->seeLink('Back to the list');
$I->dontSeeLink('Run message');
$I->seeLink('Set status New');
$I->dontSeeLink('Halt message');
$I->dontSeeLink('Cancel message');