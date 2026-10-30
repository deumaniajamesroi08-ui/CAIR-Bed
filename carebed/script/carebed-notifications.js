document.addEventListener('DOMContentLoaded', function () {
    const bells = document.querySelectorAll('.carebed-notification-bell');

    function esc(value) {
        return String(value ?? '').replace(/[&<>"']/g, function (c) {
            return {'&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#39;'}[c];
        });
    }

    function iconFor(type) {
        const t = String(type || '').toLowerCase();
        if (t.includes('heart')) return 'HR';
        if (t.includes('temp')) return 'TP';
        if (t.includes('wet')) return 'WT';
        if (t.includes('presence') || t.includes('detected')) return 'PR';
        if (t.includes('sos')) return 'SOS';
        return 'AL';
    }

    function render(panel, badge, data) {
        const list = panel.querySelector('.carebed-notification-list');
        const count = panel.querySelector('.carebed-notification-count-text');
        const notifications = Array.isArray(data.notifications) ? data.notifications : [];
        const unread = notifications.filter(n => String(n.status || '').toLowerCase() !== 'read').length;

        if (badge) {
            badge.textContent = unread;
            badge.style.display = unread > 0 ? 'flex' : 'none';
        }
        if (count) count.textContent = unread + ' unread';

        if (!notifications.length) {
            list.innerHTML = '<div class="carebed-notification-empty">No notifications yet.</div>';
            return;
        }

        list.innerHTML = notifications.map(n => {
            const severity = String(n.severity || 'Info').toLowerCase();
            return `
                <div class="carebed-notification-item ${severity}">
                    <div class="carebed-notification-icon">${iconFor(n.alert_type)}</div>
                    <div class="carebed-notification-body">
                        <div class="carebed-notification-title">${esc(n.alert_type || 'System Alert')}</div>
                        <div class="carebed-notification-message">${esc((n.patient_name ? n.patient_name + ': ' : '') + (n.message || 'Cairbed monitoring generated an alert.'))}</div>
                        <div class="carebed-notification-time">${esc(n.created_at || '')}</div>
                    </div>
                    <span class="carebed-notification-dot"></span>
                </div>
            `;
        }).join('');
    }

    function loadNotifications() {
        document.querySelectorAll('.carebed-notification-panel').forEach(panel => {
            const badge = panel.parentElement.querySelector('.carebed-notification-badge');
            const notifUrl = window.location.pathname.includes('/admin/')
                    ? '../script/get_my_notifications.php?ts=' + Date.now()
                    : 'script/get_my_notifications.php?ts=' + Date.now();
            fetch(notifUrl)
                .then(r => r.json())
                .then(data => render(panel, badge, data))
                .catch(() => {
                    const list = panel.querySelector('.carebed-notification-list');
                    if (list) list.innerHTML = '<div class="carebed-notification-empty">Failed to load notifications.</div>';
                });
        });
    }

    bells.forEach(bell => {
        bell.addEventListener('click', function (e) {
            e.stopPropagation();
            const wrap = bell.closest('.carebed-notification-wrapper');
            const panel = wrap.querySelector('.carebed-notification-panel');
            document.querySelectorAll('.carebed-notification-panel.active').forEach(p => {
                if (p !== panel) p.classList.remove('active');
            });
            panel.classList.toggle('active');
            loadNotifications();
        });
    });

    document.addEventListener('click', function (e) {
        if (!e.target.closest('.carebed-notification-wrapper')) {
            document.querySelectorAll('.carebed-notification-panel.active').forEach(panel => {
                panel.classList.remove('active');
            });
        }
    });

    loadNotifications();
    setInterval(loadNotifications, 3000);
});