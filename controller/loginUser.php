<?php
session_start();
require_once '../model/User.php';

$username = $_POST['username'];
$password = $_POST['password'];

$subj = new User();
$user = $subj->loginUser($username,$password);

if($user){
    $_SESSION['user'] = $user;
    header('Location: ../index.php');
    exit();
}else{
    echo 'не получится зайти';
}