# Emlak Satış Web Sitesi

Türkçe ve İngilizce dil desteği olan, sadece satış ilanları içeren emlak web sitesi.

## Özellikler

- ✅ Ev, daire, arsa, dükkan ve villa satış ilanları
- ✅ Türkçe ve İngilizce dil desteği
- ✅ İlan filtreleme (fiyat, konum, mülk tipi)
- ✅ Müşteri teklif verme sistemi
- ✅ Admin paneli:
  - İlan yönetimi (ekleme, düzenleme, silme)
  - Teklif yönetimi (teklifleri görüntüleme, kabul/reddetme)

## Kurulum

### 1. Veritabanı Kurulumu

1. MySQL/MariaDB veritabanı sunucunuzda `database.sql` dosyasını çalıştırın:
   ```sql
   mysql -u d04537d6 -p d04537d6 < database.sql
   ```

2. Varsayılan admin kullanıcı bilgileri:
   - **Kullanıcı Adı:** admin
   - **Şifre:** admin123

### 2. Veritabanı Bağlantısı

Veritabanı bağlantı ayarları `config.php` dosyasında zaten yapılandırılmıştır:
- **Host:** localhost
- **Database:** d04537d6
- **Username:** d04537d6
- **Password:** 01528797#Mb##

### 3. Klasör İzinleri

`uploads` klasörüne yazma izni verin (resim yüklemeleri için):
```bash
chmod 755 uploads
```

### 4. Web Sunucusu

Dosyaları web sunucunuzun root dizinine yükleyin veya XAMPP/WAMP/MAMP gibi bir yerel sunucu kullanın.

## Kullanım

### Admin Paneli

1. `admin/login.php` adresine gidin
2. Admin kullanıcı adı ve şifre ile giriş yapın
3. İlan eklemek için "İlan Yönetimi" sayfasından "Yeni İlan" butonuna tıklayın
4. Teklifleri görüntülemek için "Teklif Yönetimi" sayfasına gidin

### Müşteri Tarafı

1. Ana sayfada (`index.php`) öne çıkan ilanları görüntüleyin
2. "İlanlar" sayfasından tüm ilanları listeleyin ve filtreleyin
3. İlan detay sayfasından teklif verin

## Dosya Yapısı

```
Emlak/
├── admin/              # Admin paneli dosyaları
│   ├── dashboard.php   # Admin ana sayfa
│   ├── login.php       # Admin giriş
│   ├── listings.php    # İlan yönetimi
│   ├── listing_form.php # İlan ekleme/düzenleme
│   ├── offers.php      # Teklif yönetimi
│   └── logout.php      # Çıkış
├── includes/           # Ortak dosyalar
│   ├── header.php      # Site header
│   └── footer.php      # Site footer
├── assets/             # Statik dosyalar
│   ├── css/
│   │   └── style.css   # Stil dosyası
│   ├── js/
│   │   └── main.js     # JavaScript dosyası
│   └── images/         # Resimler
├── uploads/            # Yüklenen resimler
├── config.php          # Veritabanı bağlantısı
├── lang.php            # Dil dosyası
├── index.php           # Ana sayfa
├── listings.php        # İlan listeleme
├── property.php        # İlan detay
├── offer.php           # Teklif gönderme
├── database.sql        # Veritabanı yapısı
└── README.md           # Bu dosya
```

## Dil Değiştirme

Site üst kısmındaki TR/EN butonlarına tıklayarak dil değiştirebilirsiniz.

## Notlar

- Şifre hash'leme için PHP'nin `password_hash()` fonksiyonu kullanılmıştır
- Resim yüklemeleri `uploads` klasörüne kaydedilir
- Güvenlik için SQL injection koruması PDO prepared statements ile sağlanmıştır
- XSS koruması için `htmlspecialchars()` kullanılmıştır

## Lisans

Bu proje özel kullanım içindir.


