<?php
$pageTitle = 'Alerts';
$activePage = 'alerts';
$pageHeading = 'Alerts and Notifications';
$pageDescription = 'Review alert history and severity from your CAIR-Bed system.';
include __DIR__ . '/includes/layout_top.php';
?>
<section class="panel">
    <div class="toolbar">
        <button class="btn" id="reloadAlertsBtn">Reload Alerts</button>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Patient</th>
                    <th>Alert Type</th>
                    <th>Message</th>
                    <th>Sensor Value</th>
                    <th>Severity</th>
                    <th>Status</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody id="alertRows">
                <tr><td colspan="7" class="muted">Loading alerts...</td></tr>
            </tbody>
        </table>
    </div>
</section>
<script>
function loadAlerts() {
    $.getJSON('../script/admin_alerts_data.php', function(res) {
        let html = '';
        if (res.status !== 'success' || !res.rows.length) {
            html = '<tr><td colspan="7" class="muted">No alerts found.</td></tr>';
        } else {
            res.rows.forEach(function(row) {
                const sev = String(row.resolved_severity || '').toLowerCase();
                const statusClass = String(row.status || 'pending').toLowerCase();
                html += `
                    <tr>
                        <td>${row.patient_name || 'N/A'}</td>
                        <td>${row.resolved_alert_type || 'Alert'}</td>
                        <td>${row.resolved_message || '—'}</td>
                        <td>${row.resolved_sensor || '—'}</td>
                        <td><span class="badge ${sev}">${row.resolved_severity || 'Info'}</span></td>
                        <td><span class="badge ${statusClass}">${row.status || 'Pending'}</span></td>
                        <td>${row.resolved_time || 'N/A'}</td>
                    </tr>`;
            });
        }
        $('#alertRows').html(html);
    });
}
$('#reloadAlertsBtn').on('click', loadAlerts);
loadAlerts();
setInterval(loadAlerts, 7000);
</script>
<?php include __DIR__ . '/includes/layout_bottom.php'; ?>
