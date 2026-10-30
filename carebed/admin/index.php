<?php
$pageTitle = 'CAIR-Bed Admin Dashboard';
$activePage = 'dashboard';
$pageHeading = 'Dashboard Overview';
$pageDescription = 'Live summary of patients, alerts, and system users.';
include __DIR__ . '/includes/layout_top.php';
?>
<div class="grid-cards">
    <div class="card"><div class="card-label">Total Patients</div><div class="card-value" id="totalPatients">0</div><div class="card-sub">Registered patients in monitoring</div></div>
    <div class="card"><div class="card-label">System Users</div><div class="card-value" id="totalUsers">0</div><div class="card-sub">Admin and caregiver accounts</div></div>
    <div class="card"><div class="card-label">Active Alerts</div><div class="card-value" id="activeAlerts">0</div><div class="card-sub">Unread or pending conditions</div></div>
    <div class="card"><div class="card-label">Critical Patients</div><div class="card-value" id="criticalPatients">0</div><div class="card-sub">Based on latest health readings</div></div>
</div>
<div class="two-col">
    <section class="panel">
        <h3>Latest Patient Monitoring</h3>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Patient</th>
                        <th>Bed ID</th>
                        <th>Heart Rate</th>
                        <th>Temperature</th>
                        <th>Wetness</th>
                        <th>Presence Detection</th>
                        <th>Status</th>
                        <th>Updated</th>
                    </tr>
                </thead>
                <tbody id="patientRows">
                    <tr><td colspan="8" class="muted">Loading patient data...</td></tr>
                </tbody>
            </table>
        </div>
    </section>
    <section class="panel">
        <h3>Recent Alerts</h3>
        <div id="recentAlerts"><div class="alert-item muted">Loading alerts...</div></div>
    </section>
</div>
<script>
function loadDashboard() {
    $.getJSON('../script/admin_dashboard_data.php', function(res) {
        if (res.status !== 'success') return;
        $('#totalPatients').text(res.summary.total_patients || 0);
        $('#totalUsers').text(res.summary.total_users || 0);
        $('#activeAlerts').text(res.summary.active_alerts || 0);
        $('#criticalPatients').text(res.summary.critical_patients || 0);

        let patientHtml = '';
        if (!res.patients.length) {
            patientHtml = '<tr><td colspan="8" class="muted">No patient monitoring data found.</td></tr>';
        } else {
            res.patients.forEach(function(row) {
                patientHtml += `
                    <tr>
                        <td>${row.patient_name || 'N/A'}</td>
                        <td>${row.bed_id || 'N/A'}</td>
                        <td>${row.heart_rate || '0'} bpm</td>
                        <td>${row.temperature || '--'} °C</td>
                        <td>${row.wetness || 'N/A'}</td>
                        <td>${row.presence_detection || 'N/A'}</td>
                        <td><span class="badge ${String(row.patient_status_class || '').toLowerCase()}">${row.patient_status || 'NORMAL'}</span></td>
                        <td>${row.latest_time || 'N/A'}</td>
                    </tr>`;
            });
        }
        $('#patientRows').html(patientHtml);

        let alertHtml = '';
        if (!res.alerts.length) {
            alertHtml = '<div class="alert-item muted">No recent alerts found.</div>';
        } else {
            res.alerts.forEach(function(alert) {
                const severityClass = String(alert.severity || alert.status || 'pending').toLowerCase();
                alertHtml += `
                    <div class="alert-item">
                        <div><strong>${alert.patient_name || 'Unknown Patient'}</strong></div>
                        <div class="muted" style="margin:6px 0 8px;">${alert.alert_type || 'Alert'} • ${alert.created_at || ''}</div>
                        <span class="badge ${severityClass}">${alert.severity || alert.status || 'Pending'}</span>
                    </div>`;
            });
        }
        $('#recentAlerts').html(alertHtml);
    });
}
loadDashboard();
setInterval(loadDashboard, 5000);
</script>
<?php include __DIR__ . '/includes/layout_bottom.php'; ?>
