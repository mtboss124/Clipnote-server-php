<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mockup</title>
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
            background: url(/theater/img/bg.png) repeat; /* Repeat the background image */
       
            animation: scrollBackground 40s linear infinite; /* Apply animation */
            image-rendering: pixelated; /* Enable pixelated rendering */
        }

    </style>
</head>
<body>
    <div class="mockup-container"> 
    
        <img src="/theater/img/logo.png" alt="Logo" style="position: absolute; top: 0px; left: 25px;animation: logointro 1s ease-out;">
  
        <img src="/theater/img/bc.png" alt="Browse clipnotes" style="position: absolute; top: 174px; left: 56px; " onclick="playSoundAndRedirect('/theater/img/bc.png', '/theater/browseclipnotes.php'); ">
     
        <img src="/theater/img/log.png" alt="Login" style="position: absolute; top: 217px; left: 56px;" onclick="playSoundAndRedirect('/theater/img/log.png', 'login_page_url');">
        
        <img src="/theater/img/reg.png" alt="Register" style="position: absolute; top: 217px; left: 121px;" onclick="playSoundAndRedirect('/theater/img/reg.png', 'register_page_url');">
       
        <img src="/theater/img/cred.png" alt="Credits rules" style="position: absolute; top: 217px; left: 192px;" onclick="playSoundAndRedirect('/theater/img/cred.png', 'credits_page_url');">
    </div>
    <audio id="button-sound" src="/theater/sound/button.mp3"></audio>
    <audio id="intro-sound" src="/theater/sound/splash.mp3"></audio>
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
            const sound = document.getElementById('intro-sound');
            sound.play();
            const container = document.querySelector('.mockup-container');
            container.style.transform = 'scale(3)';
            container.style.transition = 'transform 0.5s ease';
            container.style.position = 'fixed';
            container.style.top = '50%';
            container.style.left = '50%';
            container.style.transformOrigin = 'center center';
            container.style.marginTop = `-${container.offsetHeight / 2}px`;
            container.style.marginLeft = `-${container.offsetWidth / 2}px`;
            // Set pixelated rendering for images
            const images = container.querySelectorAll('img');
            images.forEach(img => {
                img.style.imageRendering = 'pixelated';
            });
        }
        window.onload = zoomIn; // Automatically zoom in on page load
    </script>
</body>
</html>
