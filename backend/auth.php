<?php
session_start();
require 'db.php';

function checkRole($role) {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== $role) {
        header('Location: ../index.php');
        exit;
    }
}

?>
