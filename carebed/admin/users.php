<?php
$pageTitle = 'Users';
$activePage = 'users';
$pageHeading = 'User Management';
$pageDescription = 'Create and monitor administrator and caregiver accounts.';
include __DIR__ . '/includes/layout_top.php';
?>
<div class="message-box" id="userMessage"></div>
<div class="two-col">
    <section class="panel">
        <h3>Create New User</h3>
        <form id="createUserForm">
            <div class="form-grid">
                <div class="form-group"><label>Username</label><input type="text" name="username" required></div>
                <div class="form-group"><label>Password</label><input type="text" name="password" required></div>
                <div class="form-group"><label>Role</label>
                    <select name="role">
                        <option value="admin">Admin</option>
                        <option value="caregiver" selected>Caregiver</option>
                        <option value="staff">Staff</option>
                    </select>
                </div>
                <div class="form-group"><label>Full Name</label><input type="text" name="fullname"></div>
                <div class="form-group"><label>Email</label><input type="email" name="email"></div>
                <div class="form-group"><label>Phone Number</label><input type="text" name="phone_number"></div>
                <div class="form-group"><label>Age</label><input type="number" name="age"></div>
                <div class="form-group"><label>Date of Birth</label><input type="date" name="date_of_birth"></div>
                <div class="form-group span-2"><label>Address</label><textarea name="address"></textarea></div>
            </div>
            <div style="margin-top:14px;"><button class="btn" type="submit">Save User</button></div>
        </form>
    </section>
    <section class="panel">
        <h3>Registered Users</h3>
        <div class="toolbar">
            <input type="text" id="userSearch" placeholder="Search username, role, or email">
            <button class="btn" id="searchUsersBtn">Search</button>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="userRows"><tr><td colspan="5" class="muted">Loading users...</td></tr></tbody>
            </table>
        </div>
    </section>
</div>
<script>
function showUserMessage(text, ok) {
    $('#userMessage').removeClass('message-success message-error').addClass(ok ? 'message-success' : 'message-error').text(text).show();
}
function loadUsers() {
    $.getJSON('../script/admin_users_data.php', { search: $('#userSearch').val() }, function(res) {
        let html = '';
        if (res.status !== 'success' || !res.rows.length) {
            html = '<tr><td colspan="5" class="muted">No users found.</td></tr>';
        } else {
            res.rows.forEach(function(row) {
                html += `
                    <tr>
                        <td>${row.username || ''}</td>
                        <td>${row.fullname || '—'}</td>
                        <td>${row.email || '—'}</td>
                        <td>${row.role || '—'}</td>
                        <td><span class="badge active">${row.status_label || 'Active'}</span></td>
                    </tr>`;
            });
        }
        $('#userRows').html(html);
    });
}
$('#createUserForm').on('submit', function(e) {
    e.preventDefault();
    $.ajax({
        url: '../script/admin_user_save.php',
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json',
        success: function(res) {
            showUserMessage(res.message, res.status === 'success');
            if (res.status === 'success') {
                $('#createUserForm')[0].reset();
                loadUsers();
            }
        },
        error: function(xhr) {
            showUserMessage(xhr.responseJSON?.message || 'Failed to save user.', false);
        }
    });
});
$('#searchUsersBtn').on('click', loadUsers);
$('#userSearch').on('keypress', function(e) { if (e.which === 13) loadUsers(); });
loadUsers();
</script>
<?php include __DIR__ . '/includes/layout_bottom.php'; ?>
