// Regex patterns from the lab Appendix
function validateRegisterForm(e) {
    const name = document.getElementById('customer_name').value;
    const email = document.getElementById('customer_email').value;
    const pass = document.getElementById('customer_pass').value;
    const contact = document.getElementById('customer_contact').value;

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const phoneRegex = /^[0-9+\-\s]{7,15}$/;
    const passRegex = /^(?=.*\d).{8,}$/;

    const emailOK = emailRegex.test(email);
    const phoneOK = phoneRegex.test(contact);
    const passOK = passRegex.test(pass);
    const nameOK = name.trim().length >= 2;

    if (!nameOK || !emailOK || !phoneOK || !passOK) {
        e.preventDefault(); // Stop form from submitting
        
        // Simple alert for now, you can improve this with inline messages
        let errorMsg = "Please fix the following errors:\n";
        if (!nameOK) errorMsg += "- Name must be at least 2 characters.\n";
        if (!emailOK) errorMsg += "- Invalid email format.\n";
        if (!passOK) errorMsg += "- Password must be at least 8 characters with 1 digit.\n";
        if (!phoneOK) errorMsg += "- Phone must be 7-15 digits/spaces.\n";
        
        alert(errorMsg);
    }
}

const registerForm = document.getElementById('registerForm');
if (registerForm) {
    registerForm.addEventListener('submit', validateRegisterForm);
}
