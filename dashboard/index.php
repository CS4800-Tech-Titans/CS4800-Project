<?php
include_once "../protected/ensureLoggedIn.php";

$roleStr = "";
if ($_SESSION["role"] == 0)
    $roleStr = "Student";
else if ($_SESSION["role"] == 1)
    $roleStr = "Teacher";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        * {
            box-sizing: border-box;
        }
        /* CSS styles for the dashboard page */
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0; /* Reset default margin */
        }

        .container {
            text-align: center;
            margin-top: 100px;
            position: relative;
            display: flex;
            flex-direction: column;
            align-content: center;
        }

        .centered-link {
            display: inline-block; /* Make the container a block element and center its child */
            margin: 5px;
        }

        h2 {
            color: #333;
        }

        a {
            text-decoration: underline;
            color: #333;   
            width: fit-content;
        }

        a:hover {
            text-decoration: underline;
        }

        /* Style for the overlay */
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5); /* Semi-transparent black */
            z-index: 999;
        }

        /* Style for the popup */
        .popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ccc;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            z-index: 1000;
        }

        /* Style for the close button */
        .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            cursor: pointer;
        }

        /* Style for the buttons */
        .popup-buttons {
            margin-top: 20px;
        }

        .popup-buttons button {
            padding: 10px;
            margin: 0 10px;
            cursor: pointer;
        }

        #profile-modal button {
            background-color: #d6eaff;
            color: rgb(0, 0, 0);
            border: none;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
        }

        #profile-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5); /* Dim overlay color */
            z-index: 1; /* Higher z-index than the popup */
        }

        #profile-modal {
            font-family: Arial, sans-serif;
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            padding: 40px;
            background-color: white;
            border: 1px solid #ddd;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            z-index: 2;
            max-width: 600px;
            width: 100%;
            text-align: center;
        }

        #modal-bio-container {
            text-align: left;
            width: 100%;

        }

        #profile-modal h2 {
            color: #333;
        }

        #profile-modal img {
            max-width: 50%;
            border-radius: 50%;
            margin-bottom: 10px;
        }

        #profile-close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: #ddd;
            color: #333;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .modal-container {
            /*display: none;
            align-items: center;*/
            z-index: 10;
            background-color: rgba(0, 0, 0, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            top: 0;
            left: 0;
            height: 100vh;
            width: 100vw;
            position: fixed;

            opacity: 0;
            pointer-events: none;
            transition: opacity 0.40s ease
        }

        .modal-container.show {
            opacity: 1;
            pointer-events: auto;
        }

        .inner-modal-container {
            max-height: 100%;
            overflow-y: auto;
        }

        .modal {
            background-color: white;
            border-radius: 10px;
            width: 600px;
            padding: 30px;
            overflow-y: auto;
            text-align: center;
            font-family: Arial, sans-serif;
            color:black;
            font-size: 18px;
            font-weight: bold;

        }

        .form-group {
            display: flex;
            flex-direction: column;
            font-size: 18px;
            margin: 20px 0px;
        }

        .form-group input,
        .form-group textarea {
            font-family: Arial, sans-serif;
            font-size: 18px;
            line-height: 1.2;
        }


        .form-group textarea {
            resize: none;
            overflow-y: hidden;
        }

        .form-group textarea.max-size {
            overflow-y: visible;
        }

        #img-preview {
           
            margin-top: 10px;
            margin-bottom: 10px;
            border: 0px solid black;
            max-height: 300px;
            max-width: 100%;
            height: auto; 
            width: auto;
            object-fit: contain;           

        }

        .buttons input,
        .buttons button {
            font-size: 18px;
        }

        

    </style>

    <!-- Adds neccissary libraries for qr scanner -->
<script src="https://rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>
<script src="https://cdn.rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>

</head>
    <body>
       
    <div id="profile-overlay"></div>

    <div id="profile-modal">
        <form id="profile-form" action="/set_user_profile.php" method="post" enctype="multipart/form-data">
            <img id="modal-user-photo" alt="User Profile" onclick="document.getElementById('image').click()"><br>
            <input type="file" id="image" name="image" accept="image/*" onchange="displayImagePreview(this)">
            <h1 id="modal-user-name">First Last</h1>  

            <div id="modal-bio-container">
                <h3>Bio</h3>
                <textarea id="modal-user-bio" name="bio" style="width:100%; font-family: Arial, sans-serif; font-size: 16px;" rows="4" cols="50"></textarea>
            </div>
            <br>
            <button id="profile-save-btn" type="submit">Save</button>
            <button id="profile-close-btn" type="button" onclick="closeProfilePopup()">X</button>
        </form>
    </div>

    <div class="modal-container" id="modalContainer">
        <div class="inner-modal-container">

            <div class="modal">
                <h1>Create New Class</h1>
                <form method="POST" action=<?= "process_create_class.php"?> enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="image">Class Icon</label>
                        <img id="img-preview" src="/images/defaultClassPicture.png" alt="Default Image"
                            onclick="document.getElementById('image').click()">
                        <input type="file" id="image" name="image" accept="image/*"
                            onchange="displayImagePreviewClass(this)">
                    </div>

                    <div class="form-group">
                        <label for="className">Name</label>
                        <input type="text" id="name" name="className" required>
                    </div>

                    <div class="form-group">
                        <label for="classDescription">Description</label>
                        <textarea id="classDescription" name="classDescription" rows="4" cols="50" oninput="resizeTextArea(this)"
                            required></textarea>
                    </div>

                    <div class="buttons">
                        <input type="submit" value="Create Class">
                        <button type="button" id="closeModal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

        <div class="container">
        <h2 style="margin-bottom: 0px">Welcome, <?php echo $_SESSION["name"]; ?>!</h2>
        <p>Account type: <b><?=$roleStr?></b></p>

        <div class="centered-link">
            <a href="/classes">Go to my classes</a>
        </div>
        
        <div class="centered-link">
        <?php if ($_SESSION["role"] == 0): ?>
            <!-- Show 'Add a class' for students -->
            <a href="#" id="addClassLink">Join a class</a>
        <?php elseif ($_SESSION["role"] == 1): ?>
            <!-- Show 'Create a Class' for teachers -->
            <a href="#" id="createClassLink">Create a class</a>
        <?php endif; ?>
        </div>

            <!-- Overlay to darken the background -->
            <div class="overlay" id="overlay"></div>

            <!-- Popup content for adding a class -->
            <div id="addClassPopup" class="popup">
                <span class="close-btn" id="closePopup">&times;</span>
                <h3>Join a Class</h3>
                <div class="popup-buttons">
                    <button id="scanQRBtn">Scan QR Code</button>
                    <button id="enterCodeBtn">Enter Class Code</button>
                </div>
            </div>

            <!-- Popup content for entering class code -->
            <div id="enterCodePopup" class="popup">
                <span class="close-btn" id="closeEnterCodePopup">&times;</span>
                <h3>Enter Class Code</h3>
                <input type="text" id="classCodeInput" placeholder="Enter class code">
                <div class="popup-buttons">
                    <button id="addClassBtn">Add</button>
                    <button id="cancelBtn">Cancel</button>
                </div>
            </div>

            <div class="centered-link">
            <a href="#" onclick="openProfileModal()">Edit my profile</a>
            </div>

            <div class="centered-link">
            <a href="/logout">Logout</a>
            </div>
        </div>

        <script>
        userId = <?=$_SESSION["userId"]?>;

        function displayImagePreview(input) {
            var preview = document.getElementById('modal-user-photo');
            var file = input.files[0];
            var reader = new FileReader();

            reader.onloadend = function () {
                preview.src = reader.result;
            }

            if (file) {
                reader.readAsDataURL(file);
            } else {
                //preview.src = "defaultGroupImage.jpg";
            }
        }

        function displayImagePreviewClass(input) {
            var preview = document.getElementById('img-preview');
            var file = input.files[0];
            var reader = new FileReader();

            reader.onloadend = function () {
                preview.src = reader.result;
            }

            if (file) {
                reader.readAsDataURL(file);
            } else {
                //preview.src = "defaultGroupImage.jpg";
            }
        }

        function resizeTextArea(textarea) {
            textarea.style.height = 'auto';
            newHeight = textarea.scrollHeight;

            const maxHeight = 35 * parseFloat(getComputedStyle(textarea).lineHeight);

            if (newHeight > maxHeight) {
                textarea.style.height = maxHeight + "px";
                textarea.classList.add("max-size");
            }
            else {
                textarea.style.height = newHeight + "px";
                textarea.classList.remove("max-size");
            }
        }

        function openProfileModal()
        {
            const xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) 
                {
                    userProfile = JSON.parse(xhr.responseText);

                    document.getElementById("modal-user-name").innerHTML = userProfile.name;

                    document.getElementById("modal-user-bio").innerHTML = userProfile.bio;

                    if (userProfile.photo)
                        document.getElementById("modal-user-photo").src = 'data:image/*;base64, '+userProfile.photo;
                    else
                        document.getElementById("modal-user-photo").src = "/images/defaultProfilePicture.jpg";
                    
                    document.getElementById("profile-overlay").style.display = "block";
                    document.getElementById("profile-modal").style.display = "block";
                }
            };

            const params = `userId=`+userId;

            xhr.open('POST', '/get_user_profile.php', true);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr.send(params);
        }

        function closeProfilePopup() {
            document.getElementById("profile-overlay").style.display = "none";
            document.getElementById("profile-modal").style.display = "none";
        }

        <?php if ($_SESSION["role"] == 1): // Teacher ?>
            // JavaScript to handle the create class button click
            /*if (document.getElementById('createClassLink')) {
                document.getElementById('createClassLink').addEventListener('click', function (event) {
                    event.stopPropagation();
                        window.location.href = './create_class.php';
                });
            }*/
            document.getElementById("createClassLink").addEventListener("click", () => {
                document.getElementById("modalContainer").classList.add("show");
            });
            document.getElementById("closeModal").addEventListener("click", () => {
                document.getElementById("modalContainer").classList.remove("show");
            });
        <?php elseif ($_SESSION["role"] == 0): // Student ?>
            // JavaScript to show/hide the popup and overlay for adding a class
            if (document.getElementById('addClassLink')) {
                document.getElementById('addClassLink').addEventListener('click', function () {
                    document.getElementById('addClassPopup').style.display = 'block';
                    document.getElementById('overlay').style.display = 'block';
                });
            }
        <?php endif; ?>
            document.getElementById('closePopup').addEventListener('click', function () {
                document.getElementById('addClassPopup').style.display = 'none';
                document.getElementById('overlay').style.display = 'none';
            });

            document.getElementById('enterCodeBtn').addEventListener('click', function () {
                document.getElementById('addClassPopup').style.display = 'none';
                document.getElementById('enterCodePopup').style.display = 'block';
            });

            document.getElementById('closeEnterCodePopup').addEventListener('click', function () {
                document.getElementById('enterCodePopup').style.display = 'none';
                document.getElementById('overlay').style.display = 'none';
            });

            // Add event listeners for the two buttons inside the "Enter Class Code" popup
            document.getElementById('addClassBtn').addEventListener('click', function () {
                // Implement the action for adding a class
                joinCode = document.getElementById("classCodeInput").value;
                window.open("/join_class/" + joinCode, '_blank');

                // alert('Add class functionality will be implemented here.');
            });

            document.getElementById('cancelBtn').addEventListener('click', function () {
                document.getElementById('enterCodePopup').style.display = 'none';
                document.getElementById('overlay').style.display = 'none';
            });

            

        </script>
        <!-- Add a new popup for QR code scanning -->
        <div id="qrScannerPopup" class="popup">
            <span class="close-btn" id="closeQRScannerPopup">&times;</span>
            <h3>Scan QR Code</h3>
            <video id="qrScannerVideo" width="100%" height="100%"></video>
        </div>

        <!-- Add the following script tags to include the necessary libraries -->
<script src="https://rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>
<script src="https://cdn.rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    // Log to check if Instascan is loaded
    console.log('Instascan loaded:', Instascan);

    // Function to check if the URL is from the specified domain or localhost:8080
    function isFromDomainOrLocalhost8080(url, domains) {
        const urlObject = new URL(url);

        // Check if the hostname is localhost and the port is 8080
        if (urlObject.hostname === 'localhost' && urlObject.port === '8080') {
            return true;
        }

        // Check if the hostname ends with one of the specified domains
        return domains.some(domain => urlObject.hostname === domain || urlObject.hostname.endsWith('.' + domain));
    }

    document.getElementById('scanQRBtn').addEventListener('click', function () {
        document.getElementById('addClassPopup').style.display = 'none';
        document.getElementById('overlay').style.display = 'block';
        document.getElementById('qrScannerPopup').style.display = 'block';

        // Initialize the QR scanner
        let scanner = new Instascan.Scanner({ video: document.getElementById('qrScannerVideo') });
        scanner.addListener('scan', function (content) {
            // Check if the scanned QR code is from the desired domains or localhost:8080
            const allowedDomains = ['groupup.pro', 'localhost'];
            if (isFromDomainOrLocalhost8080(content, allowedDomains)) {
                window.location.href = content; // Navigate to the URL
            } else {
                alert('Invalid QR code. Please scan a QR code from groupup.pro or localhost:8080!');
            }
        });

        // Request permission to use the camera
        Instascan.Camera.getCameras().then(function (cameras) {
            if (cameras.length > 0) {
                scanner.start(cameras[0]); // Use the first available camera
            } else {
                alert('No cameras found.');
            }
        }).catch(function (e) {
            console.error('Error accessing camera:', e);
            alert('Error accessing camera: ' + e);
        });
    });

    document.getElementById('closeQRScannerPopup').addEventListener('click', function () {
        document.getElementById('qrScannerPopup').style.display = 'none';
        document.getElementById('overlay').style.display = 'none';
    });



</script>


    </body>
</html>

<?php
include "../sidebar.html";
?>