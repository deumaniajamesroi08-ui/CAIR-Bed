<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Patient Monitor - Cairbed System</title>
<meta name="description" content="CairBed user patient realtime monitoring page">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="templatemo-glass-admin-style.css">
<style>
.my-monitor-wrap{display:grid;grid-template-columns:1fr 360px;gap:28px;margin-top:30px}.patient-main-card{position:relative;overflow:hidden;padding:34px;border-radius:30px;background:linear-gradient(135deg,rgba(5,150,105,.22),rgba(14,165,233,.10),rgba(212,165,116,.14));border:1px solid var(--glass-border);box-shadow:0 24px 60px rgba(0,0,0,.18)}.patient-main-card:before{content:'';position:absolute;right:-90px;top:-90px;width:220px;height:220px;border-radius:50%;background:radial-gradient(circle,rgba(52,211,153,.24),transparent 68%)}.patient-header-row{position:relative;display:flex;justify-content:space-between;align-items:flex-start;gap:20px;margin-bottom:30px}.patient-id-block{display:flex;align-items:center;gap:18px}.patient-avatar-large{width:76px;height:76px;border-radius:24px;background:linear-gradient(135deg,var(--emerald-light),var(--gold));display:flex;align-items:center;justify-content:center;color:#fff;font-size:28px;font-weight:900;box-shadow:0 18px 40px rgba(16,185,129,.25)}.patient-title{font-size:34px;font-weight:900;line-height:1.1;margin-bottom:8px}.patient-meta-line{color:var(--text-muted);font-weight:700}.condition-pill{padding:11px 18px;border-radius:999px;font-weight:900;font-size:14px}.condition-pill.normal{background:rgba(34,197,94,.18);color:#22c55e}.condition-pill.warning{background:rgba(234,179,8,.18);color:#eab308}.condition-pill.critical{background:rgba(239,68,68,.18);color:#ef4444}.sensor-board{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px}.sensor-tile{padding:24px;border-radius:24px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.10)}.sensor-label{font-size:13px;text-transform:uppercase;letter-spacing:1px;color:var(--text-muted);font-weight:900;margin-bottom:12px}.sensor-value{font-family:'Space Mono',monospace;font-size:38px;font-weight:900;letter-spacing:-1px}.sensor-unit{font-family:'Outfit',sans-serif;font-size:16px;color:var(--text-muted);margin-left:6px}.side-panel{display:grid;gap:18px}.side-card{padding:24px;border-radius:24px;background:rgba(255,255,255,.055);border:1px solid var(--glass-border)}.side-label{font-size:13px;text-transform:uppercase;letter-spacing:1px;color:var(--text-muted);font-weight:900}.side-value{font-family:'Space Mono',monospace;font-size:30px;font-weight:900;margin-top:10px}.live-row{display:flex;align-items:center;gap:12px;margin-top:24px;color:var(--emerald-light);font-weight:800}.live-dot{width:12px;height:12px;border-radius:50%;background:#22c55e;box-shadow:0 0 0 8px rgba(34,197,94,.12);animation:pulse 1.15s infinite}.info-section{margin-top:28px;padding:28px;border-radius:26px;background:rgba(255,255,255,.045);border:1px solid var(--glass-border)}.info-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-top:18px}.info-box{padding:18px;border-radius:18px;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.08)}.info-box h4{font-size:14px;text-transform:uppercase;color:var(--text-muted);letter-spacing:.8px;margin-bottom:8px}.info-box p{font-size:16px;font-weight:700}.refresh-user-btn{border:1px solid var(--glass-border);background:var(--glass-bg);color:var(--text-primary);border-radius:16px;padding:14px 18px;font-weight:900;cursor:pointer}.empty-note{padding:26px;border-radius:22px;background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.22);color:#fca5a5;font-weight:700;margin-top:20px}@keyframes pulse{50%{opacity:.35;transform:scale(.85)}}@media(max-width:980px){.my-monitor-wrap{grid-template-columns:1fr}.sensor-board,.info-grid{grid-template-columns:1fr}.patient-header-row{flex-direction:column}.navbar{gap:16px}.navbar-right{width:100%}.search-box{width:100%}}
</style>
</head>
<body>
<div class="background"></div><div class="orb orb-1"></div><div class="orb orb-2"></div><div class="orb orb-3"></div>
<div class="dashboard">
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
                <div class="logo">
                    <img src="images/cairbed.png" alt="Cairbed Logo">
                </div>
                <span class="logo-text">Cairbed Dash</span>
            </div>
    <ul class="nav-menu">
        <li class="nav-section"><span class="nav-section-title">Main Menu</span><ul>
            <li class="nav-item"><a href="dashboard.php" class="nav-link"><svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>Dashboard</a></li>
            <li class="nav-item"><a href="users.php" class="nav-link active"><svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>Patients</a></li>
            <li class="nav-item"><a href="alerts.php" class="nav-link"><svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>Alerts</a></li>
            <li class="nav-item"><a href="settings.php" class="nav-link"><svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06A1.65 1.65 0 0 0 15 19.4"/><path d="M4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.6"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="M2 12h2"/><path d="M20 12h2"/></svg>Settings</a></li>
        </ul></li>
        <li class="nav-section"><span class="nav-section-title">Account</span><ul><li class="nav-item"><a href="index.php" class="nav-link"><svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>Logout</a></li></ul></li>
    </ul>
</aside>
<main class="main-content">
<nav class="navbar">
    <div class="page-header"><h1 class="page-title">My Patient Monitor</h1><div class="page-breadcrumb"><a href="dashboard.php">Dashboard</a><span>/</span><span>Patients</span></div></div>
    <div class="navbar-right"><div class="search-box"><svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg><input type="text" class="search-input" placeholder="My realtime bed monitor" disabled></div><div class="carebed-notification-wrapper">
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
</div><button class="nav-btn" id="theme-toggle" title="Toggle Light/Dark Mode"><svg class="icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="M4.93 4.93l1.41 1.41"/><path d="M17.66 17.66l1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="M6.34 17.66l-1.41 1.41"/><path d="M19.07 4.93l-1.41 1.41"/></svg><svg class="icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg></button></div>
</nav>
<section class="my-monitor-wrap">
    <div class="patient-main-card">
        <div class="patient-header-row">
            <div class="patient-id-block"><div class="patient-avatar-large" id="myAvatar">PT</div><div><div class="patient-title" id="myName">Loading...</div><div class="patient-meta-line"><span id="myBedId">BED---</span> • Patient ID: <span id="myPatientId">PT---</span> • Age: <span id="myAge">--</span></div></div></div>
            <span class="condition-pill normal" id="myStatus">Normal</span>
        </div>
        <div class="sensor-board">
            <div class="sensor-tile"><div class="sensor-label">Heart Rate</div><div class="sensor-value"><span id="heartRateValue">0</span><span class="sensor-unit">BPM</span></div></div>
            <div class="sensor-tile"><div class="sensor-label">Temperature</div><div class="sensor-value"><span id="temperatureValue">0</span><span class="sensor-unit">°C</span></div></div>
            <div class="sensor-tile"><div class="sensor-label">Wetness</div><div class="sensor-value"><span id="wetnessValue">--</span></div></div>
            <div class="sensor-tile"><div class="sensor-label">Presence Detection</div><div class="sensor-value"><span id="presenceValue">--</span></div></div>
        </div>
        <div class="live-row"><span class="live-dot"></span><span>Realtime ESP32 monitoring active</span></div>
        <div id="noDataNote" class="empty-note" style="display:none;">No ESP32 readings yet for this user. Once the device sends data using this user's patient ID, the values will update here automatically.</div>
    </div>
    <div class="side-panel">
        <div class="side-card"><div class="side-label">Last Updated</div><div class="side-value" id="lastUpdatedValue">--</div></div>
        <div class="side-card"><div class="side-label">Bed Assignment</div><div class="side-value" id="bedAssignmentValue">--</div></div>
        <div class="side-card"><div class="side-label">Current Condition</div><div class="side-value" id="conditionValue">--</div></div>
    </div>
</section>
<section class="info-section"><h2 class="card-title">Patient Sensor Summary</h2><p class="card-subtitle">This page is only for the currently logged-in patient. It no longer shows other users.</p><div class="info-grid"><div class="info-box"><h4>Normal Heart Rate</h4><p>60–100 BPM</p></div><div class="info-box"><h4>Temperature Watch</h4><p>38°C and above</p></div><div class="info-box"><h4>Care Alerts</h4><p>Wetness or no presence detected</p></div></div></section>
</main></div>
<script src="script/carebed-theme.js"></script>
<script>
function clean(v, fallback='--'){ if(v===null||v===undefined||String(v).trim()===''||String(v).toLowerCase()==='null') return fallback; return v; }
function zeroIfEmpty(v){ const c=clean(v,'0'); return c==='--' ? '0' : c; }
function initials(name){ if(!name) return 'PT'; const p=String(name).trim().split(/\s+/); return p.length>1?(p[0][0]+p[1][0]).toUpperCase():p[0][0].toUpperCase(); }
function applyStatus(cls, text){ const pill=document.getElementById('myStatus'); pill.className='condition-pill '+cls; pill.textContent=text; document.getElementById('conditionValue').textContent=text; }
function loadMyPatient(){
    fetch('script/get_my_patient_data.php?ts='+Date.now()).then(r=>r.json()).then(res=>{
        if(res.status!=='success'){ document.getElementById('myName').textContent='Unable to load patient'; return; }
        const p=res.patient;
        document.getElementById('myAvatar').textContent=initials(p.patient_name);
        document.getElementById('myName').textContent=clean(p.patient_name,'Unknown Patient');
        document.getElementById('myBedId').textContent=clean(p.bed_id,'BED---');
        document.getElementById('myPatientId').textContent='PT-'+clean(p.profile_id,p.user_id);
        document.getElementById('myAge').textContent=clean(p.age,'N/A');
        document.getElementById('heartRateValue').textContent=zeroIfEmpty(p.heart_rate);
        document.getElementById('temperatureValue').textContent=zeroIfEmpty(p.temperature);
        document.getElementById('wetnessValue').textContent=clean(p.wetness,'--');
        document.getElementById('presenceValue').textContent=clean(p.presence_detection,'--');
        document.getElementById('lastUpdatedValue').textContent=clean(p.timestamp,'No data yet');
        document.getElementById('bedAssignmentValue').textContent=clean(p.bed_id,'BED---');
        applyStatus(p.condition_class || 'normal', p.condition_status || 'Normal');
        const hasNoData = clean(p.timestamp,'No data yet') === 'No data yet';
        document.getElementById('noDataNote').style.display = hasNoData ? 'block' : 'none';
    }).catch(err=>{ console.log(err); document.getElementById('myName').textContent='Realtime connection error'; });
}
loadMyPatient(); setInterval(loadMyPatient,3000);
</script>
<script src="script/carebed-notifications.js"></script>
</body>
</html>
