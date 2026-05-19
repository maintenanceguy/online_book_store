<?php
session_start();
require_once '../../config/database.php';
require_once '../../config/helpers.php';
require_once '../../models/user.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../../index.php');
}

$users = User::getAllUsers($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="../../public/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        .modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.7);z-index:999;align-items:center;justify-content:center;}
        .modal-overlay.active{display:flex;}
        .modal-box{background:#1a1f2e;border:1px solid #2a2f3e;border-radius:14px;padding:32px;width:100%;max-width:420px;box-shadow:0 24px 60px rgba(0,0,0,0.6);}
        .modal-box h3{color:#f1f5f9;margin-bottom:6px;font-size:1.1rem;}
        .modal-box p{color:#64748b;font-size:0.87rem;margin-bottom:20px;}
        .modal-box label{color:#94a3b8;font-size:0.85rem;display:block;margin-bottom:6px;}
        .modal-box input[type="password"]{width:100%;padding:11px 14px;background:#0f1117;border:1px solid #2a2f3e;border-radius:8px;color:#e2e8f0;font-size:0.9rem;margin-bottom:16px;}
        .modal-box input[type="password"]:focus{outline:none;border-color:#ef4444;}
        .modal-actions{display:flex;gap:10px;justify-content:flex-end;}
        .modal-error{color:#f87171;font-size:0.83rem;margin-bottom:12px;display:none;}
    </style>
</head>
<body>

<div class="topbar">
    <h1>👥 Manage Users</h1>
    <div>
        <a href="dashboard.php">Dashboard</a>
        <a href="manage_books.php">Books</a>
        <a href="view_orders.php">Orders</a>
        <a href="../../controllers/logoutController.php">Logout</a>
    </div>
</div>

<div style="padding:36px 40px;">

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success" style="margin-bottom:16px;">
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['errors'])): ?>
        <div class="alert alert-error" style="margin-bottom:16px;">
            <?php foreach ($_SESSION['errors'] as $e): ?><p><?= $e ?></p><?php endforeach;
            unset($_SESSION['errors']); ?>
        </div>
    <?php endif; ?>

    <div class="table-card">
        <h3 style="margin-bottom:20px;">Registered Users (<?= count($users) ?>)</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Joined</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                <tr>
                    <td>#<?= $u['id'] ?></td>
                    <td><?= htmlspecialchars($u['name']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td>
                        <span class="status <?= $u['role'] === 'admin' ? 'confirmed' : 'delivered' ?>">
                            <?= ucfirst($u['role']) ?>
                        </span>
                    </td>
                    <td><?= $u['created_at'] ?></td>
                    <td>
                        <?php if ($u['role'] === 'customer'): ?>
                            <a href="../../controllers/deleteCustomerController.php?id=<?= $u['id'] ?>"
                               onclick="return confirm('Delete this customer account?')"
                               class="btn btn-danger" style="padding:5px 14px; font-size:0.8rem;">
                                Delete
                            </a>
                        <?php elseif ((int)$u['id'] !== (int)$_SESSION['user_id']): ?>
                            <button
                                onclick="openDeleteAdminModal(<?= $u['id'] ?>, '<?= htmlspecialchars($u['name'], ENT_QUOTES) ?>')"
                                class="btn btn-danger"
                                style="padding:5px 14px; font-size:0.8rem; background:#7c3aed; border:none; cursor:pointer;">
                                Delete Admin
                            </button>
                        <?php else: ?>
                            <span style="color:#475569; font-size:0.82rem;">You (protected)</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="deleteAdminModal">
    <div class="modal-box">
        <div style="font-size:1.8rem; text-align:center; margin-bottom:12px;">🔐</div>
        <h3 style="text-align:center;">Delete Admin Account</h3>
        <p style="text-align:center;">
            You are about to delete <strong id="adminNameLabel" style="color:#f1f5f9;"></strong>.<br>
            Enter the secret key to confirm.
        </p>

        <form method="POST" action="../../controllers/deleteAdminController.php" id="deleteAdminForm">
            <input type="hidden" name="id" id="deleteAdminId">

            <label>Secret Admin Key</label>
            <input type="password" name="secret_key" id="secretKeyInput" placeholder="Enter secret key" required>

            <p class="modal-error" id="modalError">Secret key is required.</p>

            <div class="modal-actions">
                <button type="button" onclick="closeDeleteAdminModal()"
                        class="btn" style="background:#2a2f3e; color:#e2e8f0;">
                    Cancel
                </button>
                <button type="submit" class="btn btn-danger">
                    Confirm Delete
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openDeleteAdminModal(id, name) {
    document.getElementById('deleteAdminId').value = id;
    document.getElementById('adminNameLabel').textContent = name;
    document.getElementById('secretKeyInput').value = '';
    document.getElementById('modalError').style.display = 'none';
    document.getElementById('deleteAdminModal').classList.add('active');
    setTimeout(() => document.getElementById('secretKeyInput').focus(), 100);
}

function closeDeleteAdminModal() {
    document.getElementById('deleteAdminModal').classList.remove('active');
}

document.getElementById('deleteAdminForm').addEventListener('submit', function(e) {
    const key = document.getElementById('secretKeyInput').value.trim();
    if (!key) {
        e.preventDefault();
        document.getElementById('modalError').style.display = 'block';
    }
});

document.getElementById('deleteAdminModal').addEventListener('click', function(e) {
    if (e.target === this) closeDeleteAdminModal();
});
</script>

</body>
</html>
