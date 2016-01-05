<?php 
$I = new FunctionalTester($scenario);
$I->reloadFixtures();

$I->wantTo('perform batch actions on Messages');
$I->amOnPage('/message');

# batch actions checkboxes on messages list, action select box, submit button
$I->seeElement('input[type="checkbox"].checkbox-select-all');
$I->seeElement('input[type="checkbox"].checkbox-select-all-item');
$I->seeElement('form#batch input[type="hidden"][name="action"]');
$I->seeElement('form#batch input[type="hidden"][name="action_type"]');
$I->see('Action for selected', 'form#batch button.dropdown-toggle');
$I->see('Action for all', 'form#batch button.dropdown-toggle');
$I->see('Set status New', 'div#batch-selected');
$I->see('Halt message', 'div#batch-selected');
$I->see('Cancel message', 'div#batch-selected');
$I->see('Reset GUIDs', 'div#batch-selected');
$I->see('Set status New', 'div#batch-all');
$I->see('Halt message', 'div#batch-all');
$I->see('Cancel message', 'div#batch-all');
$I->see('Reset GUIDs', 'div#batch-all');

# sending batch form with no action selected
$I->submitForm('form[name="batch"]', []);
$I->see('Please select batch action.', '.alert-danger');

# sending batch form with no action type selected
$I->submitForm('form[name="batch"]', ['action' => 'new']);
$I->see('Please select batch action type.', '.alert-danger');

# sending batch form with no Messages selected
//$I->submitForm('form[name="batch"]', ['action' => 'ready', 'action_type' => 'selected']);
//$I->see('Please select at least one Message.', '.alert-danger');

$I->see('New', 'table tr[class="status-new"] td');
$I->see('Cancelled', 'table tr[class="status-cancelled"] td');
$I->see('Halted', 'table tr[class="status-halted"] td');
$I->dontSee('Finished', 'table tr[class="status-halted"] td');
$I->dontSee('Error', 'table tr[class="status-halted"] td');

# sending batch form with action for all items
$I->submitForm('form[name="batch"]', ['action' => 'new', 'action_type' => 'all']);
$I->see('New', 'table tr[class="status-new"] td');
$I->see('Batch action executed on 1502 items.', '.alert-success');
$I->dontSee('Cancelled', 'table tr[class="status-cancelled"] td');
$I->dontSee('Halted', 'table tr[class="status-halted"] td');
$I->dontSee('Finished', 'table tr[class="status-halted"] td');
$I->dontSee('Error', 'table tr[class="status-halted"] td');

$I->see('New', 'div#status-overview td');
$I->dontSee('Halted', 'div#status-overview td');
$I->dontSee('Cancelled', 'div#status-overview td');

# sending batch form with Messages IDs and action for selected items
$ids = $I->getMessageIds(5);

$I->submitForm('form[name="batch"]', [
    'action' => 'halt',
    'action_type' => 'selected',
    'batch_ids' => $ids
]);

$I->see('Batch action executed on 5 items.', '.alert-success');
$I->see('New', 'div#status-overview td');
$I->see('Halted', 'div#status-overview td');
$I->dontSee('Cancelled', 'div#status-overview td');
$I->dontSee('Error', 'div#status-overview td');