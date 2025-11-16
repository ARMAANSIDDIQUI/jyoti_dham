<?php
require_once __DIR__ . '/admin_header.php';
?>
<div class="container-fluid">
    <h1 class="mt-4">Admin Dashboard</h1>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h2 class="text-center">Admin Dashboard</h2>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <a href="manage_users.php" class="btn btn-primary mb-3">Manage Users</a> <br>
                            <a href="../add-event.php" class="btn btn-primary mb-3">Add Event</a> <br>
                            <a href="../event-list.php" class="btn btn-primary mb-3">Edit Existing Event</a> <br>
                            <a href="manage_satsang.php" class="btn btn-primary mb-3">Manage Satsang</a> <br>
                            <!-- <a href="manage-news.php" class="btn btn-primary mb-3">Manage News</a> <br> -->
                            <a href="../index.php" class="btn btn-secondary mb-3">Homepage</a> <br>
                            <a href="../logout.php" class="btn btn-danger mb-3">Logout</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
require_once __DIR__ . '/admin_footer.php';
?>