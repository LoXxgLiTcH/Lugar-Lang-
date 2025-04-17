
import { saveDestination } from './utils/storage.js';

document.addEventListener("DOMContentLoaded", () => {
    const buttons = document.querySelectorAll(".set-destination-btn");
    const pinButtons = document.querySelectorAll('.pin-destination');

    buttons.forEach(button => {
        button.addEventListener("click", handleDestinationClick);
    });

    pinButtons.forEach(button => {
        button.addEventListener("click", handlePinClick);
    });
});

function handleDestinationClick(event) {
    const button = event.currentTarget;
    const card = button.closest(".campus-card");

    if (!card) return;

    const campusName = card.querySelector(".campus-name")?.textContent.trim();
    const campusLocation = card.querySelector(".campus-location")?.textContent.trim();

    if (!campusName || !campusLocation) {
        console.error("Missing campus data.");
        return;
    }

    const destinationData = { name: campusName, location: campusLocation };
    saveDestination(destinationData);

    alert(`Destination set: ${campusName}`);

    // Uncomment the following line if you want to redirect after setting the destination
    // window.location.href = "map.php";
}

function handlePinClick(event) {
    const button = event.currentTarget;
    const campus = button.getAttribute('data-campus');

    setDefaultDestination(campus);

    // Update UI to show active pin
    document.querySelectorAll('.pin-destination').forEach(btn => btn.classList.remove('active'));
    button.classList.add('active');
}

function setDefaultDestination(campus) {
    const formData = new FormData();
    formData.append('set_default_campus', true);
    formData.append('campus', campus);

    fetch('choose-campus.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        showNotification(data.message, data.success);

        if (data.success) {
            setTimeout(() => {
                window.location.href = 'home.php';
            }, 2000);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred. Please try again.', false);
    });
}

function showNotification(message, success = true) {
    const notification = document.getElementById('notification');
    const notificationMessage = document.getElementById('notification-message');

    notificationMessage.textContent = message;
    notification.style.backgroundColor = success ? 'var(--accent-green)' : 'var(--error)';

    notification.classList.add('show');

    setTimeout(() => {
        notification.classList.remove('show');
    }, 5000);
}
