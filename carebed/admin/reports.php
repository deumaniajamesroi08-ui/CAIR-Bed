<?php
$pageTitle = 'Reports';
$activePage = 'reports';
$pageHeading = 'Reports';
$pageDescription = 'Generate health monitoring summaries from patient data.';
include __DIR__ . '/includes/layout_top.php';
?>
<div class="grid-cards">
    <div class="card"><div class="card-label">Fetched Records</div><div class="card-value" id="reportRecords">0</div><div class="card-sub">Rows returned from report query</div></div>
    <div class="card"><div class="card-label">Total Alerts</div><div class="card-value" id="reportAlerts">0</div><div class="card-sub">Alert count in the system</div></div>
</div>
<section class="panel">
    <div class="toolbar">
        <input type="date" id="reportFrom">
        <input type="date" id="reportTo">
        <button class="btn" id="loadReportsBtn">Generate Report</button>
        <button class="btn secondary" onclick="window.print()">Print</button>
    </div>
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
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody id="reportRows"><tr><td colspan="8" class="muted">Generate a report to view data.</td></tr></tbody>
        </table>
    </div>
</section>
<script>
$(document).ready(function () {

    function escapeHtml(text) {
        return $('<div>').text(text ?? '').html();
    }

    function getStatusBadge(status) {
        let cls = 'status-normal';

        if ((status || '').toUpperCase() === 'WARNING') cls = 'status-warning';
        if ((status || '').toUpperCase() === 'CRITICAL') cls = 'status-critical';

        return `<span class="${cls}">${escapeHtml(status)}</span>`;
    }

    $('#loadReportsBtn').on('click', function () {
        const fromDate = $('#reportFrom').val();
        const toDate = $('#reportTo').val();

        $('#reportRows').html(`
            <tr>
                <td colspan="8" class="muted" style="text-align:center;">Loading report...</td>
            </tr>
        `);

        $.ajax({
            url: '../script/admin_reports_data.php',
            type: 'GET',
            dataType: 'json',
            data: {
                from_date: fromDate,
                to_date: toDate
            },
            success: function (response) {
                console.log(response);

                if (!response || response.status !== 'success') {
                    $('#reportRows').html(`
                        <tr>
                            <td colspan="8" class="muted" style="text-align:center;">Failed to load report.</td>
                        </tr>
                    `);
                    return;
                }

                $('#reportRecords').text(response.total_records ?? 0);
                $('#reportAlerts').text(response.total_alerts ?? 0);

                if (!response.data || response.data.length === 0) {
                    $('#reportRows').html(`
                        <tr>
                            <td colspan="8" class="muted" style="text-align:center;">No records found.</td>
                        </tr>
                    `);
                    return;
                }

                let rows = '';

                $.each(response.data, function (index, row) {
                    rows += `
                        <tr>
                            <td>${escapeHtml(row.patient_name)}</td>
                            <td>${escapeHtml(row.bed_id)}</td>
                            <td>${escapeHtml(row.heart_rate)}</td>
                            <td>${escapeHtml(row.temperature) + " °C"}</td>
                            <td>${escapeHtml(row.wetness)}</td>
                            <td>${escapeHtml(row.presence_detection)}</td>
                            <td>${getStatusBadge(row.status)}</td>
                            <td>${escapeHtml(row.timestamp)}</td>
                        </tr>
                    `;
                });

                $('#reportRows').html(rows);
            },
            error: function (xhr) {
                console.log(xhr.responseText);
                $('#reportRows').html(`
                    <tr>
                        <td colspan="8" class="muted" style="text-align:center;">Server error while generating report.</td>
                    </tr>
                `);
            }
        });
    });

});
</script>
<?php include __DIR__ . '/includes/layout_bottom.php'; ?>
