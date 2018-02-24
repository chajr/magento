<?php

include_once('app/Mage.php');
Mage::app('admin');

try {
    /** @var Mage_Admin_Model_User $adminUserModel */
    $adminUserModel = Mage::getModel('admin/user');

    $adminUserModel->setUsername($argv[1])
        ->setFirstname($argv[1])
        ->setLastname($argv[2])
        ->setEmail($argv[3])
        ->setNewPassword($argv[4])
        ->setPasswordConfirmation($argv[4])
        ->setIsActive(true)
        ->save();

    $adminUserModel->setRoleIds([1])
        ->setRoleUserId($adminUserModel->getUserId())
        ->saveRelations();
    echo 'User' . $argv[1] . ' created.';
} catch (\Exception $e) {
    echo $e->getMessage();
}
