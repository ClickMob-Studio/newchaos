<?php
require "header.php";
?>

<div class="gradient-settings-container">
    <div class="gradient-settings">
        <h3>Customize Your Username Gradient</h3>
        <input type="hidden" id="username" placeholder="Enter your username" />

        <label for="startColor">Start Color:</label>
        <input type="color" id="startColor" value="#FF0000" />

        <label for="endColor">End Color:</label>
        <input type="color" id="endColor" value="#0000FF" />

        <label for="bold">Bold:</label>
        <input type="checkbox" id="bold" />

        <label for="italic">Italic:</label>
        <input type="checkbox" id="italic" />

        <label for="glow">Glow Effect:</label>
        <input type="checkbox" id="glow" />

        <button class="apply-btn" onclick="applySettings(); saveGradientSettings()">Apply Gradient</button>
    </div>

    <div id="preview" class="preview">
        <h3>Preview:</h3>
        <div id="gradientPreview" class="preview-text"></div>
    </div>

    <!-- Success Message -->
    <div id="successMessage" class="alert alert-success" style="display:none;">
        Gradient settings saved successfully!
    </div>
</div>

<script>
    // Apply user settings and preview the result
    function applySettings() {
        const username = document.getElementById("username").value;
        const startColor = document.getElementById("startColor").value;
        const endColor = document.getElementById("endColor").value;
        const isBold = document.getElementById("bold").checked ? "bold" : "normal";
        const isItalic = document.getElementById("italic").checked ? "italic" : "normal";
        const glow = document.getElementById("glow").checked;

        // Generate gradient name and apply styles
        const gradientText = generateGradientName(startColor, endColor, username, glow);

        // Apply the styles to the preview element
        const previewElement = document.getElementById("gradientPreview");
        previewElement.innerHTML = gradientText; // Apply the gradient HTML
        previewElement.style.fontWeight = isBold;
        previewElement.style.fontStyle = isItalic;
    }

    function saveGradientSettings() {
        const user_id = <?php echo $_SESSION['id']; ?>;  // Get the user_id from the session
        const startColor = document.getElementById("startColor").value;
        const endColor = document.getElementById("endColor").value;
        const isBold = document.getElementById("bold").checked ? 'true' : 'false';
        const isItalic = document.getElementById("italic").checked ? 'true' : 'false';
        const glow = document.getElementById("glow").checked ? 'true' : 'false';

        const xhr = new XMLHttpRequest();
        xhr.open("POST", "saveGradient.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        // Prepare the data to be sent to the server
        const params = `user_id=${encodeURIComponent(user_id)}&startColor=${encodeURIComponent(startColor)}&endColor=${encodeURIComponent(endColor)}&bold=${encodeURIComponent(isBold)}&italic=${encodeURIComponent(isItalic)}&glow=${encodeURIComponent(glow)}`;

        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                // Display the success message on the page
                const successMessage = document.getElementById("successMessage");
                successMessage.style.display = 'block';

                // Hide the success message after 5 seconds
                setTimeout(() => {
                    successMessage.style.display = 'none';
                }, 5000);
            }
        };
        xhr.send(params);
    }

    // Function to generate the gradient for the username
    function generateGradientName(startColor, endColor, username, glow) {
        const gradientColors = generateGradient(startColor, endColor, username.length);
        let gradientText = "";

        for (let i = 0; i < username.length; i++) {
            gradientText += `<span style="color: ${gradientColors[i]};${glow ? `text-shadow: 0 0 10px ${gradientColors[i]}` : ''}">${username[i]}</span>`;
        }

        return gradientText;
    }

    // Function to generate a gradient between two colors for each letter
    function generateGradient(startColor, endColor, length) {
        let start = hexToRgb(startColor);
        let end = hexToRgb(endColor);

        let gradientColors = [];
        let stepR = (end.r - start.r) / (length - 1);
        let stepG = (end.g - start.g) / (length - 1);
        let stepB = (end.b - start.b) / (length - 1);

        for (let i = 0; i < length; i++) {
            let r = Math.round(start.r + stepR * i);
            let g = Math.round(start.g + stepG * i);
            let b = Math.round(start.b + stepB * i);

            gradientColors.push(rgbToHex(r, g, b));
        }

        return gradientColors;
    }

    // Helper functions for color conversion
    function hexToRgb(hex) {
        let r = parseInt(hex.slice(1, 3), 16);
        let g = parseInt(hex.slice(3, 5), 16);
        let b = parseInt(hex.slice(5, 7), 16);
        
        return { r, g, b };
    }

    function rgbToHex(r, g, b) {
        return "#" + (1 << 24 | r << 16 | g << 8 | b).toString(16).slice(1).toUpperCase();
    }
</script>

