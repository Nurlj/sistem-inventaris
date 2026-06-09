<?php 

require 'db/functions.php';

session_unset();
session_destroy();
header("Location: login.php");
exit;