
import { isEmailValid, isNotEmpty } from '../../js/utils/validate.js';

document.getElementById("loginForm").addEventListener("submit", function(event) {
    event.preventDefault();
    
    const email = document.getElementById("email").value;
    const password = document.getElementById("password").value;

    document.getElementById("emailError").textContent = "";
    document.getElementById("passwordError").textContent = "";

    let isValid = true;

    if (!isEmailValid(email)) {
        document.getElementById("emailError").textContent = "Please enter a valid email address.";
        isValid = false;
    }

    if (!isNotEmpty(password)) {
        document.getElementById("passwordError").textContent = "Password cannot be empty.";
        isValid = false;
    }

    if (isValid) {
        this.submit(); // Submit the form if validation passes
    }
});