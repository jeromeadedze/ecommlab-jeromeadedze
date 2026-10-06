// Cascading Cities Data
const citiesByCountry = {
    "Ghana": ["Accra", "Kumasi", "Tamale", "Takoradi", "Cape Coast", "Tema", "Sunyani", "Koforidua"],
    "Nigeria": ["Lagos", "Abuja", "Port Harcourt", "Ibadan", "Kano", "Enugu", "Benin City"],
    "United States": ["New York", "Los Angeles", "Chicago", "Houston", "Miami", "San Francisco", "Atlanta"],
    "United Kingdom": ["London", "Manchester", "Birmingham", "Edinburgh", "Glasgow", "Liverpool"],
    "Kenya": ["Nairobi", "Mombasa", "Kisumu", "Nakuru", "Eldoret"],
    "South Africa": ["Johannesburg", "Cape Town", "Durban", "Pretoria", "Port Elizabeth"],
    "Canada": ["Toronto", "Vancouver", "Montreal", "Calgary", "Ottawa"]
};

// Update City dropdown when Country changes
const countrySelect = document.getElementById('customer_country');
const citySelect = document.getElementById('customer_city');

if (countrySelect && citySelect) {
    countrySelect.addEventListener('change', function () {
        const country = this.value;
        citySelect.innerHTML = '<option value="">-- Select City --</option>';

        if (citiesByCountry[country]) {
            citiesByCountry[country].forEach(city => {
                const opt = document.createElement('option');
                opt.value = city;
                opt.textContent = city;
                citySelect.appendChild(opt);
            });
        }
        hideError('country_error', countrySelect);
    });
}

function showError(elemId, inputElem, message) {
    const errorElem = document.getElementById(elemId);
    if (errorElem) {
        errorElem.textContent = message;
        errorElem.style.display = 'block';
    }
    if (inputElem) {
        inputElem.classList.add('is-invalid');
    }
}

function hideError(elemId, inputElem) {
    const errorElem = document.getElementById(elemId);
    if (errorElem) {
        errorElem.textContent = '';
        errorElem.style.display = 'none';
    }
    if (inputElem) {
        inputElem.classList.remove('is-invalid');
    }
}

// Live validation on input
['customer_name', 'customer_email', 'customer_pass', 'customer_contact', 'customer_country', 'customer_city'].forEach(id => {
    const el = document.getElementById(id);
    if (el) {
        el.addEventListener('input', () => hideError(id.replace('customer_', '') + '_error', el));
        el.addEventListener('change', () => hideError(id.replace('customer_', '') + '_error', el));
    }
});

function validateRegisterForm(e) {
    let isValid = true;

    const nameInput = document.getElementById('customer_name');
    const emailInput = document.getElementById('customer_email');
    const passInput = document.getElementById('customer_pass');
    const contactInput = document.getElementById('customer_contact');
    const countryInput = document.getElementById('customer_country');
    const cityInput = document.getElementById('customer_city');

    const name = nameInput.value.trim();
    const email = emailInput.value.trim();
    const pass = passInput.value;
    const contact = contactInput.value.trim();
    const country = countryInput.value;
    const city = cityInput.value;

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const phoneRegex = /^[0-9+\-\s]{7,15}$/;
    const passRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

    // Name validation
    if (name.length < 2) {
        showError('name_error', nameInput, 'Name must be at least 2 characters.');
        isValid = false;
    } else {
        hideError('name_error', nameInput);
    }

    // Email validation
    if (!emailRegex.test(email)) {
        showError('email_error', emailInput, 'Enter a valid email address.');
        isValid = false;
    } else {
        hideError('email_error', emailInput);
    }

    // Password validation (Concise message below the field)
    if (!passRegex.test(pass)) {
        showError('pass_error', passInput, 'Must be 8+ chars with uppercase, lowercase, digit & symbol (@$!%*?&).');
        isValid = false;
    } else {
        hideError('pass_error', passInput);
    }

    // Phone validation
    if (!phoneRegex.test(contact)) {
        showError('contact_error', contactInput, 'Enter a valid phone number (7-15 digits).');
        isValid = false;
    } else {
        hideError('contact_error', contactInput);
    }

    // Country validation
    if (!country) {
        showError('country_error', countryInput, 'Please select a country.');
        isValid = false;
    } else {
        hideError('country_error', countryInput);
    }

    // City validation
    if (!city) {
        showError('city_error', cityInput, 'Please select a city.');
        isValid = false;
    } else {
        hideError('city_error', cityInput);
    }

    if (!isValid) {
        e.preventDefault(); // Stop submission
    }
}

const registerForm = document.getElementById('registerForm');
if (registerForm) {
    registerForm.addEventListener('submit', validateRegisterForm);
}
