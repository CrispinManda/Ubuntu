<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login"); // Redirect to login if not logged in
    exit();
}