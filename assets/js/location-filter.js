/**
 * Location Filter - Cascading Dropdowns for City/District/Neighborhood
 * Works on all pages: homepage, listings, contact form, and admin panel
 */
document.addEventListener('DOMContentLoaded', function () {
    // Try to find selects for all pages
    const citySelect = document.getElementById('city-select') ||
        document.getElementById('city-select-home') ||
        document.getElementById('city-select-contact') ||
        document.getElementById('city-select-admin') ||
        document.getElementById('citySelect');
    const districtSelect = document.getElementById('district-select') ||
        document.getElementById('district-select-home') ||
        document.getElementById('district-select-contact') ||
        document.getElementById('district-select-admin') ||
        document.getElementById('districtSelect');
    const mahalleSelect = document.getElementById('mahalle-select') ||
        document.getElementById('mahalle-select-admin') ||
        document.getElementById('neighborhoodSelect');

    if (!citySelect || !districtSelect) {
        return; // Not on a page with location filters
    }

    // Determine API path based on current page
    const pathParts = window.location.pathname.split('/');
    // If the last part has a filename and there are at least two parts before it (pointing to a subfolder)
    // Or just check if we are in a subfolder of the root
    const isInSubfolder = pathParts.length > 2 && pathParts[pathParts.length - 2] !== '';
    const apiPath = isInSubfolder ? '../api/get_locations.php' : 'api/get_locations.php';

    // When city changes, load districts
    citySelect.addEventListener('change', function () {
        const selectedOption = this.options[this.selectedIndex];
        const cityId = selectedOption.getAttribute('data-id');

        // Reset district and mahalle
        districtSelect.innerHTML = '<option value="">' + (document.documentElement.lang === 'tr' ? 'İlçe Seçiniz' : 'Select District') + '</option>';
        if (mahalleSelect) {
            mahalleSelect.innerHTML = '<option value="">' + (document.documentElement.lang === 'tr' ? 'Mahalle Seçiniz' : 'Select Neighborhood') + '</option>';
        }

        if (cityId) {
            // Load districts
            fetch(`${apiPath}?action=get_districts&city_id=${cityId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data) {
                        data.data.forEach(district => {
                            const option = document.createElement('option');
                            option.value = district.ilce_adi;
                            option.textContent = district.ilce_adi;
                            option.setAttribute('data-id', district.id);
                            districtSelect.appendChild(option);
                        });
                    }
                })
                .catch(error => console.error('Error loading districts:', error));
        }
    });

    // When district changes, load neighborhoods (only if mahalle select exists)
    if (mahalleSelect) {
        districtSelect.addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            const districtId = selectedOption.getAttribute('data-id');

            // Reset mahalle
            mahalleSelect.innerHTML = '<option value="">' + (document.documentElement.lang === 'tr' ? 'Mahalle Seçiniz' : 'Select Neighborhood') + '</option>';

            if (districtId) {
                // Load neighborhoods
                fetch(`${apiPath}?action=get_neighborhoods&district_id=${districtId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.data) {
                            data.data.forEach(neighborhood => {
                                const option = document.createElement('option');
                                option.value = neighborhood.mahalle_adi;
                                option.textContent = neighborhood.mahalle_adi;
                                mahalleSelect.appendChild(option);
                            });
                        }
                    })
                    .catch(error => console.error('Error loading neighborhoods:', error));
            }
        });
    }
});
