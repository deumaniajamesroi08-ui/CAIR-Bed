<?php
$pageTitle = 'Settings';
$activePage = 'settings';
$pageHeading = 'Settings';
$pageDescription = 'Update admin profile information for the monitoring panel.';
include __DIR__ . '/includes/layout_top.php';
$profile = [
    'fullname' => '',
    'email' => '',
    'phone_number' => '',
    'address' => ''
];
if (isset($_SESSION['user_id'])) {
    $uid = (int)$_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT fullname, email, phone_number, address FROM tbl_profile WHERE profile_id = ? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param('i', $uid);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows) {
            $profile = $result->fetch_assoc();
        }
    }
}
?>
<div class="message-box" id="settingsMessage"></div>
<section class="panel">
    <h3>Admin Profile</h3>
    <form id="settingsForm">
        <div class="form-grid">
            <div class="form-group"><label>Full Name</label><input type="text" name="fullname" value="<?php echo h($profile['fullname'] ?? ''); ?>"></div>
            <div class="form-group"><label>Email</label><input type="email" name="email" value="<?php echo h($profile['email'] ?? ''); ?>"></div>
            <div class="form-group"><label>Phone Number</label><input type="text" name="phone_number" value="<?php echo h($profile['phone_number'] ?? ''); ?>"></div>
            <div class="form-group span-2"><label>Address</label><textarea name="address"><?php echo h($profile['address'] ?? ''); ?></textarea></div>
        </div>
        <div style="margin-top:14px;"><button type="submit" class="btn">Save Settings</button></div>
    </form>
</section>
<script>
$('#settingsForm').on('submit', function(e) {
    e.preventDefault();
    $.ajax({
        url: '../script/admin_settings_save.php',
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json',
        success: function(res) {
            $('#settingsMessage').removeClass('message-success message-error').addClass(res.status === 'success' ? 'message-success' : 'message-error').text(res.message).show();
        },
        error: function(xhr) {
            $('#settingsMessage').removeClass('message-success').addClass('message-error').text(xhr.responseJSON?.message || 'Failed to save settings.').show();
        }
    });
});
</script>
<?php include __DIR__ . '/includes/layout_bottom.php'; ?>
