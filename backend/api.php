<?php
header("Content-Type: application/json");

// Define your API key here
$apiKey = $_GET['api_key'] ?? '';
$validApiKey = 'mySuperSecretKey123'; // <-- Replace with your actual API key

// Validate API key
if ($apiKey !== $validApiKey) {
    http_response_code(403); // Forbidden
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Read URLs from urls.txt
$urls = file('urls.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

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

// Save the scraped data to scraped_data.json (optional)
file_put_contents('../data/scraped_data.json', json_encode($results, JSON_PRETTY_PRINT));

// Output the final JSON response
echo json_encode($results, JSON_PRETTY_PRINT);

// Function to scrape the content of each site
function scrapeSite($url, $searchQuery = null) {
    // Initialize cURL session
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // Optional: Set user agent to mimic a real browser
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
    // Optional: Set timeout
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $html = curl_exec($ch);
    curl_close($ch);

    if (!$html) {
        return null; // Return null if no HTML is fetched
    }

    // Load HTML into DOMDocument
    $dom = new DOMDocument();
    @$dom->loadHTML($html); // Suppress warnings due to malformed HTML
    $xpath = new DOMXPath($dom);

    // Modify these XPath queries to fit the site's structure
    // Example XPath for Amazon (replace with actual selectors)
    $productNames = $xpath->query('//span[contains(@class, "a-size-medium a-color-base a-text-normal")]');
    $productPrices = $xpath->query('//span[contains(@class, "a-price-whole")]');
    $categories = $xpath->query('//span[contains(@class, "a-size-base a-color-secondary")]');
    $discounts = $xpath->query('//span[contains(@class, "a-offscreen")]'); // Example: price including discount

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

    return [
        'site' => $url,
        'products' => $products
    ];
}
?>
