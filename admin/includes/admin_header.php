<header class="site-header">
    <div class="container">
        <div class="header-content">
            <div class="logo">
                <a href="admin/dashboard">
                    <img src="/Logo.png" alt="Emlaxia" style="height: 50px; width: auto;">
                    <span style="font-weight: 800; font-size: 1.2rem; color: #D3AF37; margin-left: 10px;">Admin Panel</span>
                </a>
            </div>
            <nav class="main-nav">
                <a href="admin/dashboard"><?php echo t('dashboard'); ?></a>
                <a href="admin/ilanlar"><?php echo t('listings'); ?></a>

                <?php if (($_SESSION['admin_role'] ?? 'admin') === 'admin'): ?>
                    <a href="admin/offers"><?php echo t('offers'); ?></a>
                    <a href="admin/approvals"><?php echo $lang == 'tr' ? 'Onaylar' : 'Approvals'; ?></a>
                    <a href="admin/manage_users"><?php echo $lang == 'tr' ? 'Kullanıcılar' : 'Users'; ?></a>
                <?php endif; ?>

                <a href="admin/logout"><?php echo t('logout'); ?></a>
            </nav>
        </div>
    </div>
</header>

<?php if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success">
        <?php echo $_SESSION['success_message'];
        unset($_SESSION['success_message']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error_message'])): ?>
    <div class="alert alert-error">
        <?php echo $_SESSION['error_message'];
        unset($_SESSION['error_message']); ?>
    </div>
<?php endif; ?>