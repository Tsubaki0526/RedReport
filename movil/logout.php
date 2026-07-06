<?php
session_start();
unset($_SESSION['movil_user']);
header('Location: login.php');
