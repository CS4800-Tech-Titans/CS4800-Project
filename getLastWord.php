<?php
// Check if the input parameter is set in the URL
if (isset($_GET['input'])) {
    // Get the input string from the URL
    $input = $_GET['input'];

    $result = getLastWord($input);

    echo "You sent the string: " . $input . "<br>";
    echo "The last word in the string is: " . $result . "<br>";
    echo "The length of this word is: " . strlen($result)-1 . "<br>";
} else {
    echo "Input parameter is missing.";
}


function getLastWord($s) {
    $seenLetter = false;
    $letterI = strlen($s) - 2;
    
    for ($i = strlen($s) - 1; $i >= 0; $i--) {
        if ($s[$i] == ' ') {
            if ($seenLetter) {
                return substr($s, $i, $letterI - $i+1);
            }
        } else {
            if (!$seenLetter) {
                $letterI = $i;
                $seenLetter = true;
            }
        }
    }
    
    return substr($s, 0, $letterI + 1);
}
?>