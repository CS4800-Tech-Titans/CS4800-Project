<?php

// Get the request URI
$requestUri = $_SERVER['REQUEST_URI'];
//echo $requestUri;
// Remove query strings from the URI
$requestUri = strtok($requestUri, '?');

// Define a base path for your router
$basePath = '/classes';

// Check if the request URI starts with the base path  (this if statement might not be necessary actually ? )
if (strpos($requestUri, $basePath) === 0) {
    // Extract the part of the URI after the base path
    $subPath = substr($requestUri, strlen($basePath));

    // Split the subpath into segments
    $segments = explode('/', trim($subPath, '/'));



    /*foreach ($segments as $item) {
        echo gettype($item);
        echo ":".$item."-----";
    }*/

    // Handle routing based on the segments
    if (count($segments) > 0) 
    {
        $classId = $segments[0];

        //echo "------classId: ".$classId;
        if ($classId == "") 
        {
            include "index.php";
        }
        else if (!is_numeric($classId))
        {
            http_response_code(404); 
            include_once("404.html");
            die();
        }
        else
        { 
            if (count($segments) > 1) 
            {
                $action = $segments[1];
                
                // Handle specific actions based on the action segment
                switch ($action) {                        
                    case 'users':
                        // Handle the users action for the class
                        break;
                    case 'groups':
                        $groupSegments = array_slice($segments, 2);
                        include_once "groups/router.php";
                        //if (count($segments) > 2)
                        //{
                        //    $groupId = $segments[2];
                            //echo $groupId;
                        //}
                        break;
                    case 'createGroup':
                        #echo "THIS WAS A REQUEST TO MAEK A GROUP !! FOR CLASS ID ".$classId;
                        include_once "../protected/ensureLoggedIn.php";

                        if ($_SERVER["REQUEST_METHOD"] === "POST") {
                            // Check if the file was uploaded without errors
                            $imageContent = null;
                            if (isset($_FILES["image"]) && $_FILES["image"]["error"] == UPLOAD_ERR_OK) 
                            {
                                #$targetDir = "uploads/"; // Specify your desired directory
                                #$targetFile = $targetDir . basename($_FILES["file"]["name"]);

                                $imagePath = $_FILES["image"]["tmp_name"];
                                $imageContent = file_get_contents($imagePath);
                                #echo '<img src="data:image/*;base64, ' . base64_encode($imageContent) . '">';
                            }

                            $name = $_POST["name"];
                            $description = $_POST["description"];

                            include "../protected/connSql.php";
                            
                            $stmt = $conn->prepare("INSERT INTO `groups` (name, description, photo, classId) VALUES (?, ?, ?, ?);");
                            $stmt->bind_param("sssi", $name, $description, $imageContent, $classId);

                            # !!! THIS LINE REQUIRES "max_allowed_packet=32M" OR SOMETHING SIMILAR IN my.ini FILE FOR MYSQL CONFIG. MYSQL DEFAULTS TO ONLY 1MB MAX PACKET SIZE.
                            $stmt->execute();

                            $newGroupId = $stmt->insert_id;

                            $stmt->close();

                            $linkInsertQuery = $conn->prepare("INSERT INTO linkusergroup (userId, groupId, role) VALUES (?, ?, ?)");
        
                            // Define the user ID (fetched from the session)
                            $userId = $_SESSION["userId"];

                             // TODO: Replace this with the actual user role
                            $role = 1;

                            $linkInsertQuery->bind_param("iii", $userId, $newGroupId, $role);

                            // Check if the entry creation is successful
                            if ($linkInsertQuery->execute()) {
                                // Group and linkusergroup entry creation successful, redirect to a success page or back to the group listing
                                //header("Location: /classes/{$classId}");
                            } else {
                                // Error handling: Display an error message or redirect to an error page
                                echo "Failed to create a linkusergroup entry. Please try again.";
                            }
                            $linkInsertQuery->close();
                            header("Location: /classes/".$classId."/");
                        }
                        
                        break;
                    default:
                        http_response_code(404); 
                        include_once("404.html");
                        die();
                        break;
                }
            } 
            else 
            {
                // Handle the case where only the class ID is provided
                // e.g., /classes/123
                include_once "classHomepage.php";
                //echo "Homepage!";
            }
        }

    }
}
?>



