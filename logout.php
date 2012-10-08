<?php
session_start();
unset($_SESSION['UserId']);
unset($_SESSION['UserProfile']);
session_destroy();

header("Location: index.php");