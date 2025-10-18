<?php
include __DIR__ . '/../theater/config/config.php';
$clipnote=isset($_GET['clipnote']) ? $_GET['clipnote'] : 'example1';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?=$api_url?></title>
    <style>
    @font-face {
        font-family: flipnote;
        src: url(img/fhf.ttf);
    }

    @keyframes scrollBackground {
        0% {
            background-position: 0 0;
        }

        100% {
            background-position: 100% 100%;
            /* Adjust as needed for the desired effect */
        }
    }

    .mockup-container {
        font-size: 12px;
        font-family: flipnote;
        width: 320px;
        height: 240px;
        max-width: 100%;
        max-height: 100%;
        position: relative;
 
        background: url(img/bg.png) repeat;
        /* Repeat the background image */

        animation: scrollBackground 40s linear infinite;
        /* Apply animation */
        image-rendering: pixelated;
        /* Enable pixelated rendering */
    }

    ;
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="<?=BASE_URI?>theater/clipnote.js"></script>
</head>

<body>
    <div class="mockup-container">
 <clipnote-player id="clipnote-player" url="<?=BASE_URI?>data/notes/<?=$clipnote?>.clip" width="320" height="240" style="position: fixed; top: 0px; left: 0px;"></clipnote-player>
    </div>

    <script>
    function playSoundAndRedirect(imageSrc, redirectUrl) {
        const sound = document.getElementById('button-sound');
        const img = document.querySelector(`img[src="${imageSrc}"]`);
        sound.play();
        img.style.filter = 'brightness(50%)'; // Darken the button
        // Reset the button's brightness after 1 second
        setTimeout(() => {
            img.style.filter = 'brightness(100%)'; // Reset to original state
        }, 300);
        sound.onended = () => {
            window.location.href = redirectUrl; // Redirect after sound ends
        };
    }

    function handleDropdownChange(selectedValue) {
        const dropdown = document.getElementById('dropdown');
        const dropdownButton = dropdown.querySelector('.fuckingshit');
        const dropdownContent = dropdown.querySelector('.dropdown-content');

        const dropdownImage = document.getElementById('dropdown-image');

        // Change image based on dropdown state
        if (selectedValue) {
            dropdownButton.textContent = selectedValue; // Update button text
            dropdownContent.style.display = 'none'; // Hide dropdown after selection

            dropdownImage.src = 'img/barclose.png'; // Change to closed image
        } else {
            dropdownImage.src = 'img/baropen.png'; // Change to open image
        }
    }

    // Function to toggle dropdown visibility
    function toggleDropdown() {
        const dropdown = document.getElementById('dropdown');
        const dropdownContent = dropdown.querySelector('.dropdown-content');
        const dropdownImage = document.getElementById('dropdown-image');

        if (dropdownContent.style.display === 'block') {
            dropdownContent.style.display = 'none'; // Hide dropdown
            dropdownImage.src = 'img/barclose.png'; // Change to open image
        } else {
            dropdownContent.style.display = 'block'; // Show dropdown
            dropdownImage.src = 'img/baropen.png'; // Change to closed image
        }
    }

    function changeSort(sortType) {
        window.location.href = `?page=1&sort=${sortType}`;
    }

    function zoomIn() {


        const container = document.querySelector('.mockup-container');

        // Reset any transforms before measuring
        container.style.transform = 'none';
        container.style.position = 'absolute';
        container.style.top = '0';
        container.style.left = '0';

        const containerWidth = container.offsetWidth;
        const containerHeight = container.offsetHeight;
        const screenWidth = window.innerWidth;
        const screenHeight = window.innerHeight;

        // Calculate scale to fit screen while preserving aspect ratio
        const scaleX = screenWidth / containerWidth;
        const scaleY = screenHeight / containerHeight;
        const scale = Math.min(scaleX, scaleY);

        // Apply styles
        container.style.transform = `scale(${scale})`;
        container.style.transition = 'transform 0s ease';
        container.style.position = 'fixed';
        container.style.top = '50%';
        container.style.left = '50%';
        container.style.transformOrigin = 'center center';
        container.style.marginTop = `-${(containerHeight / 2)}px`;
        container.style.marginLeft = `-${(containerWidth / 2)}px`;

        // Set pixelated rendering for images

    }

    // Zoom in on load and resize
    window.onload = zoomIn;
    window.onresize = zoomIn;
    </script>
</body>

</html>