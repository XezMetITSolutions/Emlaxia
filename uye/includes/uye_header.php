<?php
$current_page = basename($_SERVER['PHP_SELF'], '.php');
$user_name = $_SESSION['user_full_name'] ?? $_SESSION['user_username'] ?? 'Kullanıcı';
?>
<header class="panel-header">
    <div class="panel-header-inner">
        <div class="panel-logo">
            <a href="/uye/dashboard">👥 Emlaxia <span class="panel-badge member">Üye Paneli</span></a>
        </div>
        <nav class="panel-nav">
            <a href="/uye/dashboard" class="<?php echo $current_page === 'dashboard' ? 'active' : ''; ?>">
                📊 <?php echo $lang === 'tr' ? 'Panel' : 'Dashboard'; ?>
            </a>
            <a href="/uye/tekliflerim" class="<?php echo $current_page === 'tekliflerim' ? 'active' : ''; ?>">
                💼 <?php echo $lang === 'tr' ? 'Tekliflerim' : 'My Offers'; ?>
            </a>
            <a href="/uye/favorites" class="<?php echo $current_page === 'favorites' ? 'active' : ''; ?>">
                ❤️ <?php echo $lang === 'tr' ? 'Favorilerim' : 'Favorites'; ?>
            </a>
            <a href="/uye/mesajlar" class="<?php echo $current_page === 'mesajlar' ? 'active' : ''; ?>">
                ✉️ <?php echo $lang === 'tr' ? 'Mesajlarım' : 'Messages'; ?>
            </a>
            <a href="/uye/profil" class="<?php echo $current_page === 'profil' ? 'active' : ''; ?>">
                ⚙️ <?php echo $lang === 'tr' ? 'Profil' : 'Profile'; ?>
            </a>
        </nav>
        <div class="panel-user">
            <span class="panel-user-name"><?php echo htmlspecialchars($user_name); ?></span>
            <a href="/uye/logout.php" class="panel-logout">
                🚪 <?php echo $lang === 'tr' ? 'Çıkış' : 'Logout'; ?>
            </a>
        </div>
    </div>
</header>

<style>
    .panel-header {
        background: linear-gradient(135deg, #0f172a 0%, #334155 100%);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        position: sticky;
        top: 0;
        z-index: 1000;
    }

    .panel-header-inner {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 2rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        height: 70px;
    }

    .panel-logo a {
        color: white;
        text-decoration: none;
        font-size: 1.35rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .panel-badge.member {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        font-size: 0.7rem;
        font-weight: 600;
        padding: 0.2rem 0.6rem;
        border-radius: 20px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .panel-nav {
        display: flex;
        gap: 0.25rem;
    }

    .panel-nav a {
        color: rgba(255, 255, 255, 0.7);
        text-decoration: none;
        padding: 0.6rem 1rem;
        border-radius: 10px;
        font-size: 0.9rem;
        font-weight: 500;
        transition: all 0.2s;
    }

    .panel-nav a:hover {
        color: white;
        background: rgba(255, 255, 255, 0.1);
    }

    .panel-nav a.active {
        color: white;
        background: rgba(16, 185, 129, 0.3);
        font-weight: 600;
    }

    .panel-user {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .panel-user-name {
        color: rgba(255, 255, 255, 0.8);
        font-size: 0.875rem;
        font-weight: 500;
    }

    .panel-logout {
        color: rgba(255, 255, 255, 0.6);
        text-decoration: none;
        font-size: 0.85rem;
        padding: 0.4rem 0.8rem;
        border-radius: 8px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        transition: all 0.2s;
    }

    @media (max-width: 900px) {
        .panel-header-inner { flex-wrap: wrap; height: auto; padding: 1rem; gap: 0.75rem; }
        .panel-nav { width: 100%; overflow-x: auto; order: 3; }
        .panel-user-name { display: none; }
    }
</style>

<?php if (isset($_SESSION['success_message'])): ?>
    <div style="max-width:1400px;margin:1rem auto;padding:1rem 2rem;background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;border-radius:12px;font-weight:500;">
        <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
    </div>
<?php endif; ?>
<?php if (isset($_SESSION['error_message'])): ?>
    <div style="max-width:1400px;margin:1rem auto;padding:1rem 2rem;background:#fef2f2;color:#dc2626;border:1px solid #fecaca;border-radius:12px;font-weight:500;">
        <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
    </div>
<?php endif; ?>
