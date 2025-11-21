<?php
session_start();
require_once '../model/User.php';

$username = $_POST['username'];
$password = $_POST['password'];

$subj = new User();
$user = $subj->registrationUser($username,$password);

if($user){
    $_SESSION['user'] = $user;
    header('Location: ../index.php');
    exit();
}