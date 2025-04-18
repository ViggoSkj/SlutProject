<?php

session_start();

include_once "User.php";
include_once "login-guard";

$user = User::SessionUser();

$user->LeaveLobby();

header("Location: /index.php");