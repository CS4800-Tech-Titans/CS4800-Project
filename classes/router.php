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



