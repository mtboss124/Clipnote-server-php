<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mockup</title>
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
                background-position: 100% 100%; /* Adjust as needed for the desired effect */
            }
        }
        @keyframes scrollridge {
            0% {
                background-position: 100% 0;
            }
            100% {
                background-position: 100% 33px; /* Adjust as needed for the desired effect */
            }
        }
        @keyframes logointro {
            0% {
                top: -200px;
            }
            100% {
                top: 0px;
            }
        }

        .mockup-container {
            font-family: flipnote;
            width: 320px;
            height: 240px;
            max-width: 100%;
            max-height: 100%;
            position: relative;
            overflow: hidden;
            background: url(img/bg.png) repeat; /* Repeat the background image */
       
            animation: scrollBackground 40s linear infinite; /* Apply animation */
            image-rendering: pixelated; /* Enable pixelated rendering */
        }
        .soniclookingass {
            width: 320px;
            height: 240px;
            max-width: 100%;
            max-height: 100%;
            position: relative;
            overflow: hidden;
            background: url(img/ridge.png) repeat-y; /* Repeat the background image */
       
            animation: scrollridge 1s linear infinite; /* Apply animation */
            image-rendering: pixelated; /* Enable pixelated rendering */
        }
    </style>
    
</head>
<body>
    <div class="mockup-container"> 
        <img src="img/header.png" alt="header" style="position: absolute; top: 0px; left: 15px;">
        
        <div id="dropdown" class="dropdown" onclick="toggleDropdown()" style="position: absolute; top: 4px; left: 20px; z-index: 1;">
<div class="dropdown-button" style="background-color: white; border: 1px solid #8b7ffe; min-width: 78px; min-height:6px; padding: 2px 1px; color: #8b7ffe;"><span class="fuckingshit"style="position:fixed; top:0px;">latest clipnotes</span></div>




            <div class="dropdown-content" style="display: none; background-color: white; border: 1px solid #8b7ffe; min-width: 20px;">
<div class="dropdown-item" style="padding-left: 1px; color: #8b7ffe; background-color: white;" onclick="handleDropdownChange('latest clipnotes')">latest clipnotes</div>


<div class="dropdown-item" style="padding-left: 1px; color: #8b7ffe; background-color: white;" onclick="handleDropdownChange('oldest clipnotes')">oldest clipnotes</div>


<div class="dropdown-item" style="padding-left: 1px; color: #8b7ffe; background-color: white;" onclick="handleDropdownChange('voted clipnotes')">voted clipnotes</div>


            </div>
        </div>
        
        <img id="dropdown-image" src="img/barclose.png" alt="Dropdown State" style="position: absolute; top: 4px; left: 101px; z-index: 2;" onclick="toggleDropdown()">

        <div class="soniclookingass"></div>
  
        <img id="sinewave-cat" src="img/cat.png" alt="cat" style="position: absolute; top: 17px; left: 225px;" onclick="playSoundAndRedirect('img/log.png', 'login_page_url');">
        
        <img src="img/nowserving.png" alt="nowserving" style="position: absolute; top: 2px; left: 188px;" onclick="playSoundAndRedirect('img/reg.png', 'register_page_url');">
       
        <img src="img/footer.png" alt="footer" style="position: absolute; top: 223px; left: 15px;" onclick="playSoundAndRedirect('img/cred.png', 'credits_page_url');">
    </div>
    <audio id="button-sound" src="sound/button.mp3"></audio>
    <audio id="intro-sound" src="sound/splash.mp3"></audio>
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
container.style.transition = 'transform 0.5s ease';
container.style.position = 'fixed';
container.style.top = '50%';
container.style.left = '50%';
container.style.transformOrigin = 'center center';
container.style.marginTop = `-${(containerHeight / 2)}px`;
container.style.marginLeft = `-${(containerWidth / 2)}px`;

// Set pixelated rendering for images
const images = container.querySelectorAll('img');
images.forEach(img => {
    img.style.imageRendering = 'pixelated';
});
}

// Zoom in on load and resize
window.onload = zoomIn;
window.onresize = zoomIn;
    </script>
</body>
</html>
