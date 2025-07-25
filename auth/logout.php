<?php
require_once '../includes/init.php';

Auth::logout();
header('Location: ../auth/login.php');
exit;
