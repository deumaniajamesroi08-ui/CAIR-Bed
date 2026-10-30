<?php
$pageTitle = 'Patient Monitoring';
$activePage = 'patients';
$pageHeading = 'Patient Monitoring';
$pageDescription = 'View live patient health data and current condition.';
include __DIR__ . '/includes/layout_top.php';
?>
<section class="panel">
    <div class="toolbar">
        <input type="text" id="patientSearch" placeholder="Search patient name or bed ID">
        <button class="btn" id="searchPatientBtn">Search</button>
        <button class="btn secondary" id="reloadPatientsBtn">Reload</button>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Patient Name</th>
                    <th>Bed ID</th>
                    <th>Age</th>
                    <th>Heart Rate</th>
                    <th>Temperature</th>
                    <th>Wetness</th>
                    <th>Presence Detection</th>
                    <th>Status</th>
                    <th>Latest Time</th>
                </tr>
            </thead>
            <tbody id="patientMonitoringRows">
                <tr><td colspan="9" class="muted">Loading data...</td></tr>
            </tbody>
        </table>
    </div>
</section>

<script>
function loadPatients() {
    const searchValue = document.getElementById('patientSearch').value || '';
    const tableBody = document.getElementById('patientMonitoringRows');

    tableBody.innerHTML = '<tr><td colspan="9" class="muted">Loading data...</td></tr>';

    fetch('../script/admin_patients_data.php?search=' + encodeURIComponent(searchValue))
        .then(function(response) {
            return response.json();
        })
        .then(function(res) {
            console.log('PATIENT DATA:', res);

            let html = '';

            if (res.status !== 'success' || !res.rows || res.rows.length === 0) {
                html = '<tr><td colspan="9" class="muted">No patient records found.</td></tr>';
            } else {
                res.rows.forEach(function(row) {
                    html += `
                        <tr>
                            <td>${row.patient_name || 'N/A'}</td>
                            <td>${row.bed_id || 'N/A'}</td>
                            <td>${row.age || 'N/A'}</td>
                            <td>${row.heart_rate || '--'} bpm</td>
                            <td>${row.temperature || '--'} °C</td>
                            <td>${row.wetness || 'N/A'}</td>
                            <td>${row.presence_detection || 'N/A'}</td>
                            <td>
                                <span class="badge ${String(row.patient_status_class || '').toLowerCase()}">
                                    ${row.patient_status || 'NORMAL'}
                                </span>
                            </td>
                            <td>${row.latest_time || 'N/A'}</td>
                        </tr>
                    `;
                });
            }

            tableBody.innerHTML = html;
        })
        .catch(function(error) {
            console.error('PATIENT LOAD ERROR:', error);

            tableBody.innerHTML = `
                <tr>
                    <td colspan="9" class="muted">Failed to load patient data.</td>
                </tr>
            `;
        });
}

document.addEventListener('DOMContentLoaded', function () {
    loadPatients();

    document.getElementById('searchPatientBtn').addEventListener('click', loadPatients);
    document.getElementById('reloadPatientsBtn').addEventListener('click', loadPatients);

    document.getElementById('patientSearch').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            loadPatients();
        }
    });

    setInterval(loadPatients, 6000);
});
</script>