<?php require_once '../layouts/header.php'; ?>

<div class="auth-container" style="max-width:440px; margin:50px auto;">
    <div class="card">
        <h2>Welcome Back</h2>
        <p style="color:#64748b; margin-bottom:24px; margin-top:-10px;">Sign in to your account</p>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <?php if (isset($_SESSION['errors'])): ?>
            <div class="alert alert-error">
                <?php foreach ($_SESSION['errors'] as $e): ?>
                    <p><?= $e; ?></p>
                <?php endforeach; unset($_SESSION['errors']); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="../../controllers/loginController.php" onsubmit="return validateLoginForm()">

            <label>Email</label>
            <input type="email" name="email" id="email"
                value="<?= isset($_COOKIE['remember_email']) ? htmlspecialchars($_COOKIE['remember_email']) : ''; ?>">

            <label>Password</label>
            <input type="password" name="password" id="password">

            <label style="flex-direction:row; display:flex; align-items:center; gap:8px; cursor:pointer;">
                <input type="checkbox" name="remember" style="width:auto; margin:0;">
                Remember me
            </label>

            <br>
            <button type="submit" style="width:100%;">Sign In</button>
        </form>

        <p style="text-align:center; margin-top:20px; color:#64748b; font-size:0.88rem;">
            Don't have an account? <a href="register.php">Register here</a>
        </p>
        <p style="text-align:center; margin-top:10px; color:#475569; font-size:0.82rem;">
            Admin? <a href="admin_register.php" style="color:#4f46e5;">Create admin account →</a>
        </p>
    </div>
</div>

<script>
function validateLoginForm() {
    const email    = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value.trim();
    if (!email)    { alert("Email is required.");    return false; }
    if (!password) { alert("Password is required."); return false; }
    return true;
}
</script>

<?php require_once '../layouts/footer.php'; ?>
