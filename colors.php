<div class="gradient-settings">
    <label for="username">Username:</label>
    <input type="text" id="username" placeholder="Enter your username" />

    <label for="startColor">Start Color:</label>
    <input type="color" id="startColor" value="#FF0000" />

    <label for="endColor">End Color:</label>
    <input type="color" id="endColor" value="#0000FF" />

    <label for="fontSize">Font Size:</label>
    <input type="number" id="fontSize" value="20" />

    <label for="bold">Bold:</label>
    <input type="checkbox" id="bold" />

    <label for="italic">Italic:</label>
    <input type="checkbox" id="italic" />

    <label for="letterSpacing">Letter Spacing:</label>
    <input type="number" id="letterSpacing" value="0" />

    <button onclick="applySettings()">Apply Gradient</button>
</div>

<div id="preview">
    <h3>Preview:</h3>
    <div id="gradientPreview"></div>
</div>
<script>
    function applySettings() {
    const username = document.getElementById("username").value;
    const startColor = document.getElementById("startColor").value;
    const endColor = document.getElementById("endColor").value;
    const fontSize = document.getElementById("fontSize").value + "px";
    const isBold = document.getElementById("bold").checked ? "bold" : "normal";
    const isItalic = document.getElementById("italic").checked ? "italic" : "normal";
    const letterSpacing = document.getElementById("letterSpacing").value + "px";

    // Create gradient name
    const gradientName = generateGradientName(startColor, endColor, username);

    // Apply styles dynamically
    const styles = `font-size: ${fontSize}; font-weight: ${isBold}; font-style: ${isItalic}; letter-spacing: ${letterSpacing};`;
    
    const previewElement = document.getElementById("gradientPreview");
    previewElement.innerHTML = `<span style="color: ${gradientName.color}; ${styles}">${gradientName.username}</span>`;
}

// Function to generate a gradient for the username
function generateGradientName(startColor, endColor, username) {
    const gradient = generateGradient(startColor, endColor, username.length);

    let gradientText = "";
    for (let i = 0; i < username.length; i++) {
        gradientText += `<span style="color: ${gradient[i]}">${username[i]}</span>`;
    }

    return {
        color: gradientText,
        username: username
    };
}

// Gradient generation function
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