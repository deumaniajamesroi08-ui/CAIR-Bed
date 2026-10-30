<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Cairbed System</title>
    <meta name="description" content="3D Glassmorphism Dashboard Template by TemplateMo">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="templatemo-glass-admin-style.css">
    
</head>
<body>
    
    <div class="background"></div>
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <div class="dashboard">
        
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <img src="images/cairbed.png" alt="Cairbed Logo">
                </div>
                <span class="logo-text">Cairbed Dash</span>
            </div>

            <ul class="nav-menu">
                <li class="nav-section">
                    <span class="nav-section-title">Main Menu</span>
                    <ul>
                        <li class="nav-item">
                            <a href="dashboard.php" class="nav-link">
                                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/>
                                    <rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>
                                </svg>
                                Dashboard
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a href="users.php" class="nav-link">
                                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                </svg>
                                Patients
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="alerts.php" class="nav-link">
                                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                    <circle cx="9" cy="7" r="4"/>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                </svg>
                                Alerts
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="settings.php" class="nav-link active">
                                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="3"/>
                                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                                </svg>
                                Settings
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-section">
                    <span class="nav-section-title">Account</span>
                    <ul>
                        <li class="nav-item">
                            <a href="index.php" class="nav-link">
                                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                                    <polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
                                </svg>
                                Logout
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>

           
        </aside>

        
        <main class="main-content">
            
            <nav class="navbar">
                <div class="page-header">
                    <h1 class="page-title">Settings</h1>
                    <div class="page-breadcrumb">
                        <a href="dashboard.php">Dashboard</a>
                        <span>/</span>
                        <span>Settings</span>
                    </div>
                </div>
                <div class="navbar-right">
                    
                    <div class="carebed-notification-wrapper">
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
</div>
<button class="nav-btn" id="theme-toggle" title="Toggle Light/Dark Mode">
                        <svg class="icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/>
                            <path d="M4.93 4.93l1.41 1.41"/><path d="M17.66 17.66l1.41 1.41"/>
                            <path d="M2 12h2"/><path d="M20 12h2"/>
                            <path d="M6.34 17.66l-1.41 1.41"/><path d="M19.07 4.93l-1.41 1.41"/>
                        </svg>
                        <svg class="icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: none;">
                            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                        </svg>
                    </button>
                </div>
            </nav>

            
            <div class="settings-grid">
                
                <div class="glass-card settings-nav-card">
                    <ul class="settings-nav">
                        <li class="settings-nav-item">
                            <a href="#" class="settings-nav-link active" data-tab="profile">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                                </svg>
                                Profile                            </a>
                        </li>
                        <li class="settings-nav-item">
                            <a href="#" class="settings-nav-link" data-tab="security">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                </svg>
                                Security
                            </a>
                        </li>
                        <li class="settings-nav-item">
                            <a href="#" class="settings-nav-link" data-tab="notifications">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                                </svg>
                                Notifications
                            </a>
                        </li>
                        <li class="settings-nav-item">
                            <a href="#" class="settings-nav-link" data-tab="appearance">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/>
                                    <path d="M4.93 4.93l1.41 1.41"/><path d="M17.66 17.66l1.41 1.41"/>
                                </svg>
                                Appearance
                            </a>
                        </li>
                        <li class="settings-nav-item">
                            <a href="#" class="settings-nav-link" data-tab="records">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M8 6h13"/>
                                    <path d="M8 12h13"/>
                                    <path d="M8 18h13"/>
                                    <path d="M3 6h.01"/>
                                    <path d="M3 12h.01"/>
                                    <path d="M3 18h.01"/>
                                </svg>
                                Records
                            </a>
                        </li>
                    </ul>
                </div>

                
               <div class="glass-card">
 
<div class="settings-tab-content active" id="tab-profile">
    <div class="profile-header">
        <div class="profile-avatar-large-wrapper">
            <img id="profilePreview" src="uploads/img.jpg" alt="Profile Picture" class="profile-avatar-large-img">
            <div id="profileInitials" class="profile-avatar-large-text">TM</div>

            <button type="button" id="editImageBtn" class="profile-avatar-edit" title="Change Profile Picture">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                </svg>
            </button>

            <input type="file" id="profile_image" name="profile_image" accept="image/*" hidden>
        </div>

        <div class="profile-info">
            <h2>Profile</h2>
            <p>User</p>
            <p id="uploadMessage" style="margin-top:8px;font-weight:bold;"></p>
        </div>
    </div>

    <form id="profileForm">
        <div class="settings-section">
            <h3 class="settings-section-title">Profile Information</h3>
            <div class="form-grid">
                <div class="form-group-settings">
                    <label>Full Name</label>
                    <input type="text" name="fullname" id="fullname" placeholder="Full Name" required>
                </div>
                <div class="form-group-settings">
                    <label>Age</label>
                    <input type="text" name="age" id="age" placeholder="Age" required>
                </div>
                <div class="form-group-settings">
                    <label>Address</label>
                    <input type="text" name="address" id="address" placeholder="Address" required>
                </div>
                <div class="form-group-settings">
                    <label>Email Address</label>
                    <input type="email" name="email" id="email" placeholder="Email Address" required>
                </div>
                <div class="form-group-settings">
                    <label>Phone Number</label>
                    <input type="tel" name="phone_number" id="phone_number" placeholder="Phone Number">
                </div>
                <div class="form-group-settings">
                    <label>Date of Birth</label>
                    <input type="date" name="date_of_birth" id="date_of_birth">
                </div>
                <div class="form-group-settings">
                    <label>Role</label>
                    <input type="text" name="role" id="role" placeholder="Role" required>
                </div>
            </div>
        </div>

        <div class="btn-group" style="margin-top:20px;">
            <button type="submit" class="btn btn-primary" style="width:auto;">Save Changes</button>
            <button type="reset" class="btn btn-secondary" style="width:auto;">Cancel</button>
        </div>

        <p id="profileMessage" style="margin-top:10px;font-weight:bold;"></p>
    </form>

    
    <div id="profileDisplay" style="display:none; margin-top:30px;">
        <div class="profile-row">
            <span class="label">Full Name:</span>
            <span class="value" id="disp_fullname"></span>
        </div>
        <div class="profile-row">
            <span class="label">Age:</span>
            <span class="value" id="disp_age"></span>
        </div>
        <div class="profile-row">
            <span class="label">Role:</span>
            <span class="value" id="disp_role"></span>
        </div>
        <div class="profile-row">
            <span class="label">Email:</span>
            <span class="value" id="disp_email"></span>
        </div>
        <div class="profile-row">
            <span class="label">Address:</span>
            <span class="value" id="disp_address"></span>
        </div>
        <div class="profile-row">
            <span class="label">Phone:</span>
            <span class="value" id="disp_phone"></span>
        </div>
        <div class="profile-row">
            <span class="label">Date of Birth:</span>
            <span class="value" id="disp_dob"></span>
        </div>
        <button id="editProfile" class="btn-edit" type="button">Edit Profile</button>
    </div>
</div>

<style>

.profile-header {
    display: flex;
    align-items: center;
    gap: 24px;
    margin-bottom: 30px;
}

.profile-avatar-large-wrapper {
    position: relative;
    width: 120px;
    height: 120px;
    min-width: 120px;
    border-radius: 28px;
    overflow: visible;
    flex-shrink: 0;
}

.profile-avatar-large-img {
    width: 120px;
    height: 120px;
    object-fit: cover;
    border-radius: 28px;
    display: block;
    background: linear-gradient(135deg, #27c498, #c9ba83);
}

.profile-avatar-large-text {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 46px;
    font-weight: 700;
    color: white;
    border-radius: 28px;
    pointer-events: none;
    background: linear-gradient(135deg, #27c498, #c9ba83);
}

.profile-avatar-edit {
    position: absolute;
    right: -6px;
    bottom: -6px;
    width: 42px;
    height: 42px;
    border: none;
    border-radius: 14px;
    background: rgba(212, 186, 138, 0.45);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 10;
}

.profile-avatar-edit svg {
    width: 18px;
    height: 18px;
}

.profile-avatar-edit:hover {
    transform: scale(1.05);
}


    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(12px);
    border-radius: 20px;
    border: 1px solid rgba(255,255,255,0.3);
    padding: 30px;
}

.glass-panel:hover {
    transform: translateY(-3px);
}

.profile-row {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    font-size: 16px;
    border-bottom: 1px solid rgba(255,255,255,0.2);
}

.profile-row:last-child {
    border-bottom: none;
}

.label {
    font-weight: bold;
    color: #ddd;
}

.value {
    color: #fff;
}

.btn-edit {
    margin-top: 20px;
    width: 100%;
    padding: 10px 0;
    border-radius: 12px;
    border: none;
    cursor: pointer;
    background: rgba(255, 255, 255, 0.25);
    color: #fff;
    font-weight: bold;
    font-size: 16px;
    transition: background 0.3s ease;
}

.btn-edit:hover {
    background: rgba(255, 255, 255, 0.45);
}
</style>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
    let latestUploadedImage = '';

    function updateAvatar(profileImage, fullname) {
        let imagePath = profileImage ? String(profileImage).trim() : '';
        let defaultImage = 'uploads/profile_18_1775832391.png';

        if (imagePath !== '' && imagePath.toLowerCase() !== 'null') {
            $('#profilePreview')
                .attr('src', imagePath + '?t=' + new Date().getTime())
                .show();

            $('#profileInitials').hide();
        } else {
            $('#profilePreview')
                .attr('src', defaultImage + '?t=' + new Date().getTime())
                .show();

            $('#profileInitials').hide();
        }
    }

    function loadProfile() {
        $.ajax({
            url: 'script/add_user.php',
            type: 'POST',
            dataType: 'json',
            data: { fetch: true },

            success: function (res) {
                console.log('LOAD RESPONSE:', res);

                if (res.status === 'success' && res.data) {
                    $('#disp_fullname').text(res.data.fullname || '');
                    $('#disp_age').text(res.data.age || '');
                    $('#disp_role').text(res.data.role || '');
                    $('#disp_email').text(res.data.email || '');
                    $('#disp_address').text(res.data.address || '');
                    $('#disp_phone').text(res.data.phone_number || '');
                    $('#disp_dob').text(res.data.date_of_birth || '');

                    $('#fullname').val(res.data.fullname || '');
                    $('#age').val(res.data.age || '');
                    $('#address').val(res.data.address || '');
                    $('#email').val(res.data.email || '');
                    $('#phone_number').val(res.data.phone_number || '');
                    $('#date_of_birth').val(res.data.date_of_birth || '');
                    $('#role').val(res.data.role || '');

                    let dbImage = res.data.profile_image
                        ? String(res.data.profile_image).trim()
                        : '';

                    let finalImage = dbImage !== '' && dbImage.toLowerCase() !== 'null'
                        ? dbImage
                        : latestUploadedImage;

                    updateAvatar(finalImage, res.data.fullname || '');

                    $('#profileForm').hide();
                    $('#profileDisplay').show();

                    $('#profileMessage').text('');
                }
            },

            error: function (xhr) {
                console.log('LOAD ERROR RAW:', xhr.responseText);
            }
        });
    }

    $('.settings-nav-link').on('click', function (e) {
        e.preventDefault();

        $('.settings-nav-link').removeClass('active');
        $(this).addClass('active');

        const tabName = $(this).data('tab');

        $('.settings-tab-content').removeClass('active').hide();
        $('#tab-' + tabName).addClass('active').show();
    });

    $('#editProfile').on('click', function (e) {
        e.preventDefault();

        $('#profileDisplay').hide();
        $('#profileForm').fadeIn();
        $('#profileMessage').text('');
    });

    $('#cancelEditProfile').on('click', function (e) {
        e.preventDefault();

        $('#profileForm').hide();
        $('#profileDisplay').fadeIn();
        $('#profileMessage').text('');

        loadProfile();
    });

    $('#profileForm').on('submit', function (e) {
        e.preventDefault();

        $('#profileMessage')
            .css('color', '#fff')
            .text('Saving changes...');

        $.ajax({
            url: 'script/add_user.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',

            success: function (res) {
                if (res.status === 'success') {
                    $('#profileMessage')
                        .css('color', '#22c55e')
                        .text(res.message || 'Profile saved successfully.');

                    loadProfile();

                    $('#profileForm').hide();
                    $('#profileDisplay').fadeIn();
                } else {
                    $('#profileMessage')
                        .css('color', '#ef4444')
                        .text(res.message || 'Failed to save profile.');
                }
            },

            error: function (xhr) {
                $('#profileMessage')
                    .css('color', '#ef4444')
                    .text('Error saving profile.');

                console.log(xhr.responseText);
            }
        });
    });

    $('#editImageBtn').on('click', function () {
        $('#profile_image').click();
    });

    $('#profile_image').on('change', function () {
        let fileInput = this;
        let file = fileInput.files[0];

        if (!file) return;

        let allowedTypes = [
            'image/jpeg',
            'image/png',
            'image/jpg',
            'image/webp'
        ];

        if (!allowedTypes.includes(file.type)) {
            $('#uploadMessage')
                .css('color', '#ef4444')
                .text('Only JPG, JPEG, PNG, and WEBP files are allowed.');

            fileInput.value = '';
            return;
        }

        if (file.size > 5 * 1024 * 1024) {
            $('#uploadMessage')
                .css('color', '#ef4444')
                .text('Image must be 5MB or below.');

            fileInput.value = '';
            return;
        }

        let reader = new FileReader();

        reader.onload = function (e) {
            $('#profilePreview')
                .attr('src', e.target.result)
                .show();

            $('#profileInitials').hide();
        };

        reader.readAsDataURL(file);

        let formData = new FormData();
        formData.append('profile_image', file);
        formData.append('upload_profile_image', '1');

        $('#uploadMessage')
            .css('color', '#fff')
            .text('Uploading image...');

        $.ajax({
            url: 'script/upload_profile_image.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            cache: false,

            success: function (res) {
                console.log('UPLOAD RESPONSE:', res);

                if (res.status === 'success') {
                    latestUploadedImage = res.image_path
                        ? String(res.image_path).trim()
                        : '';

                    $('#profilePreview')
                        .attr('src', latestUploadedImage + '?t=' + new Date().getTime())
                        .show();

                    $('#profileInitials').hide();

                    $('#uploadMessage')
                        .css('color', '#22c55e')
                        .text(res.message || 'Profile picture updated successfully.');

                    fileInput.value = '';

                    loadProfile();
                } else {
                    $('#uploadMessage')
                        .css('color', '#ef4444')
                        .text(res.message || 'Upload failed.');

                    fileInput.value = '';
                }
            },

            error: function (xhr) {
                console.log('UPLOAD ERROR STATUS:', xhr.status);
                console.log('UPLOAD ERROR RAW:', xhr.responseText);

                $('#uploadMessage')
                    .css('color', '#ef4444')
                    .text('Upload failed. Please try again.');

                fileInput.value = '';
            }
        });
    });

    $('#changePasswordForm').on('submit', function (e) {
        e.preventDefault();

        $('#passwordMessage')
            .css('color', '#fff')
            .text('Updating password...');

        $.ajax({
            url: 'update_password.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',

            success: function (res) {
                if (res.status === 'success') {
                    $('#passwordMessage')
                        .css('color', '#22c55e')
                        .text(res.message);

                    $('#changePasswordForm')[0].reset();
                } else {
                    $('#passwordMessage')
                        .css('color', '#ef4444')
                        .text(res.message);
                }
            },

            error: function (xhr) {
                $('#passwordMessage')
                    .css('color', '#ef4444')
                    .text('Something went wrong while updating password.');

                console.log(xhr.responseText);
            }
        });
    });

    $('#cancelPasswordBtn').on('click', function () {
        $('#changePasswordForm')[0].reset();
        $('#passwordMessage').text('');
    });

    $('.settings-tab-content').hide();
    $('#tab-profile').show();

    loadProfile();
});
</script>


<div class="settings-tab-content" id="tab-security">
    <form id="changePasswordForm" method="POST">
        <div class="settings-section">
            <h3 class="settings-section-title">Change Password</h3>
            <div class="form-grid">
                <div class="form-group-settings full-width">
                    <label>Current Password</label>
                    <input type="password" name="current_password" placeholder="Enter current password" required>
                </div>
                <div class="form-group-settings">
                    <label>New Password</label>
                    <input type="password" name="new_password" placeholder="Enter new password" required>
                </div>
                <div class="form-group-settings">
                    <label>Confirm New Password</label>
                    <input type="password" name="confirm_password" placeholder="Confirm new password" required>
                </div>
            </div>
        </div>


        <div class="btn-group">
            <button type="submit" class="btn btn-primary" style="width: auto;">Update Password</button>
            <button type="button" id="cancelPasswordBtn" class="btn btn-secondary" style="width: auto;">Cancel</button>
        </div>

        <p id="passwordMessage" style="margin-top: 10px; font-weight: bold;"></p>
    </form>
</div>

               
                    <div class="settings-tab-content" id="tab-notifications">
                        <div class="settings-section">
                            <h3 class="settings-section-title">Realtime Notification Center</h3>
                            <p class="settings-label-desc" style="margin-bottom:18px;">Live CAIR-Bed alerts update automatically from your patient monitoring records.</p>
                            <div class="cairbed-settings-notification-board">
                                <div>
                                    <h4 class="settings-label-title" style="margin-bottom:12px;">Latest Notifications</h4>
                                    <div class="cairbed-settings-live-list" data-cairbed-settings-notification-list>
                                        <div class="cairbed-notification-empty"><div id="settingsNotificationList">Loading realtime notifications...</div></div>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="settings-label-title" style="margin-bottom:12px;">Notification Preferences</h4>
                                    <div class="settings-row">
                                        <div class="settings-label">
                                            <span class="settings-label-title">Critical Health Alerts</span>
                                            <span class="settings-label-desc">Show abnormal heart rate, temperature, wetness, and presence alerts.</span>
                                        </div>
                                        <label class="toggle-switch"><input type="checkbox" checked><span class="toggle-slider"></span></label>
                                    </div>
                                    <div class="settings-row">
                                        <div class="settings-label">
                                            <span class="settings-label-title">SOS Emergency Updates</span>
                                            <span class="settings-label-desc">Keep emergency SOS notifications visible in the notification panel.</span>
                                        </div>
                                        <label class="toggle-switch"><input type="checkbox" checked><span class="toggle-slider"></span></label>
                                    </div>
                                    <div class="settings-row">
                                        <div class="settings-label">
                                            <span class="settings-label-title">Realtime Refresh</span>
                                            <span class="settings-label-desc">Refresh notifications every 3 seconds without reloading the page.</span>
                                        </div>
                                        <label class="toggle-switch"><input type="checkbox" checked disabled><span class="toggle-slider"></span></label>
                                    </div>
                                    <div class="settings-row">
                                        <div class="settings-label">
                                            <span class="settings-label-title">Alert Sound</span>
                                            <span class="settings-label-desc">Optional alert sound setting for future SMS/desktop notification integration.</span>
                                        </div>
                                        <label class="toggle-switch"><input type="checkbox"><span class="toggle-slider"></span></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <div class="settings-tab-content" id="tab-appearance">
                        <div class="settings-section">
                            <h3 class="settings-section-title">Theme</h3>
                            <div class="settings-row">
                                <div class="settings-label">
                                    <span class="settings-label-title">Color Mode</span>
                                    <span class="settings-label-desc">Choose your preferred color mode</span>
                                </div>
                                <select class="settings-select" id="theme-select">
                                    <option value="dark">Dark Mode</option>
                                    <option value="light">Light Mode</option>
                                    <option value="system">System Default</option>
                                </select>
                            </div>
                            <div class="settings-row">
                                <div class="settings-label">
                                    <span class="settings-label-title">Accent Color</span>
                                    <span class="settings-label-desc">Choose your preferred accent color</span>
                                </div>
                                <select class="settings-select" id="accent-select">
                                    <option value="emerald">Emerald (Default)</option>
                                    <option value="blue">Blue</option>
                                    <option value="purple">Purple</option>
                                    <option value="orange">Orange</option>
                                    <option value="pink">Pink</option>
                                </select>
                            </div>
                        </div>

                        <div class="settings-section">
                            <h3 class="settings-section-title">Display</h3>
                            <div class="settings-row">
                                <div class="settings-label">
                                    <span class="settings-label-title">Compact Mode</span>
                                    <span class="settings-label-desc">Reduce spacing for more content</span>
                                </div>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="compact-mode-toggle">
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                            <div class="settings-row">
                                <div class="settings-label">
                                    <span class="settings-label-title">Animations</span>
                                    <span class="settings-label-desc">Enable smooth animations and transitions</span>
                                </div>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="animations-toggle" checked>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                            <div class="settings-row">
                                <div class="settings-label">
                                    <span class="settings-label-title">Blur Effects</span>
                                    <span class="settings-label-desc">Enable glassmorphism blur effects</span>
                                </div>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="blur-effects-toggle" checked>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                            <div class="settings-row">
                                <div class="settings-label">
                                    <span class="settings-label-title">Floating Orbs</span>
                                    <span class="settings-label-desc">Show animated background orbs</span>
                                </div>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="floating-orbs-toggle" checked>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                        </div>

                        <div class="btn-group">
                            <button type="button" class="btn btn-primary" id="applyAppearanceBtn" style="width: auto;">Apply Changes</button>
                            <button type="button" class="btn btn-secondary" id="resetAppearanceBtn" style="width: auto;">Reset to Default</button>
                        </div>
                    </div>

                    <div class="settings-tab-content" id="tab-records">
                        <div class="records-panel records-summary-card">
                            <div>
                                <h2 class="card-title">Records Monitoring</h2>
                                <p class="card-subtitle">Realtime records from tbl_records for the currently logged-in patient.</p>
                            </div>
                            <button class="records-refresh-btn" type="button" onclick="loadRecords()">Reload</button>
                        </div>

                        <div class="records-table-card">
                            <div class="card-header records-header">
                                <div>
                                    <h2 class="card-title">Records History</h2>
                                    <p class="card-subtitle">Search heart rate, temperature, wetness, presence, or timestamp</p>
                                </div>
                                <div class="search-box records-search-box">
                                    <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="11" cy="11" r="8"/>
                                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                                    </svg>
                                    <input type="text" class="search-input" id="recordSearch" placeholder="Search your records...">
                                </div>
                            </div>
                            <div class="table-wrapper">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>Timestamp</th>
                                            <th>Patient Name</th>
                                            <th>Heart Rate</th>
                                            <th>Temperature</th>
                                            <th>Wetness</th>
                                            <th>Presence</th>
                                        </tr>
                                    </thead>
                                    <tbody id="recordsTable">
                                        <tr><td colspan="6" class="records-empty-row">Loading records...</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

<style>
.records-panel{padding:28px;border-radius:24px;background:linear-gradient(135deg,rgba(5,150,105,.14),rgba(212,165,116,.12));border:1px solid var(--glass-border);margin-bottom:28px}
.records-summary-card{display:flex;align-items:center;justify-content:space-between;gap:20px}
.records-table-card{padding:28px;border-radius:24px;background:var(--glass-bg);border:1px solid var(--glass-border)}
.records-header{align-items:center;gap:18px}
.records-search-box{min-width:320px}
.records-refresh-btn{border:1px solid var(--glass-border);background:var(--glass-bg);color:var(--text-primary);border-radius:12px;padding:10px 16px;font-weight:700;cursor:pointer}
.records-pill{display:inline-block;padding:6px 10px;border-radius:9px;font-size:12px;font-weight:800}
.records-normal{background:rgba(16,185,129,.18);color:#10b981}
.records-warning{background:rgba(234,179,8,.18);color:#eab308}
.records-critical{background:rgba(239,68,68,.18);color:#ef4444}
.records-info{background:rgba(59,130,246,.18);color:#3b82f6}
.records-empty-row{text-align:center;color:var(--text-muted);padding:26px!important}
@media(max-width:900px){.records-summary-card,.records-header{flex-direction:column;align-items:stretch}.records-search-box{min-width:0;width:100%}}
</style>

<script>
(function () {
    const defaults = {
        theme: 'dark',
        accent: 'emerald',
        compact: false,
        animations: true,
        blur: true,
        orbs: true
    };

    function $(id) { return document.getElementById(id); }

    function getSettings() {
        try {
            return Object.assign({}, defaults, JSON.parse(localStorage.getItem('carebedAppearanceSettings') || '{}'));
        } catch (e) {
            return Object.assign({}, defaults);
        }
    }

    function setAccent(accent) {
        const colors = {
            emerald: '#10b981',
            blue: '#3b82f6',
            purple: '#8b5cf6',
            orange: '#f97316',
            pink: '#ec4899'
        };
        document.documentElement.style.setProperty('--primary', colors[accent] || colors.emerald);
        document.documentElement.style.setProperty('--accent-color', colors[accent] || colors.emerald);
    }

    function applyVisualSettings(settings) {
        document.body.classList.toggle('compact-mode', !!settings.compact);
        document.body.classList.toggle('animations-disabled', !settings.animations);
        document.body.classList.toggle('blur-disabled', !settings.blur);
        document.body.classList.toggle('orbs-disabled', !settings.orbs);
        setAccent(settings.accent);
        if (window.CarebedTheme) {
            window.CarebedTheme.apply(settings.theme);
        } else {
            localStorage.setItem('theme', settings.theme);
        }
    }

    function fillControls(settings) {
        if ($('theme-select')) $('theme-select').value = settings.theme;
        if ($('accent-select')) $('accent-select').value = settings.accent;
        if ($('compact-mode-toggle')) $('compact-mode-toggle').checked = !!settings.compact;
        if ($('animations-toggle')) $('animations-toggle').checked = !!settings.animations;
        if ($('blur-effects-toggle')) $('blur-effects-toggle').checked = !!settings.blur;
        if ($('floating-orbs-toggle')) $('floating-orbs-toggle').checked = !!settings.orbs;
    }

    function collectControls() {
        return {
            theme: $('theme-select') ? $('theme-select').value : defaults.theme,
            accent: $('accent-select') ? $('accent-select').value : defaults.accent,
            compact: $('compact-mode-toggle') ? $('compact-mode-toggle').checked : defaults.compact,
            animations: $('animations-toggle') ? $('animations-toggle').checked : defaults.animations,
            blur: $('blur-effects-toggle') ? $('blur-effects-toggle').checked : defaults.blur,
            orbs: $('floating-orbs-toggle') ? $('floating-orbs-toggle').checked : defaults.orbs
        };
    }

    function save(settings) {
        localStorage.setItem('carebedAppearanceSettings', JSON.stringify(settings));
        localStorage.setItem('theme', settings.theme);
        applyVisualSettings(settings);
        fillControls(settings);
    }

    function initAppearanceButtons() {
        const saved = getSettings();
        fillControls(saved);
        applyVisualSettings(saved);

        const applyBtn = $('applyAppearanceBtn');
        const resetBtn = $('resetAppearanceBtn');

        if (applyBtn && applyBtn.dataset.ready !== '1') {
            applyBtn.dataset.ready = '1';
            applyBtn.addEventListener('click', function () {
                save(collectControls());
                this.textContent = 'Applied!';
                setTimeout(() => { this.textContent = 'Apply Changes'; }, 1200);
            });
        }

        if (resetBtn && resetBtn.dataset.ready !== '1') {
            resetBtn.dataset.ready = '1';
            resetBtn.addEventListener('click', function () {
                save(Object.assign({}, defaults));
                this.textContent = 'Reset!';
                setTimeout(() => { this.textContent = 'Reset to Default'; }, 1200);
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAppearanceButtons);
    } else {
        initAppearanceButtons();
    }
})();
</script>
<style>
body.compact-mode .settings-row { padding-top: 14px !important; padding-bottom: 14px !important; }
body.animations-disabled *, body.animations-disabled *::before, body.animations-disabled *::after { animation: none !important; transition: none !important; }
body.blur-disabled .glass-card, body.blur-disabled .navbar, body.blur-disabled .sidebar { backdrop-filter: none !important; -webkit-backdrop-filter: none !important; }
body.orbs-disabled .orb { display: none !important; }
</style>

<script src="script/carebed-theme.js"></script>

</main>
</div>
<script>
let allRecords = [];
function cleanRecordValue(value) {
    return String(value ?? '').replace(/[&<>'"]/g, function (char) {
        return {'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#39;','"':'&quot;'}[char];
    });
}
function recordStatusClass(type, value) {
    const text = String(value || '').toLowerCase();
    const number = parseFloat(value);
    if (type === 'heart_rate' && number > 0 && (number < 50 || number > 100)) return 'records-critical';
    if (type === 'temperature' && number >= 38) return 'records-warning';
    if (type === 'wetness' && ['wet','moist','damp'].includes(text)) return 'records-critical';
    if (type === 'presence' && text.includes('detected')) return 'records-info';
    return 'records-normal';
}
function renderRecords() {
    const search = String(document.getElementById('recordSearch')?.value || '').toLowerCase();
    const table = document.getElementById('recordsTable');
    if (!table) return;
    const rows = allRecords.filter(record => !search || Object.values(record).join(' ').toLowerCase().includes(search));
    if (!rows.length) {
        table.innerHTML = '<tr><td colspan="6" class="records-empty-row">No records found.</td></tr>';
        return;
    }
    table.innerHTML = rows.map(record => `
        <tr>
            <td>${cleanRecordValue(record.timestamp)}</td>
            <td>${cleanRecordValue(record.patient_name)}</td>
            <td><span class="records-pill ${recordStatusClass('heart_rate', record.heart_rate)}">${cleanRecordValue(record.heart_rate)} BPM</span></td>
            <td><span class="records-pill ${recordStatusClass('temperature', record.temperature)}">${cleanRecordValue(record.temperature)} °C</span></td>
            <td><span class="records-pill ${recordStatusClass('wetness', record.wetness)}">${cleanRecordValue(record.wetness)}</span></td>
            <td><span class="records-pill ${recordStatusClass('presence', record.presence_detection)}">${cleanRecordValue(record.presence_detection)}</span></td>
        </tr>
    `).join('');
}
function loadRecords() {
    fetch('script/get_my_records.php?ts=' + Date.now())
        .then(response => response.json())
        .then(data => {
            allRecords = Array.isArray(data) ? data : [];
            renderRecords();
        })
        .catch(() => {
            const table = document.getElementById('recordsTable');
            if (table) table.innerHTML = '<tr><td colspan="6" class="records-empty-row">Failed to load records.</td></tr>';
        });
}
document.addEventListener('DOMContentLoaded', function () {
    const search = document.getElementById('recordSearch');
    if (search) search.addEventListener('input', renderRecords);
    loadRecords();
    setInterval(loadRecords, 3000);
});
</script>

<script src="script/carebed-notifications.js"></script>

<script>
(function(){
    function loadSettingsAiNotifications(){
        const el = document.getElementById('settingsNotificationList');
        if(!el) return;
        fetch('script/settings_notifications_list.php?ts=' + Date.now())
            .then(r => r.text())
            .then(html => { el.innerHTML = html; })
            .catch(() => { el.innerHTML = '<div class="empty-row">Failed to load notifications.</div>'; });
    }
    loadSettingsAiNotifications();
    setInterval(loadSettingsAiNotifications, 3000);
})();
</script>

</body>
</html>
