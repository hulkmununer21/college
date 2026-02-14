<div class="dashboard-container">
    <!-- Profile Header -->
    <div class="profile-header">
        <div class="profile-avatar">
            <?php if (!empty($student['profile_picture'])): ?>
                <img src="<?= BASE_URL ?>/public/uploads/<?= $student['profile_picture'] ?>" alt="Profile Picture">
            <?php else: ?>
                <div class="avatar-placeholder">
                    <?= strtoupper(substr($student['first_name'], 0, 1)) ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="profile-info">
            <h1><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></h1>
            <p class="profile-email"><?= htmlspecialchars($student['email']) ?></p>
            <span class="profile-role"><?= htmlspecialchars($student['role_name']) ?></span>
        </div>
        <div class="profile-actions">
            <button class="btn btn-primary">Edit Profile</button>
        </div>
    </div>

    <!-- Profile Details -->
    <div class="content-card">
        <div class="card-header">
            <h2>Personal Information</h2>
        </div>
        <div class="profile-details">
            <div class="detail-group">
                <label>Username</label>
                <p><?= htmlspecialchars($student['username']) ?></p>
            </div>
            <div class="detail-group">
                <label>First Name</label>
                <p><?= htmlspecialchars($student['first_name']) ?></p>
            </div>
            <div class="detail-group">
                <label>Middle Name</label>
                <p><?= htmlspecialchars($student['middle_name'] ?? 'N/A') ?></p>
            </div>
            <div class="detail-group">
                <label>Last Name</label>
                <p><?= htmlspecialchars($student['last_name']) ?></p>
            </div>
            <div class="detail-group">
                <label>Email Address</label>
                <p><?= htmlspecialchars($student['email']) ?></p>
            </div>
            <div class="detail-group">
                <label>Phone Number</label>
                <p><?= htmlspecialchars($student['phone_number'] ?? 'Not provided') ?></p>
            </div>
            <div class="detail-group">
                <label>Date of Birth</label>
                <p><?= $student['date_of_birth'] ? date('F d, Y', strtotime($student['date_of_birth'])) : 'Not provided' ?></p>
            </div>
            <div class="detail-group">
                <label>Gender</label>
                <p><?= htmlspecialchars($student['gender'] ?? 'Not specified') ?></p>
            </div>
            <div class="detail-group full-width">
                <label>Address</label>
                <p><?= htmlspecialchars($student['address'] ?? 'Not provided') ?></p>
            </div>
        </div>
    </div>

    <!-- Account Status -->
    <div class="content-card">
        <div class="card-header">
            <h2>Account Status</h2>
        </div>
        <div class="profile-details">
            <div class="detail-group">
                <label>Account Status</label>
                <p>
                    <?php if ($student['is_active']): ?>
                        <span class="badge badge-success">Active</span>
                    <?php else: ?>
                        <span class="badge badge-danger">Inactive</span>
                    <?php endif; ?>
                </p>
            </div>
            <div class="detail-group">
                <label>Email Verified</label>
                <p>
                    <?php if ($student['is_email_verified']): ?>
                        <span class="badge badge-success">Verified</span>
                    <?php else: ?>
                        <span class="badge badge-warning">Not Verified</span>
                    <?php endif; ?>
                </p>
            </div>
            <div class="detail-group">
                <label>Member Since</label>
                <p><?= date('F d, Y', strtotime($student['created_at'])) ?></p>
            </div>
            <div class="detail-group">
                <label>Last Login</label>
                <p><?= $student['last_login'] ? date('F d, Y H:i', strtotime($student['last_login'])) : 'Never' ?></p>
            </div>
        </div>
    </div>
</div>

<style>
.profile-header {
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    margin-bottom: 30px;
    display: flex;
    align-items: center;
    gap: 25px;
}

.profile-avatar {
    flex-shrink: 0;
}

.profile-avatar img,
.avatar-placeholder {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
}

.avatar-placeholder {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 40px;
    font-weight: bold;
}

.profile-info {
    flex: 1;
}

.profile-info h1 {
    font-size: 28px;
    margin-bottom: 5px;
}

.profile-email {
    color: #718096;
    margin-bottom: 8px;
}

.profile-role {
    display: inline-block;
    padding: 5px 15px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
}

.profile-details {
    padding: 25px;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 25px;
}

.detail-group {
    display: flex;
    flex-direction: column;
}

.detail-group.full-width {
    grid-column: 1 / -1;
}

.detail-group label {
    font-size: 13px;
    color: #718096;
    margin-bottom: 5px;
    font-weight: 600;
    text-transform: uppercase;
}

.detail-group p {
    font-size: 16px;
    color: #2d3748;
}

@media (max-width: 768px) {
    .profile-header {
        flex-direction: column;
        text-align: center;
    }

    .profile-actions {
        width: 100%;
    }

    .profile-actions .btn {
        width: 100%;
    }

    .profile-details {
        grid-template-columns: 1fr;
    }
}
</style>
