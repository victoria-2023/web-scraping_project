<?php
// Function to generate a random API key
function generateApiKey($length = 32) {
    if ($length % 2 !== 0 || $length <= 0) {
        throw new InvalidArgumentException('Length must be a positive even integer.');
    }
    return bin2hex(random_bytes($length / 2)); // Generates a random hexadecimal string
}

// Define the path to the api_keys.txt file
$apiKeysFile = __DIR__ . '/api_keys.txt';

// Only allow POST requests to generate a new key
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Generate the new API key
        $newApiKey = generateApiKey();
        
        // Check if the file is writable
        if (is_writable($apiKeysFile)) {
            // Append the API key to the api_keys.txt file
            file_put_contents($apiKeysFile, $newApiKey . PHP_EOL, FILE_APPEND);
            
            // Return the new API key as a response in JSON format
            http_response_code(201); // Created
            echo json_encode(['api_key' => $newApiKey]);
        } else {
            // If the file isn't writable, return an error
            http_response_code(500);
            echo json_encode(['error' => 'Unable to write to api_keys.txt']);
        }
    } catch (Exception $e) {
        // Handle any unexpected errors
        http_response_code(500); // Internal Server Error
        echo json_encode(['error' => 'An error occurred: ' . $e->getMessage()]);
    }
} else {
    // If the request method is not POST, return a 405 Method Not Allowed response
    http_response_code(405);
    echo json_encode(['error' => 'Only POST requests are allowed']);
}
?>
