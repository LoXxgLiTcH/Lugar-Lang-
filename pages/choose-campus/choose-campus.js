import { saveDestination } from './utils/storage.js';

document.addEventListener("DOMContentLoaded", () => {
    const buttons = document.querySelectorAll(".set-destination-btn");

    buttons.forEach(button => {
        button.addEventListener("click", handleSetDestination);
    });
});

function handleSetDestination(event) {
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

    // window.location.href = "map.php";
}
