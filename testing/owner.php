<?php

require_once 'testADSLInit.php';

/*
$owner = OwnerFactory::Create();

$owner->login = "TEST_001";
$owner->password = "aPassword";
print "*** Create\n\n";
$owner->create();


print "*** Read\n\n";
$owner->getByLogin('TEST_001');

//var_dump($owner);

print $owner->asXML();
$owner->password='aNewPassword';
$owner->name="My company";
print "*** Update\n\n";
$owner->update();
//var_dump($owner);

print "\n*** Owner List\n";
$ownerList = OwnerListFactory::Create();
//var_dump($ownerList);

print "\n*** Display\n";
$view = OwnerViewFactory::Create();
echo json_encode($view->listall());
//displayOwnerList();

print "\n*** Delete\n";
$owner->delete();
//var_dump($owner);

print "\n*** Realms ***\n";
$owner = OwnerFactory::Create();
$owner->getByLogin('Datapro');

var_dump($owner->getRealms());
return;
 * 
 */

$owner = new OwnerController();

print "*** Create\n\n";
$id = $owner->create(array('login' => 'TEST_001', 'password' => 'oldpassword'));

print "*** Update\n\n";
$owner->update(array('password' => 'newpassword', 'primaryemail' => 'this@an.tld'));

print "*** Read\n\n";
$owner->read($id);

print_r($owner);

print "\n*** Delete\n";
$owner->delete();

?>
