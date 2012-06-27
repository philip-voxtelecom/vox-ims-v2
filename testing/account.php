<?php

require_once 'testADSLInit.php';
/*
  $account = AccountFactory::Create();

  $account->read('23353457');

  var_dump($account);
 */

/*
  $accountlist = AccountListFactory::Create();
  $accounts = $accountlist->getList();

  print count($accounts);
 */


$account = new AccountController();


print "\n*** Creating ***\n";
try {
    $id = $account->create(array(
        'username' => 'test011@blvox.co.za',
        'password' => 'aPassword',
        'bundlesize' => '5',
        'product' => '10',
        'systemReference' => 'MyReference',
            ));
    print "Account created with id $id\n";
} catch (Exception $e) {
    print "Could not create account: " . $e->getMessage() . "\n";
}


print "\n*** Finding ***\n";
$id = $account->findByUsername('test011@blvox.co.za');

if (!$id) {
    print "account not found\n";
} else {
    print "Found account with id $id\n";
}

print "\n*** Reading ***\n";
if ($id = $account->read($id)) {
    print "Found account with id $id\n";
} else {
    print "Account not found\n";
}




print "\n*** Updating ***\n";
$account->update(array('notifyemail' => 'philip@interprise.co.za', 'notifycell' => '0826002815'));

print "\n*** Reading ***\n";
$account->read($id);



print "\n*** Deleting ***\n";
if (isset($id) and $account->delete($id)) {
    print "Deleted account id $id";
}




print "\n*** Reading ***\n";
try {
$account->read($id);
} catch (Exception $e) {
    print $e->getMessage()."\n";
}

print "\n*** Account List ***\n";
$list = $account->listall();
print "There are ".count($list)." accounts\n"


?>
