import { saveDestination } from './utils/storage.js';


document.addEventListener("DOMContentLoaded", () => {
    const buttons = document.querySelectorAll(".set-destination-btn");

    buttons.forEach(button => {
        button.addEventListener("click", () => {
            const card = button.closest(".campus-card");
            const campusName = card.querySelector(".campus-name").textContent;
            const campusLocation = card.querySelector(".campus-location").textContent;

            const destinationData = {
                name: campusName.trim(),
                location: campusLocation.trim()
            };

            localStorage.setItem("chosenDestination", JSON.stringify(destinationData));
            alert(`Destination set: ${destinationData.name}`);
            // Optional: Redirect to maps page or update iframe here if needed
            // window.location.href = "map.html";
        });
    });
});
