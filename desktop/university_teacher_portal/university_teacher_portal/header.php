<?php require_once 'config.php'; require_login(); ?>
<!doctype html><html><head><title>University Teacher Portal</title><link rel="stylesheet" href="assets/style.css"></head><body>
<div class="top"><strong>University Teacher Portal</strong><div><span><?=h(current_user()['name'])?> (<?=h(current_user()['role'])?>)</span><a href="dashboard.php">Dashboard</a><a href="logout.php">Logout</a></div></div><div class="wrap">
