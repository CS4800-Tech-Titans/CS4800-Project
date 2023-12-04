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
        }

        h2 {
            color: #333;
        }

        a {
            text-decoration: none;
            color: #333;
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
    </style>

    <!-- Adds neccissary libraries for qr scanner -->
<script src="https://rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>
<script src="https://cdn.rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>

</head>
    <body>
        <div class="container">
        <h2>Welcome, <?php echo $_SESSION["name"]; ?>!</h2>
        <p>This is the dashboard. You are now authenticated. You are a <?=$roleStr?>.</p>
        <a href="/classes">Go to my classes</a><br>
        
        <?php if ($_SESSION["role"] == 0): ?>
            <!-- Show 'Add a class' for students -->
            <a href="#" id="addClassLink">Add a class</a><br>
        <?php elseif ($_SESSION["role"] == 1): ?>
            <!-- Show 'Create a Class' for teachers -->
            <a href="#" id="createClassLink">Create a class</a><br>
        <?php endif; ?>

            <!-- Overlay to darken the background -->
            <div class="overlay" id="overlay"></div>

            <!-- Popup content for adding a class -->
            <div id="addClassPopup" class="popup">
                <span class="close-btn" id="closePopup">&times;</span>
                <h3>Add a Class</h3>
                <div class="popup-buttons">
                    <button id="scanQRBtn">Scan QR</button>
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

            <p><a href="/logout">Logout</a></p>
        </div>

        <script>

        <?php if ($_SESSION["role"] == 1): // Teacher ?>
            // JavaScript to handle the create class button click
            if (document.getElementById('createClassLink')) {
                document.getElementById('createClassLink').addEventListener('click', function (event) {
                    event.stopPropagation();
                        window.location.href = './create_class.php';
                });
            }
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
                alert('Add class functionality will be implemented here.');
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