<?php
session_start();
session_unset();
session_destroy();
header("Location: login.php"); // Change 'login.html' to the correct file name
exit();
