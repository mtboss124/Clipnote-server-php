<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clipnote!</title>
    <style>
        @keyframes scrollBackground {
            0% {
                background-position: 0 0;
            }
            100% {
                background-position: 100% 100%; /* Adjust as needed for the desired effect */
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

    </style>
</head>
<body>
    <script>
            const sound = document.getElementById('intro-sound');
            sound.play();
    </script>
    <div class="mockup-container"> 
    
        <img src="img/logo.png" alt="Logo" style="position: absolute; top: 0px; left: 25px;animation: logointro 1s ease-out;">
  
        <img src="img/bc.png" alt="Browse clipnotes" style="position: absolute; top: 174px; left: 56px; " onclick="playSoundAndRedirect('img/bc.png', 'browseclipnotes.php'); ">
     
        <img src="img/log.png" alt="Login" style="position: absolute; top: 217px; left: 56px;" onclick="playSoundAndRedirect('img/log.png', 'login.html');">
        
        <img src="img/reg.png" alt="Register" style="position: absolute; top: 217px; left: 121px;" onclick="playSoundAndRedirect('img/reg.png', 'register.html');">
       
        <img src="img/cred.png" alt="Credits rules" style="position: absolute; top: 217px; left: 192px;" onclick="playSoundAndRedirect('img/cred.png', 'credits[rules.php');">
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
    </script>
<script>
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
