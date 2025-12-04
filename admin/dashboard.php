<?php
require_once __DIR__ . '/admin_header.php';
?>
<div class="container-fluid" style="padding-top: 30px; margin-top: 20px;">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-md-12">
            <div class="card">
                <div class="card-header text-center" style="background: linear-gradient(135deg, #b3e5fc 0%, #e1bee7 100%); color: #2e2e2e;">
                    <h1 class="mb-0"><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h1>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 col-sm-12 mb-3">
                            <a href="manage_users.php" class="btn admin-btn btn-block d-flex align-items-center justify-content-center">
                                <i class="fas fa-users mr-2"></i>Manage Users
                            </a>
                        </div>
                        <!-- <div class="col-md-6 col-sm-12 mb-3">
                            <a href="../add-event.php" class="btn admin-btn btn-block d-flex align-items-center justify-content-center">
                                <i class="fas fa-calendar-plus mr-2"></i>Add Event
                            </a>
                        </div> -->
                        <div class="col-md-6 col-sm-12 mb-3">
                            <a href="../admin/manage-events.php" class="btn admin-btn btn-block d-flex align-items-center justify-content-center">
                                <i class="fas fa-calendar-alt mr-2"></i>Manage Events
                            </a>
                        </div>
                        <div class="col-md-6 col-sm-12 mb-3">
                            <a href="manage_satsang.php" class="btn admin-btn btn-block d-flex align-items-center justify-content-center">
                                <i class="fas fa-video mr-2"></i>Manage Livestreams
                            </a>
                        </div>
                        <div class="col-md-6 col-sm-12 mb-3">
                            <a href="../index.php" class="btn admin-btn btn-block d-flex align-items-center justify-content-center">
                                <i class="fas fa-home mr-2"></i>View Homepage
                            </a>
                        </div>
                        <div class="col-md-6 col-sm-12 mb-3">
                            <a href="../logout.php" class="btn admin-btn btn-block d-flex align-items-center justify-content-center">
                                <i class="fas fa-sign-out-alt mr-2"></i>Logout
                            </a>
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