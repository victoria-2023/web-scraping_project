<?php
header("Content-Type: application/json");

// Check if necessary files exist
$apiKeysFile = __DIR__ . '/api_keys.txt';
$urlsFile = __DIR__ . '/urls.txt';
$dataFile = __DIR__ . '/../data/scraped_data.json';

if (!file_exists($apiKeysFile) || !file_exists($urlsFile)) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'Required file not found (api_keys.txt or urls.txt)']);
    exit;
}

// Get the API key from the request
$apiKey = $_GET['api_key'] ?? '';

// Function to check if the provided API key is valid
function isValidApiKey($apiKey, $apiKeysFile) {
    $storedKeys = file($apiKeysFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    return in_array($apiKey, $storedKeys);
}

// Validate API key
if (!isValidApiKey($apiKey, $apiKeysFile)) {
    http_response_code(403); // Forbidden
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Read URLs from urls.txt
$urls = file($urlsFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

// Initialize results array
$results = [];

// Get search query if present
$searchQuery = isset($_GET['query']) ? strtolower(trim($_GET['query'])) : null;

// Loop through each URL and scrape
foreach ($urls as $url) {
    $data = scrapeSite($url, $searchQuery);
    if ($data) {
        $results[] = $data;
    }
}

// Check if the results array is empty (no data scraped)
if (empty($results)) {
    http_response_code(404); // Not Found
    echo json_encode(['error' => 'No data found or scraped from the provided URLs']);
    exit;
}

// Save the scraped data to scraped_data.json (optional)
file_put_contents($dataFile, json_encode($results, JSON_PRETTY_PRINT));

// Output the final JSON response
echo json_encode($results, JSON_PRETTY_PRINT);

// Function to scrape the content of each site
function scrapeSite($url, $searchQuery = null) {
    // Initialize cURL session
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    
    // Execute cURL request
    $html = curl_exec($ch);

    // Check for cURL errors
    if (curl_errno($ch)) {
        error_log('cURL error: ' . curl_error($ch));
        curl_close($ch);
        http_response_code(500);
        echo json_encode(['error' => 'Failed to fetch data from ' . $url]);
        return null;
    }

    curl_close($ch);

    // If no HTML content is retrieved, return null
    if (!$html) {
        error_log("Failed to fetch HTML content from $url");
        return null;
    }

    // Load HTML into DOMDocument
    libxml_use_internal_errors(true); // Suppress HTML warnings
    $dom = new DOMDocument();
    @$dom->loadHTML($html); // Suppress warnings due to malformed HTML
    libxml_clear_errors();

    $xpath = new DOMXPath($dom);

    // Modify these XPath queries to fit the site's structure
    $productNames = $xpath->query('//span[contains(@class, "product-name")]');
    $productPrices = $xpath->query('//span[contains(@class, "product-price")]');
    $categories = $xpath->query('//span[contains(@class, "product-category")]');
    $discounts = $xpath->query('//span[contains(@class, "discount")]');

    // Check if data is retrieved from XPath queries
    if ($productNames->length === 0) {
        error_log("No products found on $url");
        return null;
    }

    // Loop through the scraped data
    $products = [];
    for ($i = 0; $i < $productNames->length; $i++) {
        $name = trim($productNames->item($i)->nodeValue ?? 'Unknown');
        $price = trim($productPrices->item($i)->nodeValue ?? 'Unknown');
        $category = trim($categories->item($i)->nodeValue ?? 'Unknown');
        $discount = trim($discounts->item($i)->nodeValue ?? 'No Discount');

        // If a search query is provided, filter the products based on the name
        if ($searchQuery && strpos(strtolower($name), $searchQuery) === false) {
            continue; // Skip products that don't match the search query
        }

        $products[] = [
            'name' => ucfirst($name),
            'price' => $price,
            'category' => $category,
            'discount' => $discount
        ];
    }

    // Return the scraped products if available
    if (!empty($products)) {
        return [
            'site' => $url,
            'products' => $products
        ];
    }

    return null;
}
?>
