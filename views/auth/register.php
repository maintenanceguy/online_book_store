<?php require_once '../layouts/header.php'; ?>

<div style="max-width:480px; margin:50px auto;">
    <div class="card">
        <h2>Create Account</h2>
        <p style="color:#64748b; margin-bottom:24px; margin-top:-10px;">Join the bookstore today</p>

        <?php if (isset($_SESSION['errors'])): ?>
            <div class="alert alert-error">
                <?php foreach ($_SESSION['errors'] as $e): ?>
                    <p><?= $e; ?></p>
                <?php endforeach; unset($_SESSION['errors']); ?>
            </div>
        <?php endif; ?>

        <!-- FIX: Original form had no action, it called AuthController.php which used
             a broken path to config. Now points directly to registerController.php -->
        <form method="POST" action="../../controllers/registerController.php" id="registerForm">

            <label>Full Name</label>
            <input type="text" name="name" placeholder="Your name" required>

            <label>Email</label>
            <input type="email" name="email" placeholder="you@example.com" required>

            <label>Password <span style="color:#64748b;">(min 8 characters)</span></label>
            <input type="password" name="password" placeholder="Password" required>

            <label>Address</label>
            <textarea name="address" placeholder="Your address" rows="2"></textarea>

            <label>Phone</label>
            <input type="text" name="phone" placeholder="01XXXXXXXXX">

            <button type="submit" style="width:100%;">Create Account</button>
        </form>

        <p style="text-align:center; margin-top:20px; color:#64748b; font-size:0.88rem;">
            Already have an account? <a href="login.php">Sign in</a>
        </p>
    </div>
</div>

<?php require_once '../layouts/footer.php'; ?>
