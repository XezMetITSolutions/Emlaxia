<header class="site-header">
    <div class="top-bar">
        <div class="container">
            <div class="top-bar-content">
                <div class="contact-info">
                    <a href="mailto:info@emlaxia.com"><i class="fas fa-envelope"></i> info@emlaxia.com</a>
                </div>
                <!-- Social media icons removed as requested -->
            </div>
        </div>
    </div>
    <div class="main-header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <a href="/">
                        <img src="/Logo.png" alt="Emlaxia" class="logo-img">
                    </a>
                </div>
                <nav class="main-nav">
                    <a href="/"
                        class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>"><?php echo t('home'); ?></a>
                    <a href="/ilanlar"
                        class="<?php echo basename($_SERVER['PHP_SELF']) == 'ilanlar.php' ? 'active' : ''; ?>"><?php echo t('listings'); ?></a>
                    <a href="/kurumsal"
                        class="<?php echo basename($_SERVER['PHP_SELF']) == 'kurumsal.php' ? 'active' : ''; ?>"><?php echo $lang == 'tr' ? 'Kurumsal' : 'Corporate'; ?></a>
                    <a href="/hizmetler"
                        class="<?php echo basename($_SERVER['PHP_SELF']) == 'hizmetler.php' ? 'active' : ''; ?>"><?php echo $lang == 'tr' ? 'Hizmetlerimiz' : 'Services'; ?></a>
                    <a href="/yenisehir"
                        class="<?php echo basename($_SERVER['PHP_SELF']) == 'yenisehir.php' ? 'active' : ''; ?>"><?php echo $lang == 'tr' ? 'Projeler' : 'Projects'; ?></a>
                    <a href="/#property-request"><?php echo t('contact'); ?></a>
                </nav>
                <div class="header-actions">
                    <a href="/ilan-ver" class="btn-post-ad">
                        <i class="fas fa-plus-circle"></i> <?php echo $lang == 'tr' ? 'İlan Ver' : 'Post Ad'; ?>
                    </a>
                    
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="user-nav">
                            <?php
                            $panel_url = '/uye/dashboard';
                            if ($_SESSION['user_type'] === 'emlakci')
                                $panel_url = '/emlakci/dashboard';
                            elseif ($_SESSION['user_type'] === 'bireysel')
                                $panel_url = '/bireysel/dashboard';
                            ?>
                            <a href="<?php echo $panel_url; ?>" class="btn-panel">
                                <i class="fas fa-user-circle"></i> <?php echo $lang == 'tr' ? 'Hesabım' : 'My Account'; ?>
                            </a>
                        </div>
                    <?php elseif (isset($_SESSION['admin_logged_in'])): ?>
                        <div class="user-nav">
                            <a href="/admin/dashboard" class="btn-panel">
                                <i class="fas fa-user-shield"></i> Admin
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="auth-btns">
                            <a href="/giris" class="btn-login"><?php echo $lang == 'tr' ? 'Giriş Yap' : 'Login'; ?></a>
                        </div>
                    <?php endif; ?>
                    
                    <div class="language-switcher">
                        <button type="button" class="lang-btn <?php echo $lang == 'tr' ? 'active' : ''; ?>"
                            data-lang="tr" onclick="changeLanguage('tr')">TR</button>
                        <button type="button" class="lang-btn <?php echo $lang == 'en' ? 'active' : ''; ?>"
                            data-lang="en" onclick="changeLanguage('en')">EN</button>
                    </div>
                    
                    <button class="mobile-nav-toggle" aria-label="Menu">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Mobile Navigation Drawer -->
    <div class="mobile-nav-drawer">
        <div class="drawer-header">
            <img src="/Logo.png" alt="Emlaxia" class="logo-img">
            <button class="drawer-close"><i class="fas fa-times"></i></button>
        </div>
        <nav class="drawer-nav">
            <a href="/"><?php echo t('home'); ?></a>
            <a href="/ilanlar"><?php echo t('listings'); ?></a>
            <a href="/kurumsal"><?php echo $lang == 'tr' ? 'Kurumsal' : 'Corporate'; ?></a>
            <a href="/hizmetler"><?php echo $lang == 'tr' ? 'Hizmetlerimiz' : 'Services'; ?></a>
            <a href="/yenisehir"><?php echo $lang == 'tr' ? 'Projeler' : 'Projects'; ?></a>
        </nav>
        <div class="drawer-footer">
            <a href="/kayit" class="btn-post-ad" style="width: 100%; justify-content: center; margin-bottom: 1rem;">
                <i class="fas fa-plus-circle"></i> <?php echo $lang == 'tr' ? 'İlan Ver' : 'Post Ad'; ?>
            </a>
            <?php if (!isset($_SESSION['user_id']) && !isset($_SESSION['admin_logged_in'])): ?>
                <div class="auth-btns" style="flex-direction: column; width: 100%;">
                    <a href="/giris" class="btn-login" style="text-align: center;"><?php echo $lang == 'tr' ? 'Giriş Yap' : 'Login'; ?></a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="drawer-overlay"></div>
</header>

<style>
    /* Header Specific Styles */
    .site-header {
        background-color: #0F123D;
    }

    .top-bar {
        background-color: #0A0D2E;
        color: white;
        padding: 6px 0;
        font-size: 0.8rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .top-bar-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .top-bar a {
        color: rgba(255, 255, 255, 0.8);
        text-decoration: none;
        margin-right: 15px;
        transition: var(--transition);
    }

    .top-bar a:hover {
        color: #D3AF37;
    }

    .main-header {
        background: #0F123D;
        box-shadow: none;
        padding: 0.5rem 0;
    }

    .header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .logo a {
        display: flex;
        align-items: center;
        gap: 10px;
        text-decoration: none;
    }

    .logo-img {
        height: 80px;
        width: auto;
        transition: var(--transition);
    }

    .logo-img:hover {
        transform: scale(1.05);
    }

    .logo-text {
        font-size: 1.25rem;
        font-weight: 800;
        color: white;
        letter-spacing: 1px;
    }

    .main-nav {
        display: flex;
        gap: 10px;
    }

    .main-nav a {
        color: rgba(255, 255, 255, 0.9);
        font-weight: 600;
        text-decoration: none;
        padding: 5px 8px;
        font-size: 0.85rem;
        transition: var(--transition);
        position: relative;
        white-space: nowrap;
    }

    .main-nav a:hover,
    .main-nav a.active {
        color: #D3AF37;
    }

    .main-nav a::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        width: 0;
        height: 2px;
        background: #D3AF37;
        transition: var(--transition);
        transform: translateX(-50%);
    }

    .main-nav a:hover::after,
    .main-nav a.active::after {
        width: 100%;
    }

    .header-actions {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .language-switcher {
        display: flex;
        gap: 5px;
        background: rgba(255, 255, 255, 0.1);
        padding: 4px;
        border-radius: 8px;
    }

    .language-switcher .lang-btn {
        color: rgba(255, 255, 255, 0.8);
        background: transparent;
        border: none;
        padding: 6px 12px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.875rem;
        cursor: pointer;
        transition: var(--transition);
    }

    .language-switcher .lang-btn:hover {
        color: white;
        background: rgba(255, 255, 255, 0.1);
    }

    .language-switcher .lang-btn.active {
        background: #D3AF37;
        color: #0F123D;
    }

    /* Auth Buttons */
    .auth-btns {
        display: flex;
        gap: 10px;
    }

    .btn-login {
        color: white;
        text-decoration: none;
        font-weight: 700;
        font-size: 0.85rem;
        padding: 10px 24px;
        border: 2px solid rgba(211, 175, 55, 0.5);
        border-radius: 12px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        white-space: nowrap;
        background: rgba(211, 175, 55, 0.05);
    }

    .btn-login:hover {
        background: #D3AF37;
        color: #0F123D;
        border-color: #D3AF37;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(211, 175, 55, 0.3);
    }

    .btn-register {
        background: #D3AF37;
        color: #0F123D;
        text-decoration: none;
        font-weight: 700;
        font-size: 0.85rem;
        padding: 8px 16px;
        border-radius: 8px;
        transition: all 0.2s;
    }

    .btn-register:hover {
        background: #C29F2E;
        transform: translateY(-1px);
    }

    .btn-post-ad {
        background: linear-gradient(135deg, #FFD700 0%, #D3AF37 100%);
        color: #0F123D;
        text-decoration: none;
        font-weight: 700;
        font-size: 0.8rem;
        padding: 8px 15px;
        border-radius: 50px;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 15px rgba(211, 175, 55, 0.3);
        border: 2px solid transparent;
        white-space: nowrap;
    }

    .btn-post-ad:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(211, 175, 55, 0.4);
        background: linear-gradient(135deg, #D3AF37 0%, #FFD700 100%);
    }

    .btn-post-ad i {
        font-size: 0.9rem;
    }

    .auth-btns .btn-register {
        background: transparent;
        color: white;
        border: 1px solid rgba(255, 255, 255, 0.3);
    }
    
    .auth-btns .btn-register:hover {
        background: rgba(255, 255, 255, 0.1);
        color: #D3AF37;
        border-color: #D3AF37;
    }


    .btn-panel {
        background: rgba(211, 175, 55, 0.15);
        color: #D3AF37;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.85rem;
        padding: 8px 16px;
        border-radius: 8px;
        border: 1px solid rgba(211, 175, 55, 0.3);
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }

    .btn-panel:hover {
        background: #D3AF37;
        color: #0F123D;
    }

    /* Mobile Menu styles */
    .mobile-nav-toggle {
        display: none;
        background: transparent;
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: white;
        padding: 8px 12px;
        border-radius: 8px;
        font-size: 1.25rem;
        cursor: pointer;
        transition: var(--transition);
    }

    .mobile-nav-toggle:hover {
        background: rgba(255, 255, 255, 0.1);
        border-color: #D3AF37;
        color: #D3AF37;
    }

    .mobile-nav-drawer {
        position: fixed;
        top: 0;
        right: -300px;
        width: 300px;
        height: 100%;
        background: #0F123D;
        z-index: 2000;
        transition: right 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        padding: 2rem;
        display: flex;
        flex-direction: column;
        box-shadow: -10px 0 30px rgba(0,0,0,0.5);
    }

    .mobile-nav-drawer.active {
        right: 0;
    }

    .drawer-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }

    .drawer-close {
        background: transparent;
        border: none;
        color: white;
        font-size: 1.5rem;
        cursor: pointer;
    }

    .drawer-nav {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .drawer-nav a {
        color: white;
        text-decoration: none;
        font-size: 1.1rem;
        font-weight: 600;
        transition: var(--transition);
    }

    .drawer-nav a:hover {
        color: #D3AF37;
        padding-left: 5px;
    }

    .drawer-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
        z-index: 1999;
        display: none;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .drawer-overlay.active {
        display: block;
        opacity: 1;
    }

    @media (max-width: 1200px) {
        .main-nav {
            display: none;
        }

        .mobile-nav-toggle {
            display: block;
        }

        .header-actions .btn-post-ad,
        .header-actions .auth-btns {
            display: none;
        }
    }

    @media (max-width: 768px) {
        .top-bar {
            display: none;
        }
        
        .logo-img {
            height: 60px;
        }
    }

</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggle = document.querySelector('.mobile-nav-toggle');
        const drawer = document.querySelector('.mobile-nav-drawer');
        const overlay = document.querySelector('.drawer-overlay');
        const close = document.querySelector('.drawer-close');

        if (toggle && drawer && overlay) {
            toggle.addEventListener('click', () => {
                drawer.classList.add('active');
                overlay.style.display = 'block';
                setTimeout(() => overlay.classList.add('active'), 10);
                document.body.style.overflow = 'hidden';
            });
        }

        const closeMenu = () => {
            if (drawer) drawer.classList.remove('active');
            if (overlay) {
                overlay.classList.remove('active');
                setTimeout(() => overlay.style.display = 'none', 300);
            }
            document.body.style.overflow = '';
        };

        if (close) close.addEventListener('click', closeMenu);
        if (overlay) overlay.addEventListener('click', closeMenu);
    });
</script>