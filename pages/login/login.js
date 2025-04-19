import { isNotEmpty, isEmailValid } from '../../js/utils/validate.js';

document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("loginForm");

    form.addEventListener("submit", function (event) {
        event.preventDefault();

        const email = document.getElementById("email").value.trim();
        const password = document.getElementById("password").value;

        const emailError = document.getElementById("emailError");
        const passwordError = document.getElementById("passwordError");

        
        emailError.textContent = "";
        passwordError.textContent = "";
        // emailError.classList.remove("visible");
        // passwordError.classList.remove("visible");

        let isValid = true;

        if (!isNotEmpty(email) || !isEmailValid(email)) {
            emailError.textContent = "Please enter a valid email address.";
            emailError.classList.add("visible");
            isValid = false;
        }

        if (!isNotEmpty(password)) {
            passwordError.textContent = "Password is required.";
            passwordError.classList.add("visible");
            isValid = false;
        }

        if (isValid) {
            form.submit(); 
        }
    });
});
