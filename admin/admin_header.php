<?php
session_start();
if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../admin-login.php"); // Redirect to admin login page if not authorized
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Custom CSS for sidebar -->
    <style>
        #wrapper {
            display: flex;
        }
        #sidebar-wrapper {
            min-height: 100vh;
            margin-left: -15rem;
            transition: margin .25s ease-out;
            background-color: #343a40; /* Dark background for sidebar */
        }
        #sidebar-wrapper .sidebar-heading {
            padding: 0.875rem 1.25rem;
            font-size: 1.2rem;
            color: #fff;
        }
        #sidebar-wrapper .list-group {
            width: 15rem;
        }
        #page-content-wrapper {
            min-width: 100vw;
        }
        #wrapper.toggled #sidebar-wrapper {
            margin-left: 0;
        }
        @media (min-width: 768px) {
            #sidebar-wrapper {
                margin-left: 0;
            }
            #page-content-wrapper {
                min-width: 0;
                width: 100%;
            }
            #wrapper.toggled #sidebar-wrapper {
                margin-left: -15rem;
            }
        }
        .list-group-item {
            background-color: #343a40;
            color: #adb5bd;
        }
        .list-group-item:hover {
            background-color: #495057;
            color: #fff;
        }
        .list-group-item.active {
            background-color: #007bff;
            border-color: #007bff;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <div class="bg-dark border-right" id="sidebar-wrapper">
            <div class="sidebar-heading">Admin Panel</div>
            <div class="list-group list-group-flush">
                <a href="dashboard.php" class="list-group-item list-group-item-action">Dashboard</a>
                <a href="manage_users.php" class="list-group-item list-group-item-action">Manage Users</a>
                <a href="../add-event.php" class="list-group-item list-group-item-action">Add Event</a>
                <a href="../event-list.php" class="list-group-item list-group-item-action">Manage Events</a>
                <a href="manage_satsang.php" class="list-group-item list-group-item-action">Manage Satsang</a>
                <!-- <a href="#" class="list-group-item list-group-item-action">Manage News</a> -->
                <a href="../index.php" class="list-group-item list-group-item-action">View Homepage</a>
                <a href="../logout.php" class="list-group-item list-group-item-action">Logout</a>
            </div>
        </div>
        <!-- /#sidebar-wrapper -->

        <!-- Page Content -->
        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
                <button class="btn btn-primary" id="menu-toggle">Toggle Menu</button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ml-auto mt-2 mt-lg-0">
                        <li class="nav-item active">
                            <a class="nav-link" href="#">Welcome, Admin! <span class="sr-only">(current)</span></a>
                        </li>
                    </ul>
                </div>
            </nav>
            <div class="container-fluid">
