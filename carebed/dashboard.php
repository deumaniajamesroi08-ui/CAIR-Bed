
<?php
session_start();
include 'script/db_connection.php';

$user_id = $_SESSION['user_id'] ?? 0;

$stmt = $conn->prepare("
    SELECT 
        u.id,
        u.username,
        p.fullname,
        p.age
    FROM tbl_user u
    LEFT JOIN tbl_profile p ON p.profile_id = u.id
    WHERE u.id = ?
    LIMIT 1
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

$displayName = !empty($user['fullname'])
    ? $user['fullname']
    : ($user['username'] ?? '');

$age = !empty($user['age'])
    ? $user['age']
    : 'N/A';

$bedId = 'BED-' . str_pad(($user['id'] ?? 0), 3, '0', STR_PAD_LEFT);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cairbed System</title>
    <meta name="description" content="Carebed System">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="templatemo-glass-admin-style.css">

    <style>
        .sidebar-header {
            display: flex;
            align-items: center;
            gap: 12px;
            padding-bottom: 30px;
            border-bottom: 1px solid var(--glass-border);
            margin-bottom: 30px;
        }

        .logo {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, var(--emerald), var(--gold));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(5, 150, 105, 0.3);
        }

        .logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
        }

        .logo-text {
            font-size: 22px;
            font-weight: 600;
            background: linear-gradient(135deg, var(--emerald-light), var(--gold));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .patient-overview-card {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            margin: 0 0 24px 0;
            padding: 22px 26px;
            border-radius: 26px;
            background: linear-gradient(90deg, rgba(37, 54, 89, 0.55), rgba(16, 98, 85, 0.22));
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.18);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
        }

        .patient-overview-left {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 8px;
        }

        .patient-name {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
            color: #ffffff;
        }

        .patient-status {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 6px 14px;
            border-radius: 999px;
            background: rgba(34, 197, 94, 0.16);
            border: 1px solid rgba(34, 197, 94, 0.20);
            color: #d7ffe4;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.4px;
            text-transform: uppercase;
        }

        .patient-meta {
            margin: 0;
            font-size: 15px;
            color: rgba(255, 255, 255, 0.78);
            line-height: 1.5;
        }

        .patient-meta strong {
            color: #ffffff;
            font-weight: 700;
        }

        .patient-overview-right {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .patient-action-btn {
            border: 1px solid rgba(255, 255, 255, 0.10);
            border-radius: 16px;
            padding: 12px 18px;
            font-size: 14px;
            font-weight: 600;
            color: #ffffff;
            background: transparent;
            cursor: pointer;
            transition: 0.25s ease;
        }

        .acknowledge-btn {
            background: rgba(22, 101, 52, 0.18);
        }

        .sos-btn {
            background: rgba(127, 29, 29, 0.22);
        }

        .patient-action-btn:hover {
            transform: translateY(-1px);
        }

        @media (max-width: 900px) {
            .patient-overview-card {
                flex-direction: column;
                align-items: flex-start;
            }

            .patient-overview-right {
                width: 100%;
            }
        }
    </style>
    
</head>
<body>
    
    <div class="background"></div>
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <div class="dashboard">

        
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <img src="images/cairbed.png" alt="Cairbed Logo">
                </div>
                <span class="logo-text">Cairbed Dash</span>
            </div>

            <ul class="nav-menu">
                <li class="nav-section">
                    <span class="nav-section-title">Main Menu</span>
                    <ul>
                        <li class="nav-item">
                            <a href="dashboard.php" class="nav-link active">
                                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="3" width="7" height="7" rx="1"/>
                                    <rect x="14" y="3" width="7" height="7" rx="1"/>
                                    <rect x="3" y="14" width="7" height="7" rx="1"/>
                                    <rect x="14" y="14" width="7" height="7" rx="1"/>
                                </svg>
                                Dashboard
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a href="users.php" class="nav-link">
                                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                    <circle cx="9" cy="7" r="4"/>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                </svg>
                                Patients
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="alerts.php" class="nav-link">
                                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                    <circle cx="9" cy="7" r="4"/>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                </svg>
                                Alerts
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a href="settings.php" class="nav-link">
                                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="3"/>
                                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                                </svg>
                                Settings
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-section">
                    <span class="nav-section-title">Account</span>
                    <ul>
                        <li class="nav-item">
                            <a href="index.php" class="nav-link">
                                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                                    <polyline points="16 17 21 12 16 7"/>
                                    <line x1="21" y1="12" x2="9" y2="12"/>
                                </svg>
                                Logout
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </aside>

        
        <main class="main-content">
            
            <nav class="navbar">
                <h1 class="page-title">Dashboard Overview</h1>
                <div class="navbar-right">
                    <div class="search-box">
                        <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"/>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                        <input type="text" class="search-input" placeholder="Search anything...">
                    </div>
                    
                    <div class="carebed-notification-wrapper">
    <button class="carebed-notification-bell" type="button" title="Notifications">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
            <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
        </svg>
        <span class="carebed-notification-badge">0</span>
    </button>
    <div class="carebed-notification-panel">
        <div class="carebed-notification-head">
            <h2>Notifications</h2>
            <span class="carebed-notification-live">LIVE</span>
        </div>
        <div class="carebed-notification-tabs">
            <span>All</span>
            <span>Unread</span>
        </div>
        <div class="carebed-notification-count-text">0 unread</div>
        <div class="carebed-notification-list">
            <div class="carebed-notification-empty">Loading notifications...</div>
        </div>
        <div class="carebed-notification-foot">
            <a href="alerts.php">View alerts</a>
            <a href="settings.php">Settings</a>
        </div>
    </div>
</div>
<button class="nav-btn" id="theme-toggle" title="Toggle Light/Dark Mode">
                        <svg class="icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="4"/>
                            <path d="M12 2v2"/><path d="M12 20v2"/>
                            <path d="M4.93 4.93l1.41 1.41"/><path d="M17.66 17.66l1.41 1.41"/>
                            <path d="M2 12h2"/><path d="M20 12h2"/>
                            <path d="M6.34 17.66l-1.41 1.41"/><path d="M19.07 4.93l-1.41 1.41"/>
                        </svg>
                        <svg class="icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: none;">
                            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                        </svg>
                    </button>
                </div>
            </nav>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const themeToggle = document.getElementById("theme-toggle");
    const sunIcon = document.querySelector(".icon-sun");
    const moonIcon = document.querySelector(".icon-moon");
    const root = document.documentElement; 

    if (!themeToggle) return;

    
    const savedTheme = localStorage.getItem("theme") || "dark";
    root.setAttribute("data-theme", savedTheme);

    if (savedTheme === "light") {
        if (sunIcon) sunIcon.style.display = "none";
        if (moonIcon) moonIcon.style.display = "block";
    } else {
        if (sunIcon) sunIcon.style.display = "block";
        if (moonIcon) moonIcon.style.display = "none";
    }

    themeToggle.addEventListener("click", function () {
        const currentTheme = root.getAttribute("data-theme");
        const newTheme = currentTheme === "light" ? "dark" : "light";

        root.setAttribute("data-theme", newTheme);
        localStorage.setItem("theme", newTheme);

        if (newTheme === "light") {
            if (sunIcon) sunIcon.style.display = "none";
            if (moonIcon) moonIcon.style.display = "block";
        } else {
            if (sunIcon) sunIcon.style.display = "block";
            if (moonIcon) moonIcon.style.display = "none";
        }
    });
});
</script>


           <div class="patient-overview-card">
    <div class="patient-overview-left">
        <div class="patient-name-status">
           <h2 class="patient-name" id="patient-name">
   <?php echo htmlspecialchars($displayName ?? 'Unknown Patient'); ?>
</h2>   
            <span class="patient-status normal" id="patient-status">NORMAL</span>
        </div>

        <p class="patient-meta">
            Bed ID: <strong id="patient-bed-id">
    <?php echo htmlspecialchars($bedId); ?>
</strong>•
            Age: <strong id="patient-age">
    <?php echo htmlspecialchars($age); ?>
</strong> •
            Last updated: <strong id="patient-last-updated">Just now</strong>
        </p>
    </div>

    <div class="patient-overview-right">
        <button class="patient-action-btn acknowledge-btn">Acknowledge Alerts</button>
        <button class="patient-action-btn sos-btn">SOS Call</button>
    </div>
</div>

<style>
.patient-name-status {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}

.patient-status {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 34px;
    padding: 6px 14px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.4px;
    text-transform: uppercase;
    white-space: nowrap;
    line-height: 1;
}

.patient-status.normal {
    background: rgba(34, 197, 94, 0.16);
    border: 1px solid rgba(34, 197, 94, 0.20);
    color: #d7ffe4;
}

.patient-status.warning {
    background: rgba(245, 158, 11, 0.16);
    border: 1px solid rgba(245, 158, 11, 0.20);
    color: #ffe7b0;
}

.patient-status.critical {
    background: rgba(239, 68, 68, 0.16);
    border: 1px solid rgba(239, 68, 68, 0.20);
    color: #ffd2d2;
}

@media (max-width: 768px) {
    .patient-name-status {
        gap: 8px;
    }

    .patient-status {
        padding: 5px 12px;
        font-size: 11px;
        min-height: 30px;
    }
}
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {

    function timeAgo(datetimeString) {
        const now = new Date();
        const updatedTime = new Date(datetimeString);
        const diffInSeconds = Math.floor((now - updatedTime) / 1000);

        if (diffInSeconds < 60) return "Just now";
        if (diffInSeconds < 3600) return Math.floor(diffInSeconds / 60) + " minute(s) ago";
        if (diffInSeconds < 86400) return Math.floor(diffInSeconds / 3600) + " hour(s) ago";
        return Math.floor(diffInSeconds / 86400) + " day(s) ago";
    }

    function updatePatientOverview() {
        $.ajax({
            url: 'patient_data.php',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data.status === "success") {
                    $('#patient-name').text(data.patient_name);
                    $('#patient-bed-id').text(data.bed_id);
                    $('#patient-age').text(data.age);
                    $('#patient-last-updated').text(timeAgo(data.last_updated));

                    $('#patient-status')
                        .text(data.patient_status)
                        .removeClass('normal warning critical')
                        .addClass(data.patient_status_class);
                }
            },
            error: function(xhr, status, error) {
                console.log("AJAX Error:", error);
                console.log(xhr.responseText);
            }
        });
    }

    updatePatientOverview();
    setInterval(updatePatientOverview, 5000);

});
</script>
            
            <section class="stats-grid">
                <div class="glass-card glass-card-3d stat-card">
                    <div class="stat-card-inner">
                        <div class="stat-info">
                            <h3>Heart Rate</h3>
                            <div class="stat-value" id="heart-rate-value">---</div>
                            <span class="stat-change positive">Baseline 60-100</span>
                        </div>
                        <div class="stat-icon cyan"> 
                            
                            <svg viewBox="0 0 24 24" fill="none" stroke="var(--emerald-light)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21l7.8-7.6 1-1a5.5 5.5 0 0 0 0-7.8z"/>
                                <polyline points="3 12 7 12 9 8 12 16 15 10 17 12 21 12"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="glass-card glass-card-3d stat-card">
                    <div class="stat-card-inner">
                        <div class="stat-info">
                            <h3>TEMPERATURE</h3>
                            <div class="stat-value" id="temperature-value">-- °C</div>
                           <span id="temperature-status">Normal Temperature</span>

                        </div>
                        <div class="stat-icon magenta">
                            
                            <svg viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 14.76V5a4 4 0 0 0-8 0v9.76a6 6 0 1 0 8 0z"/>
                                <line x1="10" y1="9" x2="10" y2="17"/>
                                <circle cx="10" cy="17" r="2"/>
                                <line x1="16" y1="7" x2="21" y2="7"/>
                                <line x1="16" y1="11" x2="19" y2="11"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="glass-card glass-card-3d stat-card">
                    <div class="stat-card-inner">
                        <div class="stat-info">
                            <h3>WETNESS</h3>
                            <div class="stat-text" id="wetness-value" style="font-size: 24px; font-weight: bold;">--</div>
                           <span id="wetness-status">No wetness detected</span>
                        </div>
                        <div class="stat-icon purple">
                            
                            <svg viewBox="0 0 24 24" fill="none" stroke="var(--coral)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 2.5S5 10 5 15a7 7 0 0 0 14 0c0-5-7-12.5-7-12.5z"/>
                                <path d="M9 16a3 3 0 0 0 3 3"/>
                                <path d="M18.5 4.5l2 2"/>
                                <path d="M21 2l-2 2"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="glass-card glass-card-3d stat-card">
                    <div class="stat-card-inner">
                        <div class="stat-info">
                           <h3>PRESENCE DETECTION</h3>
                            <div class="stat-text" id="presence-value" style="font-size: 24px; font-weight: bold;">--</div>
                            <span id="presence-status">Waiting for sensor</span>
                        </div>
                        <div class="stat-icon success">
                            
                            <svg viewBox="0 0 24 24" fill="none" stroke="var(--success)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                                <path d="M17 11l2 2 4-4"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <script>
                function updateRealtimeStats() {
                    fetch('script/get_latest_patient_data.php?t=' + Date.now())
                        .then(function(response) { return response.json(); })
                        .then(function(data) {
                            if (!data || data.status !== 'success') {
                                console.log('Realtime response:', data);
                                return;
                            }

                            document.getElementById('heart-rate-value').textContent = (data.heart_rate ?? '--') + ' bpm';
                            document.getElementById('temperature-value').textContent = (data.temperature ?? '--') + ' °C';
                            document.getElementById('wetness-value').textContent = data.wetness || '--';
                            document.getElementById('presence-value').textContent = data.presence_detection || '--';

                            const temperatureStatus = document.getElementById('temperature-status');
                            const wetnessStatus = document.getElementById('wetness-status');
                            const presenceStatus = document.getElementById('presence-status');
                            const lastUpdated = document.getElementById('patient-last-updated');

                            if (temperatureStatus) temperatureStatus.textContent = data.temperature_status || '--';
                            if (wetnessStatus) wetnessStatus.textContent = data.wetness_status || '--';
                            if (presenceStatus) presenceStatus.textContent = data.presence_status || '--';
                            if (lastUpdated) lastUpdated.textContent = data.timestamp || 'No data';
                        })
                        .catch(function(error) {
                            console.log('Error fetching realtime stats:', error);
                        });
                }

                updateRealtimeStats();
                setInterval(updateRealtimeStats, 3000);
                </script>
            </section>

            
            <section class="vital-monitor-grid">
                <div class="glass-card vital-monitor-card">
                    <div class="card-header vital-header">
                        <div>
                            <h2 class="card-title">Realtime Vital Signs Monitor</h2>
                            <p class="card-subtitle"><span class="live-pulse-dot"></span>ESP32 live graph starts at 0 until sensor data is received</p>
                        </div>
                        <div class="vital-tabs">
                            <button class="card-btn active" data-metric="heart_rate">Heart</button>
                            <button class="card-btn" data-metric="temperature">Temp</button>
                            <button class="card-btn" data-metric="wetness">Wetness</button>
                            <button class="card-btn" data-metric="presence">Presence</button>
                        </div>
                    </div>

                    <div class="vital-monitor-body">
                        <div class="vital-graph-panel">
                            <div class="graph-title-row">
                                <span id="activeGraphTitle">Heart Rate Graph</span>
                                <span id="graphCurrentValue">0 bpm</span>
                            </div>

                            <svg class="realtime-svg" viewBox="0 0 620 260" preserveAspectRatio="none">
                                <defs>
                                    <linearGradient id="lineGlow" x1="0" x2="1" y1="0" y2="0">
                                        <stop offset="0%" stop-color="#22c55e" />
                                        <stop offset="50%" stop-color="#67e8f9" />
                                        <stop offset="100%" stop-color="#f0abfc" />
                                    </linearGradient>
                                </defs>
                                <g class="grid-lines">
                                    <line x1="0" y1="52" x2="620" y2="52"></line>
                                    <line x1="0" y1="104" x2="620" y2="104"></line>
                                    <line x1="0" y1="156" x2="620" y2="156"></line>
                                    <line x1="0" y1="208" x2="620" y2="208"></line>
                                </g>
                                <polyline id="realtimeLine" points="0,240 56,240 112,240 168,240 224,240 280,240 336,240 392,240 448,240 504,240 560,240 620,240"></polyline>
                            </svg>

                            <div class="graph-footer-labels">
                                <span>Older</span>
                                <span>Live updating every 3s</span>
                                <span>Latest</span>
                            </div>
                        </div>

                        <div class="vital-text-panel">
                            <div class="vital-big-value">
                                <span id="monitorPrimaryValue">0</span>
                                <small id="monitorPrimaryUnit">bpm</small>
                            </div>
                            <p class="monitor-patient" id="monitorPatientName">Waiting for patient data</p>

                            <div class="mini-vitals-grid">
                                <div class="mini-vital-box">
                                    <span>Heart Rate</span>
                                    <strong id="monitorHeartRate">0 bpm</strong>
                                </div>
                                <div class="mini-vital-box">
                                    <span>Temperature</span>
                                    <strong id="monitorTemperature">0 °C</strong>
                                </div>
                                <div class="mini-vital-box">
                                    <span>Wetness</span>
                                    <strong id="monitorWetness">Dry</strong>
                                </div>
                                <div class="mini-vital-box">
                                    <span>Presence</span>
                                    <strong id="monitorPresence">Not Detected</strong>
                                </div>
                            </div>

                            <div class="monitor-status-row">
                                <span class="status-badge completed" id="monitorStatus">Setup Mode</span>
                                <span class="monitor-time" id="monitorLastUpdated">No sensor data yet</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="glass-card sensor-summary-card">
                    <div class="card-header">
                        <div>
                            <h2 class="card-title">Realtime Sensor Feed</h2>
                            <p class="card-subtitle">Latest ESP32 health updates</p>
                        </div>
                    </div>

                    <div class="sensor-feed-list" id="sensorFeedList">
                        <div class="sensor-feed-item">
                            <div class="sensor-feed-icon">HR</div>
                            <div>
                                <strong>Heart Rate</strong>
                                <p>Waiting for ESP32 data...</p>
                            </div>
                        </div>
                        <div class="sensor-feed-item">
                            <div class="sensor-feed-icon temp">TP</div>
                            <div>
                                <strong>Temperature</strong>
                                <p>Waiting for ESP32 data...</p>
                            </div>
                        </div>
                        <div class="sensor-feed-item">
                            <div class="sensor-feed-icon wet">WT</div>
                            <div>
                                <strong>Wetness</strong>
                                <p>Waiting for ESP32 data...</p>
                            </div>
                        </div>
                        <div class="sensor-feed-item">
                            <div class="sensor-feed-icon presence">PR</div>
                            <div>
                                <strong>Presence Detection</strong>
                                <p>Waiting for ESP32 data...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <style>
                .vital-monitor-grid{
                    display:grid;
                    grid-template-columns:minmax(0, 1.45fr) minmax(340px, .75fr);
                    gap:24px;
                    margin-bottom:28px;
                }
                .vital-monitor-card,.sensor-summary-card{padding:26px;}
                .vital-header{gap:18px;}
                .vital-tabs{display:flex;gap:10px;flex-wrap:wrap;}
                .live-pulse-dot{width:10px;height:10px;border-radius:50%;display:inline-block;background:#22c55e;margin-right:8px;box-shadow:0 0 14px #22c55e;animation:livePulse 1s infinite;}
                @keyframes livePulse{50%{opacity:.35;transform:scale(.85)}}
                .vital-monitor-body{display:grid;grid-template-columns:1.15fr .85fr;gap:24px;align-items:stretch;margin-top:18px;}
                .vital-graph-panel{min-height:340px;border-radius:22px;background:rgba(5,15,25,.22);border:1px solid rgba(255,255,255,.08);padding:22px;box-shadow:inset 0 0 40px rgba(34,197,94,.05);}
                .graph-title-row{display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;font-weight:700;color:var(--text-primary);}
                .realtime-svg{width:100%;height:260px;filter:drop-shadow(0 0 10px rgba(103,232,249,.45));}
                .grid-lines line{stroke:rgba(255,255,255,.08);stroke-width:1;}
                .graph-footer-labels{display:flex;justify-content:space-between;color:var(--text-muted);font-size:12px;margin-top:10px;}
                .vital-text-panel{border-radius:22px;background:linear-gradient(135deg,rgba(34,197,94,.12),rgba(212,165,116,.10));border:1px solid rgba(255,255,255,.08);padding:24px;display:flex;flex-direction:column;justify-content:center;}
                .vital-big-value{font-family:'Space Mono',monospace;font-size:64px;font-weight:800;line-height:1;color:#eafff4;text-shadow:0 0 20px rgba(34,197,94,.25);}
                .vital-big-value small{font-size:22px;color:var(--text-muted);margin-left:8px;}
                .monitor-patient{color:var(--text-muted);margin:12px 0 22px;font-weight:600;}
                .mini-vitals-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px;}
                .mini-vital-box{padding:16px;border-radius:16px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.08);}
                .mini-vital-box span{display:block;color:var(--text-muted);font-size:12px;text-transform:uppercase;margin-bottom:8px;}
                .mini-vital-box strong{font-size:18px;}
                .monitor-status-row{display:flex;justify-content:space-between;align-items:center;gap:12px;margin-top:22px;}
                .monitor-time{font-size:13px;color:var(--text-muted);}
                .sensor-feed-list{display:flex;flex-direction:column;gap:16px;margin-top:18px;max-height:390px;overflow:auto;padding-right:8px;}
                .sensor-feed-item{display:flex;align-items:center;gap:16px;padding:18px;border-radius:18px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.08);}
                .sensor-feed-icon{width:50px;height:50px;border-radius:14px;background:linear-gradient(135deg,#10b981,#34d399);display:flex;align-items:center;justify-content:center;font-weight:800;color:#fff;flex:0 0 auto;}
                .sensor-feed-icon.temp{background:linear-gradient(135deg,#f59e0b,#d4a574);}
                .sensor-feed-icon.wet{background:linear-gradient(135deg,#38bdf8,#2563eb);}
                .sensor-feed-icon.presence{background:linear-gradient(135deg,#a78bfa,#10b981);}
                .sensor-feed-item strong{display:block;margin-bottom:4px;}
                .sensor-feed-item p{margin:0;color:var(--text-muted);font-size:14px;line-height:1.35;}
                @media(max-width:1100px){.vital-monitor-grid,.vital-monitor-body{grid-template-columns:1fr}.vital-big-value{font-size:48px}}
            </style>

            <script>
                let activeMetric = 'heart_rate';
                const metricMeta = {
                    heart_rate: { title: 'Heart Rate Graph', unit: 'bpm', max: 140 },
                    temperature: { title: 'Temperature Graph', unit: '°C', max: 45 },
                    wetness: { title: 'Wetness Graph', unit: '', max: 1 },
                    presence: { title: 'Presence Detection Graph', unit: '', max: 1 }
                };

                document.querySelectorAll('[data-metric]').forEach(btn => {
                    btn.addEventListener('click', function(){
                        document.querySelectorAll('[data-metric]').forEach(b => b.classList.remove('active'));
                        this.classList.add('active');
                        activeMetric = this.dataset.metric;
                        loadVitalMonitor();
                    });
                });

                function numberFromStatus(value){
                    const text = String(value || '').toLowerCase();
                    return (text.includes('wet') || text.includes('detected')) && !text.includes('not') ? 1 : 0;
                }

                function buildPolyline(values, metric){
                    const meta = metricMeta[metric];
                    const cleaned = values.length ? values : new Array(12).fill(0);
                    const max = meta.max || 100;
                    const width = 620;
                    const height = 240;
                    const count = cleaned.length - 1 || 1;
                    return cleaned.map((val, index) => {
                        let numeric = metric === 'wetness' || metric === 'presence' ? numberFromStatus(val) : Number(val || 0);
                        numeric = Math.max(0, Math.min(max, numeric));
                        const x = (width / count) * index;
                        const y = height - ((numeric / max) * 210);
                        return `${x},${y}`;
                    }).join(' ');
                }

                function readableStatus(value, type){
                    if(type === 'wetness') return value || 'Dry';
                    if(type === 'presence') return value || 'Not Detected';
                    return value || 0;
                }

                function getRealtimeStatus(latest){
                    const heart = Number(latest.heart_rate || 0);
                    const temp = Number(latest.temperature || 0);
                    const wet = String(latest.wetness || '').toLowerCase();
                    const presence = String(latest.presence_detection || '').toLowerCase();

                    if (heart > 0 && (heart < 60 || heart > 100)) return ['Critical', 'failed'];
                    if (temp > 0 && temp >= 38) return ['Critical', 'failed'];
                    if (wet === 'wet') return ['Warning', 'pending'];
                    if (presence.includes('not')) return ['Warning', 'pending'];
                    if (!heart && !temp) return ['Setup Mode', 'completed'];
                    return ['Normal', 'completed'];
                }

                function loadVitalMonitor(){
                    fetch('script/get_dashboard_vital_monitor.php?t=' + Date.now())
                        .then(res => res.json())
                        .then(data => {
                            const latest = data.latest || {};
                            const history = data.history || {};
                            const meta = metricMeta[activeMetric];
                            const values = history[activeMetric] || new Array(12).fill(0);

                            document.getElementById('activeGraphTitle').textContent = meta.title;
                            document.getElementById('realtimeLine').setAttribute('points', buildPolyline(values, activeMetric));

                            let activeValue;
                            if(activeMetric === 'wetness') activeValue = readableStatus(latest.wetness, 'wetness');
                            else if(activeMetric === 'presence') activeValue = readableStatus(latest.presence_detection, 'presence');
                            else activeValue = latest[activeMetric] || 0;

                            document.getElementById('graphCurrentValue').textContent = activeValue + (meta.unit ? ' ' + meta.unit : '');
                            document.getElementById('monitorPrimaryValue').textContent = activeValue;
                            document.getElementById('monitorPrimaryUnit').textContent = meta.unit;
                            document.getElementById('monitorPatientName').textContent = latest.patient_name || 'Waiting for patient data';

                            document.getElementById('monitorHeartRate').textContent = (latest.heart_rate || 0) + ' bpm';
                            document.getElementById('monitorTemperature').textContent = (latest.temperature || 0) + ' °C';
                            document.getElementById('monitorWetness').textContent = latest.wetness || 'Dry';
                            document.getElementById('monitorPresence').textContent = latest.presence_detection || 'Not Detected';
                            document.getElementById('monitorLastUpdated').textContent = latest.timestamp || 'No sensor data yet';

                            const [statusText, statusClass] = getRealtimeStatus(latest);
                            const statusEl = document.getElementById('monitorStatus');
                            statusEl.textContent = statusText;
                            statusEl.className = 'status-badge ' + statusClass;

                            const feed = document.getElementById('sensorFeedList');
                            feed.innerHTML = `
                                <div class="sensor-feed-item"><div class="sensor-feed-icon">HR</div><div><strong>Heart Rate</strong><p>${latest.patient_name || 'Unknown Patient'} updated to ${latest.heart_rate || 0} bpm.</p></div></div>
                                <div class="sensor-feed-item"><div class="sensor-feed-icon temp">TP</div><div><strong>Temperature</strong><p>Current temperature is ${latest.temperature || 0} °C.</p></div></div>
                                <div class="sensor-feed-item"><div class="sensor-feed-icon wet">WT</div><div><strong>Wetness</strong><p>Wetness status: ${latest.wetness || 'Dry'}.</p></div></div>
                                <div class="sensor-feed-item"><div class="sensor-feed-icon presence">PR</div><div><strong>Presence Detection</strong><p>Presence status: ${latest.presence_detection || 'Not Detected'}.</p></div></div>
                            `;
                        })
                        .catch(err => console.log('Vital monitor error:', err));
                }

                loadVitalMonitor();
                setInterval(loadVitalMonitor, 3000);
            </script>


                
                
                <style>
                    


                    .cair-intel-shell {
                        margin-top: 32px;
                        display: grid;
                        grid-template-columns: minmax(330px, 0.9fr) minmax(0, 1.25fr) minmax(300px, 0.8fr);
                        gap: 24px;
                    }

                    .cair-panel {
                        position: relative;
                        overflow: hidden;
                        padding: 28px;
                        border-radius: 28px;
                        border: 1px solid rgba(255,255,255,0.12);
                        background:
                            radial-gradient(circle at 20% 10%, rgba(34, 197, 94, 0.22), transparent 32%),
                            radial-gradient(circle at 85% 80%, rgba(212, 165, 116, 0.20), transparent 34%),
                            rgba(255,255,255,0.055);
                        box-shadow: 0 18px 55px rgba(0,0,0,0.18);
                    }

                    .cair-panel::before {
                        content: "";
                        position: absolute;
                        inset: 0;
                        background-image:
                            linear-gradient(rgba(255,255,255,0.04) 1px, transparent 1px),
                            linear-gradient(90deg, rgba(255,255,255,0.04) 1px, transparent 1px);
                        background-size: 42px 42px;
                        opacity: 0.35;
                        pointer-events: none;
                    }

                    .cair-panel > * {
                        position: relative;
                        z-index: 1;
                    }

                    .cair-title-row {
                        display: flex;
                        align-items: flex-start;
                        justify-content: space-between;
                        gap: 18px;
                        margin-bottom: 24px;
                    }

                    .cair-title {
                        font-size: 24px;
                        font-weight: 800;
                        margin: 0 0 8px;
                        color: var(--text-primary);
                    }

                    .cair-subtitle {
                        margin: 0;
                        color: var(--text-muted);
                        line-height: 1.5;
                    }

                    .cair-chip {
                        display: inline-flex;
                        align-items: center;
                        gap: 8px;
                        padding: 10px 16px;
                        border-radius: 999px;
                        font-weight: 800;
                        font-size: 13px;
                        color: #34d399;
                        background: rgba(16,185,129,0.16);
                        white-space: nowrap;
                    }

                    .cair-chip::before {
                        content: "";
                        width: 9px;
                        height: 9px;
                        border-radius: 50%;
                        background: #34d399;
                        box-shadow: 0 0 16px #34d399;
                        animation: cairPulse 1.2s infinite ease-in-out;
                    }

                    @keyframes cairPulse {
                        50% { opacity: 0.35; transform: scale(0.82); }
                    }

                    
                    .cair-bed-pod {
                        min-height: 520px;
                    }

                    .cair-radar-wrap {
                        height: 300px;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        margin: 10px 0 24px;
                    }

                    .cair-radar {
                        position: relative;
                        width: 260px;
                        height: 260px;
                        border-radius: 50%;
                        border: 1px solid rgba(52,211,153,0.35);
                        background:
                            radial-gradient(circle, rgba(52,211,153,0.22) 0 3px, transparent 4px),
                            radial-gradient(circle, rgba(52,211,153,0.12), rgba(255,255,255,0.03) 58%, rgba(0,0,0,0.10) 100%);
                        box-shadow:
                            inset 0 0 60px rgba(52,211,153,0.12),
                            0 0 45px rgba(52,211,153,0.08);
                    }

                    .cair-radar::before,
                    .cair-radar::after {
                        content: "";
                        position: absolute;
                        border-radius: 50%;
                        border: 1px dashed rgba(255,255,255,0.10);
                    }

                    .cair-radar::before { inset: 38px; }
                    .cair-radar::after { inset: 82px; }

                    .cair-scan {
                        position: absolute;
                        inset: 0;
                        border-radius: 50%;
                        background: conic-gradient(from 0deg, rgba(52,211,153,0.42), transparent 70deg);
                        animation: cairScan 3s linear infinite;
                        opacity: 0.65;
                    }

                    @keyframes cairScan {
                        to { transform: rotate(360deg); }
                    }

                    .cair-bed-center {
                        position: absolute;
                        inset: 72px;
                        border-radius: 35px;
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        justify-content: center;
                        text-align: center;
                        background: rgba(8, 47, 35, 0.72);
                        border: 1px solid rgba(255,255,255,0.15);
                        backdrop-filter: blur(12px);
                    }

                    .cair-bed-center strong {
                        font-size: 26px;
                        display: block;
                    }

                    .cair-bed-center span {
                        color: var(--text-muted);
                        font-size: 13px;
                        margin-top: 6px;
                    }

                    .cair-orbit {
                        position: absolute;
                        width: 78px;
                        height: 78px;
                        border-radius: 24px;
                        display: flex;
                        flex-direction: column;
                        justify-content: center;
                        align-items: center;
                        background: rgba(255,255,255,0.10);
                        border: 1px solid rgba(255,255,255,0.12);
                        backdrop-filter: blur(10px);
                    }

                    .cair-orbit small {
                        color: var(--text-muted);
                        font-size: 10px;
                        text-transform: uppercase;
                        letter-spacing: 1px;
                        font-weight: 800;
                    }

                    .cair-orbit b {
                        font-size: 18px;
                        margin-top: 4px;
                    }

                    .orbit-heart { top: -10px; left: 91px; }
                    .orbit-temp { top: 92px; right: -12px; }
                    .orbit-wet { bottom: -10px; left: 91px; }
                    .orbit-pres { top: 92px; left: -12px; }

                    .cair-mini-summary {
                        display: grid;
                        grid-template-columns: 1fr 1fr;
                        gap: 14px;
                    }

                    .cair-summary-box {
                        padding: 16px;
                        border-radius: 18px;
                        background: rgba(255,255,255,0.06);
                        border: 1px solid rgba(255,255,255,0.10);
                    }

                    .cair-summary-box small {
                        color: var(--text-muted);
                        display: block;
                        margin-bottom: 8px;
                        text-transform: uppercase;
                        font-weight: 800;
                        letter-spacing: 1px;
                    }

                    .cair-summary-box strong {
                        font-size: 22px;
                    }

                    
                    .cair-main-graph {
                        min-height: 520px;
                    }

                    .cair-graph-frame {
                        height: 310px;
                        border-radius: 26px;
                        padding: 20px;
                        background: rgba(255,255,255,0.055);
                        border: 1px solid rgba(255,255,255,0.10);
                        margin-bottom: 20px;
                    }

                    .cair-svg {
                        width: 100%;
                        height: 100%;
                        overflow: visible;
                    }

                    .cair-grid-line {
                        stroke: rgba(255,255,255,0.08);
                        stroke-width: 1;
                    }

                    .cair-line-heart {
                        fill: none;
                        stroke: #34d399;
                        stroke-width: 4;
                        stroke-linecap: round;
                        stroke-linejoin: round;
                        filter: drop-shadow(0 0 8px rgba(52,211,153,0.35));
                    }

                    .cair-line-temp {
                        fill: none;
                        stroke: #f6d18a;
                        stroke-width: 4;
                        stroke-linecap: round;
                        stroke-linejoin: round;
                        filter: drop-shadow(0 0 8px rgba(246,209,138,0.30));
                    }

                    .cair-graph-legend {
                        display: flex;
                        gap: 16px;
                        flex-wrap: wrap;
                        color: var(--text-muted);
                        font-weight: 700;
                    }

                    .cair-dot {
                        width: 12px;
                        height: 12px;
                        border-radius: 4px;
                        display: inline-block;
                        margin-right: 8px;
                    }

                    .dot-heart { background: #34d399; }
                    .dot-temp { background: #f6d18a; }

                    .cair-reading-strip {
                        display: grid;
                        grid-template-columns: repeat(4, 1fr);
                        gap: 14px;
                    }

                    .cair-reading-tile {
                        padding: 18px;
                        border-radius: 20px;
                        background: rgba(255,255,255,0.06);
                        border: 1px solid rgba(255,255,255,0.10);
                    }

                    .cair-reading-tile span {
                        display: block;
                        color: var(--text-muted);
                        font-size: 12px;
                        text-transform: uppercase;
                        letter-spacing: 1px;
                        font-weight: 800;
                        margin-bottom: 8px;
                    }

                    .cair-reading-tile strong {
                        font-size: 24px;
                    }

                    
                    .cair-care-feed {
                        min-height: 520px;
                    }

                    .cair-care-status {
                        padding: 22px;
                        border-radius: 24px;
                        background: rgba(255,255,255,0.07);
                        border: 1px solid rgba(255,255,255,0.10);
                        margin-bottom: 18px;
                    }

                    .cair-care-status small {
                        color: var(--text-muted);
                        font-weight: 800;
                        letter-spacing: 1px;
                        text-transform: uppercase;
                    }

                    .cair-care-status strong {
                        display: block;
                        font-size: 34px;
                        margin: 8px 0;
                    }

                    .cair-care-status p {
                        margin: 0;
                        color: var(--text-muted);
                        line-height: 1.5;
                    }

                    .cair-check-list {
                        display: grid;
                        gap: 12px;
                    }

                    .cair-check-item {
                        display: grid;
                        grid-template-columns: 42px 1fr;
                        gap: 14px;
                        padding: 16px;
                        border-radius: 18px;
                        background: rgba(255,255,255,0.055);
                        border: 1px solid rgba(255,255,255,0.09);
                    }

                    .cair-check-num {
                        width: 42px;
                        height: 42px;
                        border-radius: 14px;
                        display: grid;
                        place-items: center;
                        font-weight: 900;
                        color: white;
                        background: linear-gradient(135deg, #34d399, #d4a574);
                    }

                    .cair-check-item b {
                        display: block;
                        margin-bottom: 4px;
                    }

                    .cair-check-item span {
                        color: var(--text-muted);
                        line-height: 1.4;
                        font-size: 14px;
                    }

                    @media (max-width: 1250px) {
                        .cair-intel-shell {
                            grid-template-columns: 1fr;
                        }

                        .cair-bed-pod,
                        .cair-main-graph,
                        .cair-care-feed {
                            min-height: unset;
                        }
                    }

                    @media (max-width: 720px) {
                        .cair-reading-strip,
                        .cair-mini-summary {
                            grid-template-columns: 1fr;
                        }

                        .cair-title-row {
                            flex-direction: column;
                        }
                    }
                </style>

                <section class="cair-intel-shell">
                    <div class="cair-panel cair-bed-pod">
                        <div class="cair-title-row">
                            <div>
                                <h2 class="cair-title">BedSense Command Pod</h2>
                                <p class="cair-subtitle">A compact live bed view for your ESP32 health and safety sensors.</p>
                            </div>
                            <span class="cair-chip">Linked</span>
                        </div>

                        <div class="cair-radar-wrap">
                            <div class="cair-radar">
                                <div class="cair-scan"></div>

                                <div class="cair-bed-center">
                                    <strong id="cairBedStatus">Waiting</strong>
                                    <span id="cairBedNote">Sensor feed not active yet</span>
                                </div>

                                <div class="cair-orbit orbit-heart">
                                    <small>HR</small>
                                    <b><span id="cairOrbitHeart">0</span></b>
                                </div>

                                <div class="cair-orbit orbit-temp">
                                    <small>Temp</small>
                                    <b><span id="cairOrbitTemp">0</span>°</b>
                                </div>

                                <div class="cair-orbit orbit-wet">
                                    <small>Wet</small>
                                    <b id="cairOrbitWet">--</b>
                                </div>

                                <div class="cair-orbit orbit-pres">
                                    <small>Pres</small>
                                    <b id="cairOrbitPresence">--</b>
                                </div>
                            </div>
                        </div>

                        <div class="cair-mini-summary">
                            <div class="cair-summary-box">
                                <small>Care Alert</small>
                                <strong id="cairCareAlert">None</strong>
                            </div>
                            <div class="cair-summary-box">
                                <small>Last Update</small>
                                <strong id="cairLastUpdate">No data</strong>
                            </div>
                        </div>
                    </div>

                    <div class="cair-panel cair-main-graph">
                        <div class="cair-title-row">
                            <div>
                                <h2 class="cair-title">Live Wave Trend</h2>
                                <p class="cair-subtitle">Heart rate and temperature draw a moving trend as new readings arrive.</p>
                            </div>
                            <span class="cair-chip">Realtime</span>
                        </div>

                        <div class="cair-graph-frame">
                            <svg class="cair-svg" viewBox="0 0 600 260" preserveAspectRatio="none">
                                <line class="cair-grid-line" x1="0" y1="52" x2="600" y2="52"></line>
                                <line class="cair-grid-line" x1="0" y1="104" x2="600" y2="104"></line>
                                <line class="cair-grid-line" x1="0" y1="156" x2="600" y2="156"></line>
                                <line class="cair-grid-line" x1="0" y1="208" x2="600" y2="208"></line>
                                <polyline id="cairHeartLine" class="cair-line-heart" points=""></polyline>
                                <polyline id="cairTempLine" class="cair-line-temp" points=""></polyline>
                            </svg>
                        </div>

                        <div class="cair-graph-legend">
                            <span><i class="cair-dot dot-heart"></i>Heart Rate Path</span>
                            <span><i class="cair-dot dot-temp"></i>Temperature Path</span>
                        </div>

                        <div class="cair-reading-strip" style="margin-top:22px;">
                            <div class="cair-reading-tile">
                                <span>Heart Rate</span>
                                <strong><span id="cairHeartValue">0</span> BPM</strong>
                            </div>
                            <div class="cair-reading-tile">
                                <span>Temperature</span>
                                <strong><span id="cairTempValue">0</span> °C</strong>
                            </div>
                            <div class="cair-reading-tile">
                                <span>Wetness</span>
                                <strong id="cairWetValue">--</strong>
                            </div>
                            <div class="cair-reading-tile">
                                <span>Presence</span>
                                <strong id="cairPresenceValue">--</strong>
                            </div>
                        </div>
                    </div>

                    <div class="cair-panel cair-care-feed">
                        <div class="cair-title-row">
                            <div>
                                <h2 class="cair-title">Care Guidance</h2>
                                <p class="cair-subtitle">Quick interpretation of the latest bed readings.</p>
                            </div>
                        </div>

                        <div class="cair-care-status">
                            <small>Current Condition</small>
                            <strong id="cairCondition">Waiting</strong>
                            <p id="cairConditionNote">Connect ESP32 to start reading from tbl_patient_data.</p>
                        </div>

                        <div class="cair-check-list">
                            <div class="cair-check-item">
                                <div class="cair-check-num">01</div>
                                <div>
                                    <b>Heart check</b>
                                    <span id="cairHeartAdvice">Waiting for heart rate data.</span>
                                </div>
                            </div>

                            <div class="cair-check-item">
                                <div class="cair-check-num">02</div>
                                <div>
                                    <b>Bed safety</b>
                                    <span id="cairBedAdvice">Wetness and presence will update once data arrives.</span>
                                </div>
                            </div>

                            <div class="cair-check-item">
                                <div class="cair-check-num">03</div>
                                <div>
                                    <b>Temperature watch</b>
                                    <span id="cairTempAdvice">Temperature monitor is on standby.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <script>
                    const cairZeroHistory = new Array(16).fill(0);

                    function cairNum(value) {
                        const number = Number(value);
                        return Number.isFinite(number) ? number : 0;
                    }

                    function cairToPoints(values, maxValue) {
                        const list = Array.isArray(values) && values.length ? values.slice(-16) : cairZeroHistory;
                        const step = 600 / 15;

                        return list.map((value, index) => {
                            const safe = Math.max(0, Math.min(maxValue, cairNum(value)));
                            const x = index * step;
                            const y = 235 - ((safe / maxValue) * 205);
                            return `${x.toFixed(1)},${y.toFixed(1)}`;
                        }).join(' ');
                    }

                    function cairEvaluate(latest) {
                        const heart = cairNum(latest.heart_rate);
                        const temp = cairNum(latest.temperature);
                        const wet = String(latest.wetness || '').toLowerCase();
                        const presence = String(latest.presence_detection || '').toLowerCase();

                        if (heart === 0 && temp === 0) {
                            return {
                                status: 'Waiting',
                                note: 'Sensor feed not active yet',
                                alert: 'None',
                                heartAdvice: 'Waiting for heart rate data.',
                                bedAdvice: 'Wetness and presence will update once data arrives.',
                                tempAdvice: 'Temperature monitor is on standby.'
                            };
                        }

                        if (heart < 60 || heart > 100) {
                            return {
                                status: 'Critical',
                                note: 'Heart rate needs attention.',
                                alert: 'Heart Rate',
                                heartAdvice: 'Heart rate is outside the safe range of 60–100 BPM.',
                                bedAdvice: 'Check wetness and presence sensor for added context.',
                                tempAdvice: 'Continue monitoring body temperature.'
                            };
                        }

                        if (temp >= 38) {
                            return {
                                status: 'Warning',
                                note: 'High temperature detected.',
                                alert: 'High Temp',
                                heartAdvice: 'Heart rate currently appears acceptable.',
                                bedAdvice: 'Bed safety sensors remain part of the check.',
                                tempAdvice: 'Temperature is at fever-watch level.'
                            };
                        }

                        if (wet === 'wet') {
                            return {
                                status: 'Warning',
                                note: 'Wetness detected on bed sensor.',
                                alert: 'Wetness',
                                heartAdvice: 'Heart rate currently appears acceptable.',
                                bedAdvice: 'Wetness detected. Check patient bed condition.',
                                tempAdvice: 'Temperature currently appears acceptable.'
                            };
                        }

                        if (presence.includes('not')) {
                            return {
                                status: 'Warning',
                                note: 'Presence sensor does not detect patient.',
                                alert: 'Presence',
                                heartAdvice: 'Heart rate currently appears acceptable.',
                                bedAdvice: 'Presence not detected. Verify patient position.',
                                tempAdvice: 'Temperature currently appears acceptable.'
                            };
                        }

                        return {
                            status: 'Normal',
                            note: 'Live readings are inside the expected range.',
                            alert: 'None',
                            heartAdvice: 'Heart rate is within the 60–100 BPM range.',
                            bedAdvice: 'No wetness detected and patient presence is confirmed.',
                            tempAdvice: 'Temperature is below fever-watch level.'
                        };
                    }

                    function cairSet(id, value) {
                        const el = document.getElementById(id);
                        if (el) el.textContent = value;
                    }

                    function loadCreativeCarebedPanel() {
                        fetch('script/get_dashboard_vital_monitor.php')
                            .then(response => response.json())
                            .then(data => {
                                const latest = data.latest || {};
                                const history = data.history || {};
                                const heart = cairNum(latest.heart_rate);
                                const temp = cairNum(latest.temperature);
                                const wetness = latest.wetness || '--';
                                const presence = latest.presence_detection || '--';
                                const evaluation = cairEvaluate(latest);

                                cairSet('cairHeartValue', heart);
                                cairSet('cairTempValue', temp);
                                cairSet('cairWetValue', wetness);
                                cairSet('cairPresenceValue', presence);

                                cairSet('cairOrbitHeart', heart);
                                cairSet('cairOrbitTemp', temp);
                                cairSet('cairOrbitWet', wetness);
                                cairSet('cairOrbitPresence', presence);

                                cairSet('cairBedStatus', evaluation.status);
                                cairSet('cairBedNote', evaluation.note);
                                cairSet('cairCareAlert', evaluation.alert);
                                cairSet('cairLastUpdate', latest.timestamp || 'No data');

                                cairSet('cairCondition', evaluation.status);
                                cairSet('cairConditionNote', evaluation.note);
                                cairSet('cairHeartAdvice', evaluation.heartAdvice);
                                cairSet('cairBedAdvice', evaluation.bedAdvice);
                                cairSet('cairTempAdvice', evaluation.tempAdvice);

                                const heartLine = document.getElementById('cairHeartLine');
                                const tempLine = document.getElementById('cairTempLine');

                                if (heartLine) heartLine.setAttribute('points', cairToPoints(history.heart_rate, 120));
                                if (tempLine) tempLine.setAttribute('points', cairToPoints(history.temperature, 45));
                            })
                            .catch(error => {
                                console.log('Creative Carebed panel error:', error);

                                const heartLine = document.getElementById('cairHeartLine');
                                const tempLine = document.getElementById('cairTempLine');

                                if (heartLine) heartLine.setAttribute('points', cairToPoints([], 120));
                                if (tempLine) tempLine.setAttribute('points', cairToPoints([], 45));
                            });
                    }

                    loadCreativeCarebedPanel();
                    setInterval(loadCreativeCarebedPanel, 3000);
                </script>


        </main>
    </div>

    
    <button class="mobile-menu-toggle">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/>
        </svg>
    </button>

    
    <footer class="site-footer">
        <p>Copyright © 2026 CAIR-Bed Developers. Designed by BS Computer Engineering Students. </a></p>
    </footer>
<script src="script/carebed-theme.js"></script>

<script src="script/carebed-notifications.js"></script>