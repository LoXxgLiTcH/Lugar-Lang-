
import { isEmailValid, isPasswordStrong, isNotEmpty } from '../../js/utils/validate.js';

document.getElementById("signupForm").addEventListener("submit", function(event) {
    event.preventDefault();
    
    const fullName = document.getElementById("fullName").value;
    const email = document.getElementById("email").value;
    const password = document.getElementById("password").value;
    const confirmPassword = document.getElementById("confirmPassword").value;

    document.getElementById("fullNameError").textContent = "";
    document.getElementById("emailError").textContent = "";
    document.getElementById("passwordError").textContent = "";
    document.getElementById("confirmPasswordError").textContent = "";

    let isValid = true;

    if (!isNotEmpty(fullName) || fullName.length < 2) {
        document.getElementById("fullNameError").textContent = "Name must be at least 2 characters.";
        isValid = false;
    }

    if (!isEmailValid(email)) {
        document.getElementById("emailError").textContent = "Please enter a valid email address.";
        isValid = false;
    }

    if (!isPasswordStrong(password)) {
        document.getElementById("passwordError").textContent = "Password must be at least 8 characters and include both letters and numbers.";
        isValid = false;
    }

    if (password !== confirmPassword) {
        document.getElementById("confirmPasswordError").textContent = "Passwords don't match.";
        isValid = false;
    }

    if (isValid) {
        alert("Account created successfully!");
        document.getElementById("signupForm").reset();
    }
});
