// assets/js/app.js
// JavaScript interactivity for Arunella System

document.addEventListener('DOMContentLoaded', function() {
    // 1. Dynamic fields in Registration page based on Selected Role
    const roleInputs = document.querySelectorAll('input[name="role"]');
    if (roleInputs.length > 0) {
        const farmerFields = document.getElementById('farmer-fields');
        const buyerFields = document.getElementById('buyer-fields');
        const transporterFields = document.getElementById('transporter-fields');

        function toggleRoleFields(role) {
            // Hide all first
            if (farmerFields) farmerFields.style.display = 'none';
            if (buyerFields) buyerFields.style.display = 'none';
            if (transporterFields) transporterFields.style.display = 'none';

            // Disable all inner inputs to prevent them from being sent or validated on server
            disableInputs(farmerFields);
            disableInputs(buyerFields);
            disableInputs(transporterFields);

            // Show selected and enable inputs
            if (role === 'Farmer' && farmerFields) {
                farmerFields.style.display = 'block';
                enableInputs(farmerFields);
            } else if (role === 'Buyer' && buyerFields) {
                buyerFields.style.display = 'block';
                enableInputs(buyerFields);
            } else if (role === 'Transporter' && transporterFields) {
                transporterFields.style.display = 'block';
                enableInputs(transporterFields);
            }
        }

        function disableInputs(container) {
            if (!container) return;
            const inputs = container.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                input.disabled = true;
                input.removeAttribute('required');
            });
        }

        function enableInputs(container) {
            if (!container) return;
            const inputs = container.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                input.disabled = false;
                // Add required attribute back for fields that shouldn't be empty
                if (!input.classList.contains('optional')) {
                    input.setAttribute('required', 'required');
                }
            });
        }

        // Initialize on page load
        const checkedRole = document.querySelector('input[name="role"]:checked');
        if (checkedRole) {
            toggleRoleFields(checkedRole.value);
        }

        // Listen for changes
        roleInputs.forEach(input => {
            input.addEventListener('change', function() {
                toggleRoleFields(this.value);
            });
        });
    }

    // 2. Client-side Search Filter in Marketplace
    const searchInput = document.getElementById('market-search');
    const districtFilter = document.getElementById('market-district');
    const cropCards = document.querySelectorAll('.crop-card');

    if (searchInput || districtFilter) {
        function filterCrops() {
            const searchValue = searchInput ? searchInput.value.toLowerCase() : '';
            const districtValue = districtFilter ? districtFilter.value.toLowerCase() : '';

            cropCards.forEach(card => {
                const title = card.querySelector('h3').textContent.toLowerCase();
                const description = card.querySelector('.crop-desc').textContent.toLowerCase();
                const district = card.querySelector('.crop-location') ? card.querySelector('.crop-location').textContent.toLowerCase() : '';

                const matchesSearch = title.includes(searchValue) || description.includes(searchValue);
                const matchesDistrict = districtValue === '' || district.includes(districtValue);

                if (matchesSearch && matchesDistrict) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        if (searchInput) searchInput.addEventListener('input', filterCrops);
        if (districtFilter) districtFilter.addEventListener('change', filterCrops);
    }

    // 3. Auto-fade alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transition = 'opacity 0.6s ease';
            setTimeout(() => {
                alert.style.display = 'none';
            }, 600);
        }, 5000);
    });
});
