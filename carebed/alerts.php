<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Alerts - Cairbed System</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet"><link rel="stylesheet" href="templatemo-glass-admin-style.css">
<style>.alerts-toolbar{display:flex;gap:14px;align-items:center;margin:24px 0}.carebed-panel{padding:28px;border-radius:24px;background:linear-gradient(135deg,rgba(5,150,105,.14),rgba(212,165,116,.12));border:1px solid var(--glass-border)}.sos-card{display:flex;justify-content:space-between;align-items:center;gap:20px;margin-bottom:28px}.sos-btn{background:linear-gradient(135deg,#ff4d4d,#cc0000);border:none;color:white;border-radius:16px;padding:16px 28px;font-weight:900;cursor:pointer}.sos-btn span:first-child{font-family:'Space Mono',monospace;font-size:26px;margin-right:8px}.alert-search{min-width:320px}.alert-pill{display:inline-block;padding:6px 10px;border-radius:9px;font-size:12px;font-weight:800}.alert-critical{background:rgba(239,68,68,.18);color:#ef4444}.alert-warning{background:rgba(234,179,8,.18);color:#eab308}.alert-info{background:rgba(59,130,246,.18);color:#3b82f6}.status-unread{color:var(--text-primary);font-weight:800}.empty-row{text-align:center;color:var(--text-muted);padding:26px!important}@media(max-width:800px){.sos-card,.alerts-toolbar{flex-direction:column;align-items:stretch}.alert-search{min-width:0;width:100%}}</style>
</head>
<body>
<div class="background"></div><div class="orb orb-1"></div><div class="orb orb-2"></div><div class="orb orb-3"></div>
<div class="dashboard">
<aside class="sidebar" id="sidebar"><div class="sidebar-header">
                <div class="logo">
                    <img src="images/cairbed.png" alt="Cairbed Logo">
                </div>
                <span class="logo-text">Cairbed Dash</span>
            </div><ul class="nav-menu"><li class="nav-section"><span class="nav-section-title">Main Menu</span><ul><li class="nav-item"><a href="dashboard.php" class="nav-link"><svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>Dashboard</a></li><li class="nav-item"><a href="users.php" class="nav-link"><svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>Patients</a></li><li class="nav-item"><a href="alerts.php" class="nav-link active"><svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>Alerts</a></li><li class="nav-item"><a href="settings.php" class="nav-link"><svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06A1.65 1.65 0 0 0 15 19.4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="M2 12h2"/><path d="M20 12h2"/></svg>Settings</a></li></ul></li><li class="nav-section"><span class="nav-section-title">Account</span><ul><li class="nav-item"><a href="index.php" class="nav-link"><svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>Logout</a></li></ul></li></ul></aside>
<main class="main-content"><nav class="navbar"><div class="page-header"><h1 class="page-title">Alerts</h1><div class="page-breadcrumb"><a href="dashboard.php">Dashboard</a><span>/</span><span>Alerts</span></div></div><div class="navbar-right"><div class="search-box alert-search"><svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg><input type="text" class="search-input" id="alertSearch" placeholder="Search your alerts..."></div><div class="carebed-notification-wrapper">
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
</div><button class="nav-btn" id="theme-toggle" title="Toggle Light/Dark Mode"><svg class="icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="M4.93 4.93l1.41 1.41"/><path d="M17.66 17.66l1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="M6.34 17.66l-1.41 1.41"/><path d="M19.07 4.93l-1.41 1.41"/></svg><svg class="icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg></button></div></nav>
<section class="carebed-panel sos-card"><div><h2 class="card-title">Alerts Monitoring</h2><p class="card-subtitle">Only alerts for the currently logged-in patient are shown here.</p></div><button class="sos-btn" onclick="alert('SOS Triggered!')"><span>SOS</span> Emergency</button></section>
<section class="glass-card table-card" style="padding:28px"><div class="card-header"><div><h2 class="card-title">Alerts History</h2><p class="card-subtitle">Search your alert type, severity, or status</p></div><div class="card-actions"><button class="card-btn" onclick="loadAlerts()">Reload</button></div></div><div class="table-wrapper"><table class="data-table"><thead><tr><th>Timestamp</th><th>Patient Name</th><th>Alert</th><th>Severity</th><th>Status</th></tr></thead><tbody id="alertTable"><tr><td colspan="5" class="empty-row">Loading alerts...</td></tr></tbody></table></div></section>
</main></div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script><script src="script/carebed-theme.js"></script>
<script>
let allAlerts=[];function esc(v){return String(v??'').replace(/[&<>'"]/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#39;','"':'&quot;'}[c]));}function severityClass(s){s=String(s||'').toLowerCase();if(s.includes('critical'))return'alert-critical';if(s.includes('warning'))return'alert-warning';return'alert-info';}
function renderAlerts(){const q=String(document.getElementById('alertSearch').value||'').toLowerCase();const rows=allAlerts.filter(a=>!q||Object.values(a).join(' ').toLowerCase().includes(q));const table=document.getElementById('alertTable');if(!rows.length){table.innerHTML='<tr><td colspan="5" class="empty-row">No alerts found.</td></tr>';return;}table.innerHTML=rows.map(a=>`<tr><td>${esc(a.timestamp)}</td><td>${esc(a.patient_name)}</td><td><span class="alert-pill alert-critical">${esc(a.alert_type)}</span></td><td><span class="alert-pill ${severityClass(a.severity)}">${esc(a.severity)}</span></td><td><span class="status-unread">${esc(a.status)}</span></td></tr>`).join('');}
function loadAlerts(){fetch('script/get_my_alerts.php?ts='+Date.now()).then(r=>r.json()).then(data=>{allAlerts=Array.isArray(data)?data:[];renderAlerts();}).catch(e=>{console.log(e);document.getElementById('alertTable').innerHTML='<tr><td colspan="5" class="empty-row">Failed to load alerts.</td></tr>';});}
document.getElementById('alertSearch').addEventListener('input',renderAlerts);loadAlerts();setInterval(loadAlerts,10000);
</script>
<script src="script/carebed-notifications.js"></script>
</body></html>
