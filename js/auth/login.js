
import { isEmailValid, isNotEmpty } from './utils/validate.js';

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

    
    if (!isNotEmpty(password) || password.length < 8) {
        document.getElementById("passwordError").textContent = "Password must be at least 8 characters.";
        isValid = false;
    }

    
    if (isValid) {
        alert("Logged in successfully!");
        document.getElementById("loginForm").reset();
    }
});
