document.addEventListener('DOMContentLoaded', function () {
    function esc(v) {
        return String(v ?? '').replace(/[&<>"']/g, c => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
        }[c]));
    }

    function statusClass(status) {
        const s = String(status || '').toLowerCase();
        if (s === 'normal') return 'status-normal';
        if (s === 'offline') return 'status-offline';
        return 'status-critical';
    }

    function statusBadge(status) {
        return `<span class="admin-status-badge ${statusClass(status)}">${esc(status)}</span>`;
    }

    function patientRow(p, compact = false) {
        return `
            <tr>
                <td>${esc(p.patient_name)}</td>
                <td>${esc(p.bed_id)}</td>
                ${compact ? '' : `<td>${esc(p.age)}</td>`}
                <td>${p.heart_rate !== null ? esc(p.heart_rate) : '--'} bpm</td>
                <td>${p.temperature !== null ? esc(Number(p.temperature).toFixed(2)) : '--'} °C</td>
                <td>${esc(p.wetness || '--')}</td>
                <td>${esc(p.presence_detection || '--')}</td>
                <td>${statusBadge(p.status)}</td>
                <td>${esc(p.updated || 'No data')}</td>
            </tr>
        `;
    }

    function alertCard(a) {
        const sev = String(a.severity || '').toLowerCase();
        return `
            <div class="admin-alert-card ${sev}">
                <strong>${esc(a.patient_name)}</strong>
                <div>${esc(a.alert_type)} • ${esc(a.created_at)}</div>
                <small>${esc(a.message || '')}</small>
                <span>${esc(a.status || 'Unread')}</span>
            </div>
        `;
    }

    function updateByText(label, value) {
        document.querySelectorAll('.summary-card, .stat-card, .dashboard-card').forEach(card => {
            if ((card.innerText || '').toLowerCase().includes(label.toLowerCase())) {
                const big = card.querySelector('h2, h3, .number, .stat-number, .summary-number');
                if (big) big.textContent = value;
            }
        });
    }

    function render(data) {
        if (!data || data.status !== 'success') return;

        updateByText('total patients', data.summary.total_patients);
        updateByText('system users', data.summary.system_users);
        updateByText('active alerts', data.summary.active_alerts);
        updateByText('critical patients', data.summary.critical_patients);

        const latestTables = document.querySelectorAll('table');
        latestTables.forEach(table => {
            const txt = table.innerText.toLowerCase();
            const tbody = table.querySelector('tbody');
            if (!tbody) return;

            if (txt.includes('heart') && txt.includes('temperature') && txt.includes('wetness')) {
                const compact = !txt.includes('age');
                tbody.innerHTML = data.patients.map(p => patientRow(p, compact)).join('');
            }
        });

        const alertContainers = document.querySelectorAll('.recent-alerts, .alerts-list, .alert-list, [data-admin-alerts]');
        alertContainers.forEach(c => {
            c.innerHTML = data.alerts.length
                ? data.alerts.map(alertCard).join('')
                : '<div class="admin-empty">No recent alerts.</div>';
        });

        document.querySelectorAll('section, .card, .panel').forEach(panel => {
            if ((panel.innerText || '').toLowerCase().includes('recent alerts')) {
                const list = panel.querySelector('.recent-alerts, .alerts-list, .alert-list');
                if (list) {
                    list.innerHTML = data.alerts.length
                        ? data.alerts.map(alertCard).join('')
                        : '<div class="admin-empty">No recent alerts.</div>';
                }
            }
        });
    }

    function loadAdminRealtime() {
        fetch('admin_realtime_data.php?ts=' + Date.now())
            .then(r => r.json())
            .then(render)
            .catch(err => console.log('Admin realtime failed', err));
    }

    loadAdminRealtime();
    setInterval(loadAdminRealtime, 3000);
});