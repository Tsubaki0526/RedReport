<?php
session_start();
unset($_SESSION['portal_cliente']);
session_destroy();
header('Location: ../index.php');
