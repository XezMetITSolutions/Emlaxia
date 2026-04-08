<footer class="site-footer">
    <div class="footer-top">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-widget">
                    <div class="footer-logo">
                        <img src="../Logo.png" alt="Emlaxia" style="height: 80px; width: auto;">
                    </div>
                    <p class="footer-desc">
                        <?php echo $lang == 'tr' ? 'Emlaxia ile hayalinizi gerçeğe dönüştürün. Geniş portföyümüz ve uzman ekibimizle hizmetinizdeyiz.' : 'Turn your dreams into reality with Emlaxia. We are at your service with our wide portfolio and expert team.'; ?>
                    </p>
                    <div class="social-links">
                        <a href="https://instagram.com/emlaxia" target="_blank" rel="noopener noreferrer"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                
                <div class="footer-widget">
                    <h4><?php echo $lang == 'tr' ? 'Hızlı Erişim' : 'Quick Links'; ?></h4>
                    <ul class="footer-links">
                        <li><a href="/index"><?php echo t('home'); ?></a></li>
                        <li><a href="/ilanlar"><?php echo t('listings'); ?></a></li>
                        <li><a href="/hakkimizda"><?php echo $lang == 'tr' ? 'Hakkımızda' : 'About Us'; ?></a></li>
                        <li><a href="/hizmetler"><?php echo $lang == 'tr' ? 'Hizmetlerimiz' : 'Services'; ?></a></li>
                        <li><a href="#property-request"><?php echo t('contact'); ?></a></li>
                    </ul>
                </div>

                <div class="footer-widget">
                    <h4><?php echo $lang == 'tr' ? 'Kurumsal' : 'Corporate'; ?></h4>
                    <ul class="footer-links">
                        <li><a href="/kosullar"><?php echo $lang == 'tr' ? 'Kullanım Koşulları' : 'Terms of Use'; ?></a></li>
                        <li><a href="/gizlilik"><?php echo $lang == 'tr' ? 'Gizlilik Politikası' : 'Privacy Policy'; ?></a></li>
                        <li><a href="kvkk"><?php echo $lang == 'tr' ? 'KVKK Aydınlatma Metni' : 'KVKK Text'; ?></a></li>
                        <li><a href="/cerezler"><?php echo $lang == 'tr' ? 'Çerez Politikası' : 'Cookie Policy'; ?></a></li>
                    </ul>
                </div>

                <div class="footer-widget">
                    <h4><?php echo t('contact'); ?></h4>
                    <ul class="contact-list">
                        <li>
                            <i class="fas fa-envelope"></i>
                            <a href="mailto:info@emlaxia.com">info@emlaxia.com</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container">
            <div class="footer-bottom-content">
                <p>&copy; <?php echo date('Y'); ?> Emlaxia. <?php echo $lang == 'tr' ? 'Tüm hakları saklıdır.' : 'All rights reserved.'; ?></p>
            </div>
        </div>
    </div>
</footer>

<style>
.site-footer {
    background-color: #0F123D;
    color: #a0aec0;
    font-size: 0.95rem;
}

.footer-top {
    padding: 4rem 0;
}

.footer-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 3rem;
}

.footer-widget h3 {
    color: white;
    font-size: 1.5rem;
    margin-bottom: 1.5rem;
}

.footer-widget h4 {
    color: white;
    font-size: 1.2rem;
    margin-bottom: 1.5rem;
    position: relative;
    padding-bottom: 10px;
}

.footer-widget h4::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 40px;
    height: 2px;
    background: var(--secondary-color);
}

.footer-desc {
    margin-bottom: 1.5rem;
    line-height: 1.6;
}

.social-links {
    display: flex;
    gap: 15px;
}

.social-links a {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    background: rgba(255,255,255,0.1);
    color: white;
    border-radius: 50%;
    transition: var(--transition);
    text-decoration: none;
}

.social-links a:hover {
    background: var(--secondary-color);
    transform: translateY(-3px);
}

.footer-links {
    list-style: none;
    padding: 0;
}

.footer-links li {
    margin-bottom: 10px;
}

.footer-links a {
    color: #a0aec0;
    text-decoration: none;
    transition: var(--transition);
}

.footer-links a:hover {
    color: var(--secondary-color);
    padding-left: 5px;
}

.contact-list {
    list-style: none;
    padding: 0;
}

.contact-list li {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    margin-bottom: 15px;
}

.contact-list i {
    color: var(--secondary-color);
    margin-top: 5px;
}

.contact-list a {
    color: #a0aec0;
    text-decoration: none;
    transition: var(--transition);
}

.contact-list a:hover {
    color: white;
}

.footer-bottom {
    background-color: rgba(0,0,0,0.2);
    padding: 1.5rem 0;
    text-align: center;
}
</style>
