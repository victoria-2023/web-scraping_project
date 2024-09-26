<?php
// Function to generate a random API key
function generateApiKey($length = 32) {
    return bin2hex(random_bytes($length / 2)); // Generates a random hexadecimal string
}

// Only allow POST requests to generate a new key
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Generate the new API key
    $newApiKey = generateApiKey();
    
    // Append the API key to the api_keys.txt file
    file_put_contents('api_keys.txt', $newApiKey . PHP_EOL, FILE_APPEND);
    
    // Return the new API key as a response in JSON format
    echo json_encode(['api_key' => $newApiKey]);
} else {
    // If the request method is not POST, return a 405 Method Not Allowed response
    http_response_code(405);
    echo json_encode(['error' => 'Only POST requests are allowed']);
}
?>
