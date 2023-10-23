<?php

// Handle routing based on the segments (recieved when this was included by classes router)

//echo " THIS IS GROUP ROUTER";

$groupId = "";
if (count($groupSegments) > 0)
    $groupId = $groupSegments[0];

if ($groupId == "") 
{
    include "index.php";
}
else if (!is_numeric($groupId))
{
    http_response_code(404); 
    include_once("404.html");
    die();
}
else
{ 
    echo "This is group #".$groupId;
    /*if (count($groupSegments) > 1) 
    {
        $action = $groupSegments[1];
        
        // Handle specific actions based on the action segment
        switch ($action) {                        
            case 'users':
                // Handle the users action for the class
                break;
            case 'groups':
                //$groupSegments = array_slice($segments, 2);
                //include_once "groupsRouter.php";
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
    }*/
}


?>



