import { validateForm } from '../../js/utils/validate.js';

document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("signupForm");

    form.addEventListener("submit", (e) => {
        const isValid = validateForm();

        if (!isValid) {
            e.preventDefault(); // Prevent form submission if validation fails
        }
    });
});
