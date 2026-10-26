<?php
require __DIR__ . '/includes/bootstrap.php';
if (admin_count() === 0) {
    redirect('setup-admin.php');
}
redirect(is_logged_in() ? 'dashboard.php' : 'login.php');
