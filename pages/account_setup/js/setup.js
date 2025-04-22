const uploadBtn = document.getElementById("uploadBtn");
const photoInput = document.getElementById("photoInput");
const photoPreview = document.getElementById("photoPreview");

uploadBtn.addEventListener("click", function (event) {
  photoInput.click();
});

photoInput.addEventListener("change", function () {
  const file = this.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = function (e) {
      photoPreview.innerHTML = "";
      photoPreview.innerHTML = `<img src="${e.target.result}" alt="Profile Photo">`;
    };
    reader.readAsDataURL(file);
  }
});

document
  .getElementById("setupForm")
  .addEventListener("submit", function (event) {
    const username = document.getElementById("username").value;
    const role = document.getElementById("role").value;

    let isValid = true;
    let errorMessage = "";

    if (!username) {
      event.preventDefault();
      errorMessage += "Username is required.\n";
      isValid = false;
    }

    if (!role) {
      event.preventDefault();
      errorMessage += "Please select your role.\n";
      isValid = false;
    }

    if (!isValid) {
      alert(errorMessage);
    }
  });

function completeSetup() {
  const setupContainer = document.getElementById("setupContainer");
  const overlayContainer = document.getElementById("overlayContainer");
  const pageBackground = document.getElementById("pageContainer");

  setupContainer.classList.add("slide-down");

  pageBackground.style.filter = "blur(0px)";

  setTimeout(function () {
    overlayContainer.style.opacity = "0";

    setTimeout(function () {
      overlayContainer.style.display = "none";
    }, 800);
  }, 300);
}

// For testing purposes (comment out or remove in production)
// setTimeout(completeSetup, 3000);
