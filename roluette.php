<?php

session_start();

include_once "login-guard.php";
include_once "User.php";
include_once "Lobby.php";

$lobby= User::SessionUser()->GetActiveLobby();

$users = $lobby->Users();

var_dump($users);