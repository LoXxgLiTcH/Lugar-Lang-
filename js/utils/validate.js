export { validateForm, isStrongPassword };

function isValidEmail(email) {
  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email) && email.endsWith("@usc.edu.ph");
}

function isStrongPassword(password) {
 
  return /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/.test(password);
}



export function validateForm() {
  let isValid = true;

  const nameInput = document.getElementById("fullName");
  const emailInput = document.getElementById("email");
  const passwordInput = document.getElementById("password");
  const confirmPasswordInput = document.getElementById("confirmPassword");

  const nameError = document.getElementById("fullNameError");
  const emailError = document.getElementById("emailError");
  const passwordError = document.getElementById("passwordError");
  const confirmPasswordError = document.getElementById("confirmPasswordError");

  nameError.textContent = "";
  emailError.textContent = "";
  passwordError.textContent = "";
  confirmPasswordError.textContent = "";

  if (nameInput.value.trim() === "") {
    nameError.textContent = "Name is required.";
    isValid = false;
  }

  const email = emailInput.value.trim();
  if (email === "") {
    emailError.textContent = "Email is required.";
    isValid = false;
  } else if (!isValidEmail(email)) {
    emailError.textContent = "Invalid email format.";
    isValid = false;
  }

  const password = passwordInput.value;
  const confirmPassword = confirmPasswordInput.value;

  if (password === "") {
    passwordError.textContent = "Password is required.";
    isValid = false;
  } else if (!isStrongPassword(password)) {
    passwordError.textContent = "Password must be at least 8 characters long and include uppercase, lowercase, number, and special character.";
    isValid = false;
  }

  if (confirmPassword !== password) {
    confirmPasswordError.textContent = "Passwords do not match.";
    isValid = false;
  }

  return isValid;
}
