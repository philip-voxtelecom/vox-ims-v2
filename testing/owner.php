<?php

require_once 'testADSLInit.php';

$owner = OwnerFactory::Create('rux');

$owner->login = "TEST_001";
$owner->password = "aPassword";
$owner->create();
print "*** Create\n\n";
var_dump($owner);


$owner->read('TEST_001');
print "*** Read\n\n";
var_dump($owner);

$owner->password='aNewPassword';
$owner->name="My company";
$owner->update();
print "*** Update\n\n";
var_dump($owner);

$ownerList = OwnerListFactory::Create('rux');
print "*** Owner List\n\n";
var_dump($ownerList);

$owner->delete();
print "*** Delete\n\n";
var_dump($owner);

return;

?>
