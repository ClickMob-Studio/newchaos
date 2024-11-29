<!-- Color Picker Inputs -->
<label for="startColor">Start Color:</label>
<input type="color" id="startColor" value="#FF0000">

<label for="endColor">End Color:</label>
<input type="color" id="endColor" value="#00FF00">

<!-- Username Input -->
<label for="username">Username:</label>
<input type="text" id="username" placeholder="Enter your username">

<!-- Preview Area -->
<div>
  <h3>Preview:</h3>
  <div id="previewText" style="font-size: 20px;">ExampleUser</div>
</div>

<!-- Submit Button -->
<button onclick="applyGradient()">Save Gradient</button>

<!-- Hidden Form for Backend Submission -->
<form id="gradientForm" method="POST" action="/saveGradient.php" style="display: none;">
  <input type="hidden" id="gradientStart" name="startColor">
  <input type="hidden" id="gradientEnd" name="endColor">
  <input type="hidden" id="gradientUsername" name="username">
  <button type="submit">Submit</button>
</form>
<script>
    // Function to apply the gradient and update the preview
function applyGradient() {
  var startColor = document.getElementById('startColor').value;
  var endColor = document.getElementById('endColor').value;
  var username = document.getElementById('username').value;

  // Create gradient effect using text_gradient function
  var gradientText = textGradient(startColor.substring(1), endColor.substring(1), 20, username);

  // Update preview
  document.getElementById('previewText').innerHTML = gradientText;

  // Set values to hidden inputs for backend submission
  document.getElementById('gradientStart').value = startColor;
  document.getElementById('gradientEnd').value = endColor;
  document.getElementById('gradientUsername').value = username;

  // Optionally, submit the form (if you want to save it directly)
  // document.getElementById('gradientForm').submit();
}

// Example of text_gradient function (as we discussed earlier)
function textGradient(startcol, endcol, fontsize, user) {
  var letters = user.split('');
  var graduations = letters.length - 1;
  var startcoln = {
    r: parseInt(startcol.substring(0, 2), 16),
    g: parseInt(startcol.substring(2, 4), 16),
    b: parseInt(startcol.substring(4, 6), 16)
  };
  var endcoln = {
    r: parseInt(endcol.substring(0, 2), 16),
    g: parseInt(endcol.substring(2, 4), 16),
    b: parseInt(endcol.substring(4, 6), 16)
  };
  var GSize = {
    r: (endcoln.r - startcoln.r) / graduations,
    g: (endcoln.g - startcoln.g) / graduations,
    b: (endcoln.b - startcoln.b) / graduations
  };

  var hexCol = [];
  for (var i = 0; i <= graduations; i++) {
    var HexR = Math.round(startcoln.r + (GSize.r * i)).toString(16).padStart(2, '0');
    var HexG = Math.round(startcoln.g + (GSize.g * i)).toString(16).padStart(2, '0');
    var HexB = Math.round(startcoln.b + (GSize.b * i)).toString(16).padStart(2, '0');
    hexCol.push(HexR + HexG + HexB);
  }

  var result = "";
  for (var i = 0; i < letters.length; i++) {
    result += "<span style='color:#" + hexCol[i] + "; font-size:" + fontsize + "px'>" + letters[i] + "</span>";
  }

  return result;
}

</script>