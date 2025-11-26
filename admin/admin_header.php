<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../admin-login.php"); // Redirect to admin login page if not authorized
    exit();
}
// If execution reaches here, the user is logged in and is an admin, so we can safely output HTML.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="icon" href="../images/Logo.svg" type="image/svg+xml">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS for sidebar -->
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        #wrapper {
            display: flex;
        }
        #sidebar-wrapper {
            min-height: 100vh;
            margin-left: -15rem;
            transition: margin .25s ease-out;
            background: linear-gradient(135deg, #b3e5fc 0%, #e1bee7 100%);
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        #sidebar-wrapper .sidebar-heading {
            padding: 1rem 1.25rem;
            font-size: 1.4rem;
            color: #2e2e2e;
            font-weight: bold;
            border-bottom: 1px solid rgba(0,0,0,0.2);
        }
        #sidebar-wrapper .list-group {
            width: 15rem;
        }
        #page-content-wrapper {
            min-width: 100vw;
            padding-bottom: 5rem; /* Added to prevent content from touching the bottom */
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
            background-color: transparent;
            color: #424242;
            border: none;
            padding: 0.75rem 1.25rem;
            transition: all 0.3s ease;
        }
        .list-group-item:hover {
            background-color: rgba(255,255,255,0.5);
            color: #2e2e2e;
            transform: translateX(5px);
        }
        .list-group-item.active {
            background-color: rgba(255,255,255,0.7);
            border-left: 4px solid #66bb6a;
            color: #2e2e2e;
        }
        .list-group-item i {
            margin-right: 10px;
            width: 20px;
        }
        .navbar {
            background: linear-gradient(135deg, #b3e5fc 0%, #e1bee7 100%) !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .navbar .btn-primary {
            background-color: rgba(255,255,255,0.7);
            border: none;
            color: #2e2e2e;
        }
        .navbar .btn-primary:hover {
            background-color: rgba(255,255,255,0.9);
        }
        .navbar .nav-link {
            color: #2e2e2e !important;
            font-weight: 500;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        .btn {
            border-radius: 25px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 3px 10px rgba(0,0,0,0.15);
        }
        .table {
            border-radius: 10px;
            overflow: hidden;
        }
        .thead-dark {
            background: linear-gradient(135deg, #b3e5fc 0%, #e1bee7 100%);
            color: #2e2e2e;
        }
        .modal-content {
            border-radius: 15px;
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        .admin-btn {
            background-color: #cad1f1;
            color: #2e2e2e; /* Adjust text color for readability */
            border: none;
        }
        .admin-btn:hover {
            background-color: #a9b9ea;
            color: #2e2e2e; /* Adjust text color for readability */
        }
        /* Custom Tab Styling */
        .nav-tabs {
            border-bottom: none; /* Remove default border */
        }
        .nav-tabs .nav-item .nav-link {
            border: none;
            border-bottom: 3px solid transparent; /* default for inactive */
            background-color: transparent;
            color: #6c757d; /* Grey text for inactive */
            font-weight: normal;
            transition: all 0.3s ease;
        }
        .nav-tabs .nav-item .nav-link:hover {
            color: #495057; /* Slightly darker grey on hover */
            border-bottom-color: #e9ecef; /* Light border on hover */
        }
        .nav-tabs .nav-item .nav-link.active {
            color: #007bff; /* Blue text for active */
            font-weight: bold;
            border-bottom-color: #007bff; /* Blue bottom border for active */
            background-color: transparent;
        }

        @media (max-width: 767.98px) {
            .container-fluid {
                padding-left: 15px;
                padding-right: 15px;
            }
            .table-responsive {
                border-radius: 10px;
                box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            }
        }
    </style>
</head>
<body>
    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <div class="bg-dark border-right" id="sidebar-wrapper">
            <div class="sidebar-heading">Admin Panel</div>
            <div class="list-group list-group-flush">
                <a href="dashboard.php" class="list-group-item list-group-item-action"><i class="fas fa-tachometer-alt"></i>Dashboard</a>
                <a href="manage_users.php" class="list-group-item list-group-item-action"><i class="fas fa-users"></i>Manage Users</a>
                <!-- <a href="../add-event.php" class="list-group-item list-group-item-action"><i class="fas fa-calendar-plus"></i>Add Event</a> -->
                <a href="manage-events.php" class="list-group-item list-group-item-action"><i class="fas fa-calendar-alt"></i>Manage Events</a>
                <a href="manage_satsangs.php" class="list-group-item list-group-item-action"><i class="fas fa-video"></i>Manage Satsangs</a>
                <!-- <a href="#" class="list-group-item list-group-item-action"><i class="fas fa-newspaper"></i>Manage News</a> -->
                <a href="../index.php" class="list-group-item list-group-item-action"><i class="fas fa-home"></i>View Homepage</a>
                <a href="../logout.php" class="list-group-item list-group-item-action"><i class="fas fa-sign-out-alt"></i>Logout</a>
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
