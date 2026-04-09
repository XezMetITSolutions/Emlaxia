<?php
require_once '../config.php';

// Admin kontrolü
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: login.php');
    exit;
}

// AJAX silme işlemi artık delete_listing.php'de yapılıyor

// İlanları getir
$stmt = $pdo->query("SELECT l.*, u.full_name, u.username, u.email, u.user_type, u.firma_adi 
                       FROM listings l 
                       LEFT JOIN users u ON l.user_id = u.id 
                       ORDER BY l.created_at DESC");
$listings = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo t('manage_listings'); ?> - Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <?php include 'includes/admin_header.php'; ?>

    <main>
        <div class="container">
            <div class="admin-header">
                <h1><?php echo t('manage_listings'); ?></h1>
                <a href="listing_form.php" class="btn btn-primary"><?php echo t('new_listing'); ?></a>
            </div>

            <div class="admin-table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th><?php echo $lang == 'tr' ? 'Kullanıcı' : 'User'; ?></th>
                            <th><?php echo t('title'); ?></th>
                            <th><?php echo t('property_type'); ?></th>
                            <th><?php echo t('price'); ?></th>
                            <th><?php echo t('status'); ?></th>
                            <th><?php echo $lang == 'tr' ? 'İşlemler' : 'Actions'; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($listings)): ?>
                            <tr>
                                <td colspan="6" class="text-center"><?php echo t('no_listings'); ?></td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($listings as $listing): ?>
                                <tr>
                                    <td><?php echo $listing['id']; ?></td>
                                    <td>
                                        <?php if ($listing['full_name'] || $listing['username']): ?>
                                            <div style="font-weight: 600; color: #0F123D;">
                                                <?php echo htmlspecialchars($listing['full_name'] ?: $listing['username']); ?>
                                            </div>
                                            <div style="font-size: 0.75rem; color: #64748b;">
                                                <?php echo htmlspecialchars($listing['email']); ?>
                                            </div>
                                            <?php if ($listing['user_type'] === 'emlakci' && $listing['firma_adi']): ?>
                                                <div style="font-size: 0.75rem; color: #64748b; font-style: italic;">
                                                    🏢 <?php echo htmlspecialchars($listing['firma_adi']); ?>
                                                </div>
                                            <?php endif; ?>
                                            <span class="user-badge badge-<?php echo $listing['user_type']; ?>">
                                                <?php echo $listing['user_type'] === 'emlakci' ? '🏢 Emlakçı' : '👤 Bireysel'; ?>
                                            </span>
                                        <?php else: ?>
                                            <span style="color: #94a3b8; font-size: 0.85rem;">Sistem / Silinmiş</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($listing['title_' . $lang]); ?></td>
                                    <td><?php echo t($listing['property_type']); ?></td>
                                    <td><?php echo number_format($listing['price'], 2, ',', '.'); ?>
                                        <?php echo t('currency'); ?></td>
                                    <td><?php echo t($listing['status']); ?></td>
                                    <td>
                                        <a href="listing_form.php?id=<?php echo $listing['id']; ?>"
                                            class="btn btn-small btn-secondary"><?php echo t('edit'); ?></a>
                                        <?php if (($_SESSION['admin_role'] ?? 'admin') === 'admin'): ?>
                                            <button type="button" class="btn btn-small btn-danger btn-delete-listing"
                                                data-id="<?php echo $listing['id']; ?>"
                                                data-title="<?php echo htmlspecialchars($listing['title_' . $lang], ENT_QUOTES); ?>">
                                                <?php echo t('delete'); ?>
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>

    <!-- Toast Notification -->
    <div id="toast" class="toast"></div>

    <script src="../assets/js/main.js"></script>
    <script>
        // AJAX ile silme işlemi
        document.addEventListener('DOMContentLoaded', function () {
            const deleteButtons = document.querySelectorAll('.btn-delete-listing');

            deleteButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const id = this.getAttribute('data-id');
                    const title = this.getAttribute('data-title');
                    const lang = '<?php echo $lang; ?>';
                    const confirmMessage = lang === 'tr'
                        ? `"${title}" adlı ilanı silmek istediğinizden emin misiniz?`
                        : `Are you sure you want to delete listing "${title}"?`;

                    if (!confirm(confirmMessage)) {
                        return;
                    }

                    // Butonu devre dışı bırak ve loading göster
                    const originalText = this.innerHTML;
                    this.disabled = true;
                    this.innerHTML = lang === 'tr' ? '<span class="loading-spinner"></span> Siliniyor...' : '<span class="loading-spinner"></span> Deleting...';

                    // AJAX isteği
                    const formData = new FormData();
                    formData.append('id', id);

                    fetch('delete_listing.php', {
                        method: 'POST',
                        body: formData
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Başarı mesajı göster
                                showToast(data.message, 'success');

                                // Satırı animasyonla kaldır
                                const row = this.closest('tr');
                                row.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                                row.style.opacity = '0';
                                row.style.transform = 'translateX(-20px)';

                                setTimeout(() => {
                                    row.remove();

                                    // Eğer tüm satırlar silindiyse "no listings" mesajı göster
                                    const tbody = document.querySelector('.admin-table tbody');
                                    if (tbody.querySelectorAll('tr').length === 0) {
                                        tbody.innerHTML = '<tr><td colspan="6" class="text-center"><?php echo t('no_listings'); ?></td></tr>';
                                    }
                                }, 300);
                            } else {
                                // Hata mesajı göster
                                showToast(data.message || 'Hata oluştu', 'error');

                                // Butonu tekrar etkinleştir
                                this.disabled = false;
                                this.innerHTML = originalText;
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showToast(lang === 'tr' ? 'Bir hata oluştu' : 'An error occurred', 'error');

                            // Butonu tekrar etkinleştir
                            this.disabled = false;
                            this.innerHTML = originalText;
                        });
                });
            });
        });

        // Toast notification fonksiyonu
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.className = `toast toast-${type} toast-show`;

            setTimeout(() => {
                toast.classList.remove('toast-show');
            }, 3000);
        }
    </script>

    <style>
        .btn-delete-listing {
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-delete-listing:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .loading-spinner {
            display: inline-block;
            width: 12px;
            height: 12px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 0.6s linear infinite;
            margin-right: 6px;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .toast {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            padding: 1rem 1.5rem;
            border-radius: var(--radius-lg);
            color: white;
            font-weight: 500;
            box-shadow: var(--shadow-xl);
            z-index: 10000;
            transform: translateY(100px);
            opacity: 0;
            transition: all 0.3s ease;
            max-width: 400px;
            word-wrap: break-word;
        }

        .toast-show {
            transform: translateY(0);
            opacity: 1;
        }

        .toast-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .toast-error {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }

        .admin-table tbody tr {
            transition: opacity 0.3s ease, transform 0.3s ease;
        }

        .user-badge {
            display: inline-block;
            font-size: 0.65rem;
            font-weight: 600;
            padding: 0.15rem 0.4rem;
            border-radius: 4px;
            margin-top: 4px;
        }

        .badge-emlakci {
            background: #dcfce7;
            color: #16a34a;
        }

        .badge-bireysel {
            background: #dbeafe;
            color: #2563eb;
        }
    </style>
</body>

</html>