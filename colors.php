<?php
require "header.php";
?>

<div class="gradient-settings">
    <label for="username">Username:</label>
    <input type="text" id="username" placeholder="Enter your username" />

    <label for="startColor">Start Color:</label>
    <input type="color" id="startColor" value="#FF0000" />

    <label for="endColor">End Color:</label>
    <input type="color" id="endColor" value="#0000FF" />

    <label for="bold">Bold:</label>
    <input type="checkbox" id="bold" />

    <label for="italic">Italic:</label>
    <input type="checkbox" id="italic" />

    <label for="glow">Glow:</label>
    <input type="checkbox" id="glow" />

    <button onclick="applySettings()">Apply Gradient</button>
</div>

<div id="preview">
    <h3>Preview:</h3>
    <div id="gradientPreview" style="font-size: 40px;"></div>
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

<?php
echo generateGradientName(1);
// Function to generate the gradient for the username
function generateGradientName($user_id) {
    // Fetch user settings from the database based on the user_id
    global $db;
    $db->query("SELECT start_color, end_color, is_bold, is_italic, glow, u.username 
                FROM user_gradients ug
                JOIN users u ON u.id = ug.user_id
                WHERE ug.user_id = ?");
    $db->execute([$user_id]);
    $settings = $db->fetch_row(true);

    // If settings are found, generate the gradient text
    if ($settings) {
        $startColor = $settings['start_color'];
        $endColor = $settings['end_color'];
        $username = $settings['username'];
        $isBold = $settings['is_bold'];
        $isItalic = $settings['is_italic'];
        $glow = $settings['glow'];

        // Generate the gradient colors for each letter of the username
        $gradientColors = generateGradient($startColor, $endColor, strlen($username));
        $gradientText = "";

        // Loop through each character of the username and apply the gradient
        for ($i = 0; $i < strlen($username); $i++) {
            $gradientText .= "<span style=\"color: {$gradientColors[$i]};" . 
                             ($glow ? " text-shadow: 0 0 10px {$gradientColors[$i]}" : '') . 
                             "; font-weight: " . ($isBold ? "bold" : "normal") . "; font-style: " . ($isItalic ? "italic" : "normal") . ";\">" . 
                             $username[$i] . "</span>";
        }

        return $gradientText;
    }

    return "<span>Default Username</span>";  // If no settings, return default username
}

// Function to generate a gradient between two colors for each letter
function generateGradient($startColor, $endColor, $length) {
    $start = hexToRgb($startColor);
    $end = hexToRgb($endColor);
    
    $gradientColors = [];
    $stepR = ($end['r'] - $start['r']) / ($length - 1);
    $stepG = ($end['g'] - $start['g']) / ($length - 1);
    $stepB = ($end['b'] - $start['b']) / ($length - 1);

    for ($i = 0; $i < $length; $i++) {
        $r = round($start['r'] + $stepR * $i);
        $g = round($start['g'] + $stepG * $i);
        $b = round($start['b'] + $stepB * $i);
        
        $gradientColors[] = rgbToHex($r, $g, $b);
    }

    return $gradientColors;
}

// Helper functions for color conversion
function hexToRgb($hex) {
    $r = hexdec(substr($hex, 1, 2));
    $g = hexdec(substr($hex, 3, 2));
    $b = hexdec(substr($hex, 5, 2));
    
    return ['r' => $r, 'g' => $g, 'b' => $b];
}

function rgbToHex($r, $g, $b) {
    return "#" . str_pad(dechex($r), 2, "0", STR_PAD_LEFT) . 
           str_pad(dechex($g), 2, "0", STR_PAD_LEFT) . 
           str_pad(dechex($b), 2, "0", STR_PAD_LEFT);
}