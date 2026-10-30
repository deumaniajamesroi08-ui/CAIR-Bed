<?php require_once __DIR__ . '/auth.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? h($pageTitle) : 'CAIR-Bed Admin'; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #111827 45%, #0b1120 100%);
            color: #e5eefb;
            min-height: 100vh;
        }
        a { color: inherit; text-decoration: none; }
        .app { display: flex; min-height: 100vh; }
        .sidebar {
            width: 270px;
            background: rgba(15, 23, 42, 0.82);
            border-right: 1px solid rgba(148, 163, 184, 0.18);
            backdrop-filter: blur(16px);
            position: sticky; top: 0; height: 100vh;
            padding: 24px 18px;
        }
        .brand { display: flex; gap: 12px; align-items: center; margin-bottom: 24px; }
        .brand-icon {
            width: 48px; height: 48px; border-radius: 14px;
            background: linear-gradient(135deg, #14b8a6, #22c55e);
            display: flex; align-items: center; justify-content: center;
            color: white; font-weight: 800; font-size: 20px;
        }
        .brand h2 { margin: 0; font-size: 19px; }
        .brand p { margin: 4px 0 0; color: #94a3b8; font-size: 12px; }
        .nav-group { display: grid; gap: 8px; }
        .nav-link {
            padding: 12px 14px; border-radius: 14px;
            color: #dbeafe; background: rgba(255,255,255,0.03);
            border: 1px solid transparent; transition: .2s ease;
            font-size: 14px; font-weight: 600;
        }
        .nav-link:hover, .nav-link.active {
            background: rgba(20, 184, 166, 0.15);
            border-color: rgba(45, 212, 191, 0.25);
            transform: translateX(2px);
        }
        .sidebar-footer {
            margin-top: 28px; padding: 14px; border-radius: 16px;
            background: rgba(255,255,255,0.04); color: #cbd5e1; font-size: 13px;
        }
        .content { flex: 1; padding: 24px; }
        .topbar {
            display: flex; justify-content: space-between; gap: 16px; align-items: center;
            margin-bottom: 18px;
        }
        .title h1 { margin: 0; font-size: 28px; }
        .title p { margin: 6px 0 0; color: #93c5fd; font-size: 14px; }
        .admin-chip {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(148, 163, 184, 0.18);
            border-radius: 16px; padding: 12px 14px; min-width: 220px;
        }
        .admin-chip .name { font-weight: 700; }
        .admin-chip .role { color: #94a3b8; font-size: 13px; margin-top: 4px; }
        .grid-cards {
            display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px;
            margin-bottom: 18px;
        }
        .card, .panel {
            background: rgba(15, 23, 42, 0.72);
            border: 1px solid rgba(148, 163, 184, 0.15);
            border-radius: 20px; padding: 18px;
            box-shadow: 0 12px 30px rgba(0,0,0,0.18);
        }
        .card-label { color: #94a3b8; font-size: 13px; margin-bottom: 10px; }
        .card-value { font-size: 30px; font-weight: 800; }
        .card-sub { color: #93c5fd; font-size: 12px; margin-top: 8px; }
        .two-col { display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 16px; margin-bottom: 18px; }
        .panel h3 { margin: 0 0 14px; font-size: 18px; }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 10px; text-align: left; border-bottom: 1px solid rgba(148,163,184,0.12); font-size: 14px; }
        th { color: #93c5fd; font-weight: 700; }
        .badge {
            display: inline-flex; align-items: center; justify-content: center;
            padding: 6px 10px; border-radius: 999px; font-size: 12px; font-weight: 700;
        }
        .badge.normal, .badge.resolved, .badge.active { background: rgba(34, 197, 94, 0.14); color: #4ade80; }
        .badge.warning, .badge.pending { background: rgba(245, 158, 11, 0.14); color: #fbbf24; }
        .badge.critical, .badge.unread, .badge.inactive { background: rgba(239, 68, 68, 0.14); color: #f87171; }
        .toolbar { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 14px; }
        .toolbar input, .toolbar select {
            background: rgba(255,255,255,0.05); color: #e5eefb; border: 1px solid rgba(148,163,184,0.15);
            border-radius: 12px; padding: 10px 12px; min-width: 180px;
        }
        .btn {
            background: linear-gradient(135deg, #0ea5e9, #14b8a6); color: white; border: 0;
            border-radius: 12px; padding: 10px 14px; font-weight: 700; cursor: pointer;
        }
        .btn.secondary { background: rgba(255,255,255,0.05); border: 1px solid rgba(148,163,184,0.15); }
        .alert-item {
            border: 1px solid rgba(148,163,184,0.15); border-radius: 16px; padding: 12px; margin-bottom: 10px;
            background: rgba(255,255,255,0.03);
        }
        .muted { color: #94a3b8; }
        .form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; }
        .form-group label { display:block; margin-bottom: 8px; color: #93c5fd; font-size: 13px; }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%; background: rgba(255,255,255,0.05); color: #e5eefb; border: 1px solid rgba(148,163,184,0.15);
            border-radius: 12px; padding: 11px 12px; outline: none;
        }
        .form-group textarea { min-height: 120px; resize: vertical; }
        .span-2 { grid-column: span 2; }
        .message-box { margin-bottom: 14px; padding: 12px 14px; border-radius: 12px; display:none; }
        .message-success { background: rgba(34,197,94,0.14); color: #4ade80; }
        .message-error { background: rgba(239,68,68,0.14); color: #fca5a5; }
        @media (max-width: 1100px) { .grid-cards { grid-template-columns: repeat(2, minmax(0, 1fr)); } .two-col { grid-template-columns: 1fr; } }
        @media (max-width: 860px) {
            .app { flex-direction: column; }
            .sidebar { width: 100%; height: auto; position: relative; }
            .content { padding: 16px; }
            .form-grid, .grid-cards { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="app">
    <aside class="sidebar">
        <div class="brand">
            <div class="brand-icon">C</div>
            <div>
                <h2>CAIR-Bed</h2>
                <p>Admin Monitoring Panel</p>
            </div>
        </div>
        <div class="nav-group">
            <a class="nav-link <?php echo ($activePage === 'dashboard') ? 'active' : ''; ?>" href="index.php">Dashboard</a>
            <a class="nav-link <?php echo ($activePage === 'patients') ? 'active' : ''; ?>" href="patients.php">Patient Monitoring</a>
            <a class="nav-link <?php echo ($activePage === 'alerts') ? 'active' : ''; ?>" href="alerts.php">Alerts</a>
            <a class="nav-link <?php echo ($activePage === 'users') ? 'active' : ''; ?>" href="users.php">Users</a>
            <a class="nav-link <?php echo ($activePage === 'reports') ? 'active' : ''; ?>" href="reports.php">Reports</a>
            <a class="nav-link <?php echo ($activePage === 'settings') ? 'active' : ''; ?>" href="settings.php">Settings</a>
            <a class="nav-link" href="../index.php">Back to Login</a>
        </div>
        <div class="sidebar-footer">
            Logged in as <strong><?php echo h($_SESSION['user'] ?? 'Admin'); ?></strong><br>
            Role: <?php echo h($_SESSION['role'] ?? 'admin'); ?>
        </div>
    </aside>
    <main class="content">
        <div class="topbar">
            <div class="title">
                <h1><?php echo isset($pageHeading) ? h($pageHeading) : 'Admin Dashboard'; ?></h1>
                <p><?php echo isset($pageDescription) ? h($pageDescription) : 'Monitor patients, alerts, users, and reports.'; ?></p>
            </div>
            <div class="admin-chip">
                <div class="name"><?php echo h($_SESSION['user'] ?? 'Admin'); ?></div>
                <div class="role">Administrator</div>
            </div>
        </div>
