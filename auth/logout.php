<?php
require_once '../functions.php';

session_start();
unset($_SESSION['user_id']);

redirect_to('/login.php');
