// Ana JavaScript Dosyası

// Dil değiştirme (URL parametrelerini koruyarak)
document.addEventListener('DOMContentLoaded', function() {
    // Form gönderiminde dil parametresini koru
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const urlParams = new URLSearchParams(window.location.search);
            const lang = urlParams.get('lang');
            if (lang) {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'lang';
                hiddenInput.value = lang;
                form.appendChild(hiddenInput);
            }
        });
    });

    // Alert mesajlarını otomatik kapat
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => {
                alert.remove();
            }, 500);
        }, 5000);
    });

    // Resim yükleme önizlemesi
    const fileInputs = document.querySelectorAll('input[type="file"]');
    fileInputs.forEach(input => {
        input.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.maxWidth = '100px';
                    img.style.display = 'block';
                    img.style.marginTop = '10px';
                    
                    // Eski önizlemeyi kaldır
                    const oldPreview = input.parentElement.querySelector('img.preview');
                    if (oldPreview) {
                        oldPreview.remove();
                    }
                    
                    img.classList.add('preview');
                    input.parentElement.appendChild(img);
                };
                reader.readAsDataURL(file);
            }
        });
    });

    // Favorileri yönet
    const favoriteBtns = document.querySelectorAll('.btn-favorite');
    favoriteBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const listingId = this.dataset.id;
            const icon = this.querySelector('i');
            
            fetch('/api/toggle_favorite.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ listing_id: listingId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.is_favorite) {
                        this.classList.add('active');
                        icon.classList.remove('far');
                        icon.classList.add('fas');
                    } else {
                        this.classList.remove('active');
                        icon.classList.remove('fas');
                        icon.classList.add('far');
                    }
                } else {
                    if (data.message === 'Lütfen giriş yapın') {
                        // Giriş yapmamışsa login sayfasına yönlendir veya modal aç
                        window.location.href = '/login.php';
                    } else {
                        alert(data.message);
                    }
                }
            })
            .catch(error => {
                console.error('Error toggling favorite:', error);
            });
        });
    });

    // Direkt Mesaj Gönder
    const dmForm = document.getElementById('dm-form');
    if (dmForm) {
        dmForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const listingId = this.querySelector('[name="listing_id"]').value;
            const message = this.querySelector('[name="message"]').value;
            const submitBtn = this.querySelector('button[type="submit"]');
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Gönderiliyor...';

            fetch('/api/send_message.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ 
                    listing_id: listingId,
                    message: message
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                    this.reset();
                } else {
                    showToast(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error sending message:', error);
                showToast('Bir hata oluştu', 'error');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-paper-plane" style="margin-right: 8px;"></i> Mesaj Gönder';
            });
        });
    }
});

// Ana resim değiştirme
function changeMainImage(src) {
    const mainImage = document.getElementById('mainImage');
    if (mainImage) {
        mainImage.src = src;
    }
}

// Fiyat formatı
function formatPrice(price) {
    return new Intl.NumberFormat('tr-TR', {
        style: 'currency',
        currency: 'TRY'
    }).format(price);
}

// AJAX ile dil değiştirme
let isChangingLanguage = false;
window.isChangingLanguage = false; // Global erişim için

function changeLanguage(newLang) {
    // Eğer zaten değişim devam ediyorsa bekle
    if (isChangingLanguage || window.isChangingLanguage) {
        console.log('Language change already in progress, ignoring...');
        return;
    }
    
    // Mevcut dili aktif butondan al (daha güvenilir)
    const langButtons = document.querySelectorAll('.lang-btn');
    let currentLang = null;
    langButtons.forEach(btn => {
        if (btn.classList.contains('active')) {
            currentLang = btn.dataset.lang;
        }
    });
    
    // Eğer aktif buton bulunamazsa, URL'den veya HTML attribute'undan al
    if (!currentLang) {
        const urlParams = new URLSearchParams(window.location.search);
        const urlLang = urlParams.get('lang');
        const htmlLang = document.documentElement.lang;
        currentLang = urlLang || htmlLang || 'tr';
    }
    
    // Eğer zaten istenen dildeyse ve buton da aktifse, hiçbir şey yapma
    if (currentLang === newLang) {
        console.log('Already in language:', newLang);
        return;
    }
    
    console.log('Changing language from', currentLang, 'to', newLang);
    isChangingLanguage = true;
    window.isChangingLanguage = true;
    
    // langButtons zaten yukarıda tanımlı
    langButtons.forEach(btn => {
        btn.disabled = true;
    });
    
    // Loading state göster
    langButtons.forEach(btn => {
        if (btn.dataset.lang === newLang) {
            btn.innerHTML = '<span class="loading-spinner"></span>';
        }
    });
    
    // AJAX isteği
    const formData = new FormData();
    formData.append('lang', newLang);
    
    fetch('/change_language.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload the page to apply PHP translations
            window.location.reload();
        } else {
            showToast(data.message || 'Dil değiştirilemedi', 'error');
            langButtons.forEach(btn => {
                btn.disabled = false;
                btn.innerHTML = btn.dataset.lang.toUpperCase();
            });
            isChangingLanguage = false;
        }
    })
    .catch(error => {
        console.error('Language change error:', error);
        showToast('Bir hata oluştu', 'error');
        langButtons.forEach(btn => {
            btn.disabled = false;
            btn.innerHTML = btn.dataset.lang.toUpperCase();
        });
        isChangingLanguage = false;
        window.isChangingLanguage = false;
    });
}

// Sayfa içeriğini güncelle
function updatePageContent(newLang) {
    // Fade out animasyonu
    document.body.style.transition = 'opacity 0.2s ease';
    document.body.style.opacity = '0.8';
    
    // Önce genel içerikleri güncelle
    document.querySelectorAll('[data-i18n]').forEach(element => {
        const key = element.getAttribute('data-i18n');
        const translations = {
            tr: {
                'home': 'Ana Sayfa',
                'listings': 'İlanlar',
                'contact': 'İletişim',
                'price': 'Fiyat',
                'area': 'Alan',
                'rooms': 'Oda Sayısı',
                'bathrooms': 'Banyo Sayısı',
                'location': 'Konum',
                'address': 'Adres',
                'description': 'Açıklama',
                'offer': 'Teklif Ver',
                'submit': 'Gönder',
                'back': 'Geri',
                'your_name': 'Adınız',
                'your_email': 'E-posta',
                'your_phone': 'Telefon',
                'offer_amount': 'Teklif Tutarı',
                'message': 'Mesaj',
                'basic_info': 'Temel Bilgiler',
                'details': 'Detaylar',
                'land_details': 'Arsa Detayları',
                'residential_details': 'Konut Detayları',
                'commercial_details': 'İşyeri Detayları',
                'infrastructure': 'Altyapı Özellikleri',
                'building_features': 'Bina Özellikleri',
                'location_features': 'Konum Özellikleri',
                'notes': 'Notlar ve Ek Bilgiler',
                'search_title': 'Arama',
                'recent_listings': 'Güncel İlanlar',
                'view_all_listings': 'Tüm İlanları Gör',
                'more_details': 'Daha Fazla Detay ▸',
                'property_request_title': 'Hayalinizdeki Mülkü Bulamadınız mı?',
                'property_request_subtitle': 'Aradığınız özelliklerdeki mülkü bizimle paylaşın, sizin için bulalım!',
                'no_listings': 'İlan bulunamadı'
            },
            en: {
                'home': 'Home',
                'listings': 'Listings',
                'contact': 'Contact',
                'price': 'Price',
                'area': 'Area',
                'rooms': 'Rooms',
                'bathrooms': 'Bathrooms',
                'location': 'Location',
                'address': 'Address',
                'description': 'Description',
                'offer': 'Make Offer',
                'submit': 'Submit',
                'back': 'Back',
                'your_name': 'Your Name',
                'your_email': 'Email',
                'your_phone': 'Phone',
                'offer_amount': 'Offer Amount',
                'message': 'Message',
                'basic_info': 'Basic Information',
                'details': 'Details',
                'land_details': 'Land Details',
                'residential_details': 'Residential Details',
                'commercial_details': 'Commercial Property Details',
                'infrastructure': 'Infrastructure Features',
                'building_features': 'Building Features',
                'location_features': 'Location Features',
                'notes': 'Notes and Additional Information',
                'search_title': 'Search',
                'recent_listings': 'Recent Listings',
                'view_all_listings': 'View All Listings',
                'more_details': 'More Details ▸',
                'property_request_title': 'Couldn\'t Find Your Dream Property?',
                'property_request_subtitle': 'Share the property features you\'re looking for, and we\'ll find it for you!',
                'no_listings': 'No listings found'
            }
        };
        
        if (translations[newLang] && translations[newLang][key]) {
            element.textContent = translations[newLang][key];
        }
    });
    
    // Özel içerik güncellemeleri için sayfaya özel fonksiyon çağır
    if (typeof updatePageContentSpecific === 'function') {
        // Property sayfası için özel güncelleme
        updatePageContentSpecific(newLang)
            .then(() => {
                // İçerik güncellendi, fade in yap
                document.body.style.opacity = '1';
                
                // Toast göster
                const toastMessage = newLang === 'tr' 
                    ? 'Türkçe diline geçildi' 
                    : 'Switched to English';
                showToast(toastMessage, 'success');
                
                // Language change flag'ini sıfırla
                isChangingLanguage = false;
                window.isChangingLanguage = false;
                
                // Butonları tekrar aktif et
                const langButtons = document.querySelectorAll('.lang-btn');
                langButtons.forEach(btn => {
                    btn.disabled = false;
                });
                
                console.log('Language change completed. Flag reset.');
            })
            .catch((error) => {
                console.error('Update error:', error);
                document.body.style.opacity = '1';
                showToast('İçerik güncellenirken hata oluştu', 'error');
                
                // Language change flag'ini sıfırla
                isChangingLanguage = false;
                window.isChangingLanguage = false;
                
                // Butonları tekrar aktif et
                const langButtons = document.querySelectorAll('.lang-btn');
                langButtons.forEach(btn => {
                    btn.disabled = false;
                    if (!btn.classList.contains('active')) {
                        btn.innerHTML = btn.dataset.lang.toUpperCase();
                    }
                });
                
                console.log('Language change failed. Flag reset.');
            });
    } else {
        // Normal sayfalar için hemen fade in ve toast göster
        setTimeout(() => {
            document.body.style.opacity = '1';
            
            const toastMessage = newLang === 'tr' 
                ? 'Türkçe diline geçildi' 
                : 'Switched to English';
            showToast(toastMessage, 'success');
            
            // Language change flag'ini sıfırla
            isChangingLanguage = false;
            window.isChangingLanguage = false;
        }, 200);
    }
}

// Toast notification (global function)
function showToast(message, type = 'success') {
    let toast = document.getElementById('language-toast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'language-toast';
        document.body.appendChild(toast);
    }
    toast.textContent = message;
    toast.className = `toast toast-${type}`;
    // Force reflow
    toast.offsetHeight;
    toast.classList.add('toast-show');
    
    setTimeout(() => {
        toast.classList.remove('toast-show');
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    }, 3000);
}

// Smooth scroll to contact section
function scrollToContact(event) {
    event.preventDefault();
    
    // Check if we're on the index page
    const currentPage = window.location.pathname.split('/').pop();
    
    if (currentPage === 'index.php' || currentPage === '') {
        // We're on index page, scroll to the section
        const contactSection = document.getElementById('property-request');
        if (contactSection) {
            contactSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    } else {
        // We're on another page, redirect to index with hash
        window.location.href = '/index.php#property-request';
    }
}

