<?php
include 'header.php';

// Start the session
session_start();

if (isset($_GET['buy'])) {
    $selected_pack = $_GET['buy'];

    if ($selected_pack === "FirstSet1" && $user_class->id >= 0) {
        $db->query("UPDATE grpgusers SET pack1 = 1, pack1time = 100 WHERE id = ?");
        $db->execute(array(
            $user_class->id
        ));
        $message = "You Changed to Pack1.";
    } elseif ($selected_pack === "FirstSet2" && $user_class->id >= 0) {
        $db->query("UPDATE grpgusers SET pack1 = 2, pack1time = 100 WHERE id = ?");
        $db->execute(array(
            $user_class->id
        ));
        $message = "You Changed to Pack2.";
    } elseif ($selected_pack === "FirstSet3" && $user_class->id >= 0) {
        $db->query("UPDATE grpgusers SET pack1 = 3, pack1time = 100 WHERE id = ?");
        $db->execute(array(
            $user_class->id
        ));
        $message = "You Changed to Pack3.";
    } elseif ($selected_pack === "FirstSet4" && $user_class->id >= 0) {
        $db->query("UPDATE grpgusers SET pack1 = 4, pack1time = 100 WHERE id = ?");
        $db->execute(array(
            $user_class->id
        ));
        $message = "You Changed to Pack4.";
    } elseif ($selected_pack === "FirstSet5" && $user_class->id >= 0) {
        $db->query("UPDATE grpgusers SET pack1 = 5, pack1time = 100 WHERE id = ?");
        $db->execute(array(
            $user_class->id
        ));
        $message = "You Changed to Pack5.";
    } else {
        $message = "You Are unable to change.";
    }
}

// Set the last clicked image index in the session
if (isset($_GET['image'])) {
    $_SESSION['last_clicked_image'] = $_GET['image'];
}
?>

<style>
  /* Set initial filter to grayscale (100%) for all images */
  img {
    filter: grayscale(100%);
    transition: filter 0.3s ease; /* Add a smooth transition effect */
  }

  /* Remove the grayscale filter when the image is clicked */
  img.active {
    filter: none;
  }

  /* Tooltip styles */
  .tooltip {
    position: absolute;
    display: none;
    width: 120px;
    background-color: #333;
    color: #fff;
    text-align: center;
    border: 1px solid #000; /* 1px black border */
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.5); /* Black shadow */
    border-radius: 5px;
    padding: 5px;
    z-index: 999; /* Increase the z-index to ensure the tooltip appears above other elements */
  }

  /* Image containers to display images side by side */
  .image-container {
    display: inline-block;
    margin: 0 10px; /* Adjust this value to change the spacing between images */
    position: relative;
  }

  /* Reset margins for first and last images */
  .image-container:first-child {
    margin-left: 0;
  }

  .image-container:last-child {
    margin-right: 0;
  }

  /* Center the images within the containers */
  .image-container img {
    display: block;
    margin: 0 auto;
  }
</style>

<h3>Prayer</h3>
<hr>
<table style='width: 100%; border-collapse: collapse;'>
<?php
echo "<tr><td colspan='5' style='text-align: center; font-weight: bold;'>Blessing from Apollo</td></tr>";
echo "<tr><td colspan='5' style='text-align: center; padding: 10px;'>You will become mightier than you ever dreamed of if you choose to make a pact with either the earthly or divine powers! But you will always have to keep in mind that you can only activate one pact from each category. We wouldn't want the gods to argue about whose favorite you are; this would cause terrible chaos.</td></tr>";
echo "<tr>";
echo "<td style='text-align: center;'>";

// Assuming $user_class->pack1 holds the user's selected pack number (1 to 5)
$user_pack = $user_class->pack1;

// Create an array of image paths for each pack
$image_paths = array(
    "/images/ml2thing.jpg",
    "/images/ml2thing.jpg",
    "/images/ml2thing.jpg",
    "/images/ml2thing.jpg",
    "/images/ml2thing.jpg"
);

// Create an array of tooltip texts for each pack
$tooltip_texts = array(
    "Tooltip Text 1",
    "Tooltip Text 2",
    "Tooltip Text 3",
    "Tooltip Text 4",
    "Tooltip Text 5"
);

for ($i = 0; $i < 5; $i++) {
    echo "<div class=\"image-container\">";
    // Determine if the image should remain in color based on the last clicked image index
    $isLastClicked = (isset($_SESSION['last_clicked_image']) && $_SESSION['last_clicked_image'] == $i);
    echo "<a href='gamething.php?buy=FirstSet" . ($i + 1) . "&image=" . $i . "'><img src=\"" . $image_paths[$i] . "\" width='100' height='100' alt='test' " . ($isLastClicked ? "class='active'" : "") . " /></a>";
    echo "<div class=\"tooltip\" id=\"tooltip" . ($i + 1) . "\">" . $tooltip_texts[$i] . "</div>";
    echo "</div>";
}

echo "</td>";
echo "</tr>";
echo "</table>";

// Display the notification message if set
if (isset($message)) {
    echo "<div id=\"notification\">" . $message . "</div>";
}
?>

<script>
  // Function to show tooltip
 function showTooltip(event, tooltip) {
    const scrollX = window.pageXOffset || document.documentElement.scrollLeft;
    const scrollY = window.pageYOffset || document.documentElement.scrollTop;

    tooltip.style.left = event.pageX + "px";
    tooltip.style.top = event.pageY + "px";
    tooltip.style.display = "block";
  }
  // Function to hide tooltip
  function hideTooltip(tooltip) {
    tooltip.style.display = "none";
  }

  // Function to handle image hover effect
  function handleImageHover(img) {
    const lastClickedImage = <?php echo isset($_SESSION['last_clicked_image']) ? $_SESSION['last_clicked_image'] : -1; ?>;
    if (lastClickedImage !== -1) {
      const images = document.querySelectorAll(".image-container img");
      images.forEach((imgElement, index) => {
        if (index !== lastClickedImage) {
          imgElement.style.filter = "grayscale(100%)";
        }
      });
    }
    img.style.filter = "none";
  }

  // Function to handle AJAX call
  function handleImageClick(pack, img) {
    fetch("gamething.php?buy=FirstSet" + (pack + 1) + "&image=" + pack)
      .then((response) => response.json())
      .then((data) => {
        if (data.message) {
          const notification = document.getElementById("notification");
          notification.textContent = data.message;
          notification.style.display = "block";

          // Hide notification after a few seconds (adjust as needed)
          setTimeout(() => {
            notification.textContent = "";
            notification.style.display = "none";
          }, 3000); // 3000 milliseconds = 3 seconds
        }

        if (data.pack) {
          const images = document.querySelectorAll(".image-container img");
          images.forEach((imgElement, index) => {
            if (index === pack) {
              imgElement.style.filter = "none";
            } else {
              imgElement.style.filter = "grayscale(100%)";
            }
          });
        }
      })
      .catch((error) => console.log(error));
  }

  // Add event listeners to images
  document.addEventListener("DOMContentLoaded", () => {
    const images = document.querySelectorAll(".image-container img");
    images.forEach((img, index) => {
      img.addEventListener("click", () => {
        handleImageClick(index, img);
      });
      img.addEventListener("mouseover", (event) => {
        showTooltip(event, document.getElementById("tooltip" + (index + 1)));
        handleImageHover(img);
      });
      img.addEventListener("mousemove", (event) => {
        showTooltip(event, document.getElementById("tooltip" + (index + 1)));
      });
      img.addEventListener("mouseout", () => {
        hideTooltip(document.getElementById("tooltip" + (index + 1)));
        handleImageHover(img);
      });
    });
  });

document.querySelectorAll('.image-container img').forEach((imgElement, index) => {
    const tooltip = document.getElementById("tooltip" + (index + 1));
    
    imgElement.addEventListener('mouseenter', (event) => {
        showTooltip(event, tooltip);
    });
    
    imgElement.addEventListener('mouseleave', () => {
        hideTooltip(tooltip);
    });
});

</script>
