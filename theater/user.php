<?php
include __DIR__ . '/../theater/config/config.php';

// Get page number from query string, default = 1
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

// Fetch JSON data from the API
$api_url = BASE_URI . "note/list?page={$page}&sort={$sort}";
$response = file_get_contents($api_url);

// Decode JSON
$data = json_decode($response, true);

// Safety checks
$notes = $data['notes'] ?? $data ?? [];
$totalPages = $data['totalPages'] ?? 1;
require __DIR__ . '/../db.php';
$db = new FileDB($config);
 $tnotes = $db->getNotes();
$serving = count($tnotes);
switch ($sort) {
    case 'newest':
        $selectorcurrent = 'Lastest Clipnotes';
        break;
    case 'time':
        $selectorcurrent = 'Oldest Clipnotes';
        break;
    case 'score':
        $selectorcurrent = 'Voted Clipnotes';
        break;
    default:
        $selectorcurrent = 'Lastest Clipnotes';
        break;
}

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

    @keyframes scrollridge {
        0% {
            background-position: 100% 0;
        }

        100% {
            background-position: 100% 33px;
            /* Adjust as needed for the desired effect */
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
        font-size: 12px;
        font-family: flipnote;
        width: 320px;
        height: 240px;
        max-width: 100%;
        max-height: 100%;
        position: relative;
        overflow: hidden;
        background: url(img/bg.png) repeat;
        /* Repeat the background image */

        animation: scrollBackground 40s linear infinite;
        /* Apply animation */
        image-rendering: pixelated;
        /* Enable pixelated rendering */
    }

    .soniclookingass {
        width: 320px;
        height: 240px;
        max-width: 100%;
        max-height: 100%;
        position: relative;
        overflow: hidden;
        background: url(img/ridge.png) repeat-y;
        /* Repeat the background image */

        animation: scrollridge 1s linear infinite;
        /* Apply animation */
        image-rendering: pixelated;
        /* Enable pixelated rendering */
    }

    .nowservingcont {
        position: fixed;
        width: 82px;
        height: 20px;
        font-family: flipnote;
        font-size: 12px;
        color: #8b7ffe;
        word-break: break-word;
        top: 3px;
        left: 189px;
    }

    p {
        font-family: flipnote;
        color: #8b7ffe;
        margin-top: -5px;
        margin-bottom: 1px;
        margin-left: 1px;
        margin-right: 1px;
    }

    .cgrid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        column-gap: 20px;
        row-gap: 4px;
        width: 100%;
        box-sizing: border-box;
    }

    .cthumb {
        background-color: white;
        width: 75px;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        border: 1px solid #8b7ffe;
    }

    .thumb {
        aspect-ratio: 4/3;
        object-fit: contain;
    }

    .cmeta {
        background-color: white;
        border-top: 1px solid #8b7ffe;
        padding-left: 2px;
        padding-right: 2px;
        color: #8b7ffe;
        font-size: 12px;
        display: flex;
        flex-direction: column;
        gap: 0.2em;
    }

    .ctitle {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .pagination {
        background-image: url(img/footer.png);
        position: absolute;
        top: 223px;
        left: 15px;
        width: 273px;
        height: 17px;
        text-align: center
    }

    a {
        text-decoration: none;
        color: #8b7ffe;
    }

    ;
    </style>

</head>

<body>
    <div class="mockup-container">
        <img src="img/header.png" alt="header" style="position: absolute; top: 0px; left: 15px;">

        <div id="dropdown" class="dropdown" onclick="toggleDropdown()"
            style="position: absolute; top: 2px; left: 20px; z-index: 1;">
            <div class="dropdown-button"
                style="background-color: white; border: 1px solid #8b7ffe; min-width: 78px; min-height:6px; padding: 2px 1px; color: #8b7ffe;">
                <span class="fuckingshit" style="position:fixed; top:0px;"><?=$selectorcurrent?></span>
            </div>




            <div class="dropdown-content"
                style="display: none; background-color: white; border: 1px solid #8b7ffe; border-top:0px; min-width: 20px;">
                <div class="dropdown-item" style="padding-left: 1px; color: #8b7ffe; background-color: white;"
                    onclick="changeSort('newest'),handleDropdownChange('latest Clipnotes')">latest Clipnotes</div>


                <div class="dropdown-item" style="padding-left: 1px; color: #8b7ffe; background-color: white;"
                    onclick="changeSort('time'),handleDropdownChange('oldest Clipnotes')">oldest Clipnotes</div>


                <div class="dropdown-item" style="padding-left: 1px; color: #8b7ffe; background-color: white;"
                    onclick="changeSort('score'),handleDropdownChange('voted Clipnotes')">voted Clipnotes</div>


            </div>
        </div>

        <img id="dropdown-image" src="img/barclose.png" alt="Dropdown State"
            style="position: absolute; top: 2px; left: 101px; z-index: 2;" onclick="toggleDropdown()">

        <div class="soniclookingass"></div>

        <img id="sinewave-cat" src="img/cat.png" alt="cat" style="position: absolute; top: 17px; left: 225px;"
            onclick="playSoundAndRedirect('img/log.png', 'login_page_url');">

        <img src="img/nowserving.png" alt="nowserving" style="position: absolute; top: 2px; left: 188px;"
            onclick="playSoundAndRedirect('img/reg.png', 'register_page_url');">
        <div class="nowservingcont">
            <p>Not implemented lmao</p>
            <p><?=$serving?> Clipnotes</p>
        </div>
        <!-- clipnote grid -->
        <div class="cgrid" style="position: absolute; top: 68px; left: 15px; width: 273px; overflow-y: auto;">
            <?php foreach ($notes as $note):?>
            <div class="cthumb">
                <img src="<?=BASE_URI?>data/thumbnails/<?=$note['filename']?>.png"
                    class="thumb" style="image-rendering: smooth;">


                <div class="cmeta">
                    <div class="ctitle">
                        <span><a
                                href="<?=BASE_URI?>theater "><?=$note['username']?></a></span>
                        <span id="starc">
                            <?=$note['rating']?></span>
                    </div>
                </div>
            </div>
            <?php endforeach;?>
        </div>
        <!-- Pagination -->
        <div class="pagination">
            <?php if ($page > 1): ?>
            <img src="img/lr.png" alt="previous"onclick="playSoundAndRedirect('img/lr.png', '?page=<?=($page-1)?>&sort=<?=$sort?>'); ">
            
            <?php else: ?>
            <img src="img/lr.png" style="opacity:0.3;">
            <?php endif; ?>

            <strong><?=$page?></strong> / <?=$totalPages?>

            <?php if ($page < $totalPages): ?>
            <img src="img/rr.png" alt="next"onclick="playSoundAndRedirect('img/rr.png', '?page=<?=($page+1)?>&sort=<?=$sort?>'); ">
            <?php else: ?>
            <img src="img/rr.png" style="opacity:0.3;">
            <?php endif; ?>
        </div>
    </div>
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