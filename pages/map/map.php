
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lugar Lang!</title>
    <link rel="stylesheet" href="styles/map.css">
   
</head>
<body>
    <div class="map-container">
        <h1>Lugar Lang</h1>
        <form id="mapForm" method="POST">
            <label for="from">From</label>
            <input type="text" name="from" id="from" placeholder="Your current location">

            <label for="to">To</label>
            <input type="text" name="to" id="to" value="<?= htmlspecialchars($defaultCampus) ?>" placeholder="Destination">

            <button type="submit">Lugar Lang, kuya!</button>
        </form>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
        
            const form = document.getElementById("mapForm");
            form.addEventListener("submit", (e) => {
                e.preventDefault();
                const from = document.getElementById("from").value;
                const to = document.getElementById("to").value;

          
                console.log("Calculating route from:", from, "to:", to);
            });
        });
    </script>
</body>
</html>
