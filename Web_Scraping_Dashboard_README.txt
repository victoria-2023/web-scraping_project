
# Web Scraping Dashboard

## Project Overview
This project is a web scraping dashboard that allows you to scrape product data from selected e-commerce websites and display it in a user-friendly frontend. The backend is built using PHP, and it includes API key authentication to ensure secure access. The frontend displays scraped product data, categories, and visualizes it using charts.

## Features
- **API Key Authentication:** Secure access to the API using API keys.
- **Web Scraping:** Scrape product data (name, price, category, discount) from e-commerce websites.
- **Filtering:** Search for specific products using a query filter.
- **Visualization:** Display the scraped data in a dashboard with product listings, categories, and charts.
- **JSON Data Storage:** Optionally save scraped data to a JSON file.

## Project Structure
```
web-scraping_project/
├── backend/
│   ├── api.php              <-- Main backend API for scraping and data handling
│   ├── generate_key.php      <-- API key generation script
│   ├── api_keys.txt          <-- File to store generated API keys
│   └── urls.txt              <-- File containing URLs to scrape
├── frontend/
│   ├── index.html            <-- Main frontend HTML file (dashboard interface)
│   └── dashboard.js          <-- Frontend JavaScript file (handles API calls and UI updates)
└── data/
    └── scraped_data.json     <-- (Optional) Stores scraped data
```

## Prerequisites
- XAMPP (or any other PHP server environment)
- PHP (version 7 or higher)
- cURL enabled in PHP (check your php.ini file for `extension=curl`)

## Installation and Setup

### 1. Install XAMPP:
- Download and install [XAMPP](https://www.apachefriends.org/index.html) if you haven’t already.
- Ensure Apache is running from the XAMPP control panel.

### 2. Clone or Download the Project:
- Place the project folder (`web-scraping_project`) inside the `htdocs` folder of your XAMPP installation (typically located at `C:/xampp/htdocs`).

### 3. Enable cURL:
- Open your `php.ini` file (found in `C:/xampp/php/php.ini`).
- Search for `;extension=curl` and uncomment it by removing the semicolon (`;`).
- Save the file and restart Apache from the XAMPP control panel.

### 4. Configure the URLs to Scrape:
- Open the `urls.txt` file inside the `backend` folder and add the URLs of the e-commerce pages you want to scrape. Each URL should be on a new line.

## How to Use the Project

### 1. Generate an API Key
Before making any requests to the API, you need to generate an API key. You can do this by making a POST request to the `generate_key.php` endpoint.

- **URL:** `http://localhost/web-scraping_project/backend/generate_key.php`
- **Method:** POST

You can use a tool like Postman or cURL to generate the API key.

Example cURL Command:
```bash
curl -X POST http://localhost/web-scraping_project/backend/generate_key.php
```
The server will respond with a new API key in the following format:
```json
{
    "api_key": "your_generated_api_key"
}
```

### 2. Scrape Data
Once you have the API key, you can start scraping data. To scrape all products from the URLs listed in `urls.txt`, make a GET request to `api.php` with your API key.

- **URL:** `http://localhost/web-scraping_project/backend/api.php?api_key=your_api_key`
- **Method:** GET

To filter the results (e.g., search for "laptop"), you can add a query parameter to the request:
```bash
http://localhost/web-scraping_project/backend/api.php?api_key=your_api_key&query=laptop
```

### 3. Access the Frontend Dashboard
To access the dashboard, open the following URL in your browser:
```bash
http://localhost/web-scraping_project/frontend/index.html
```
This will load the dashboard where you can enter a search term, click "Search," and view the scraped data along with charts showing product categories.

## Example Requests

- **Generate API Key:**
```bash
curl -X POST http://localhost/web-scraping_project/backend/generate_key.php
```

- **Scrape Data Without Filter:**
```bash
http://localhost/web-scraping_project/backend/api.php?api_key=your_api_key
```

- **Scrape Data With Search Filter:**
```bash
http://localhost/web-scraping_project/backend/api.php?api_key=your_api_key&query=laptop
```

## Troubleshooting

### cURL Not Enabled:
If you get an error related to `curl_init()` not being defined, make sure cURL is enabled in your `php.ini` file (`extension=curl` should be uncommented).

### File Permissions:
Ensure that your server has the necessary permissions to read and write to the `api_keys.txt`, `urls.txt`, and `scraped_data.json` files.

### Missing Files:
If you receive a 500 error, check to ensure that the `api_keys.txt` and `urls.txt` files exist and are properly configured.

### Invalid API Key:
If you get a 403 Forbidden error, verify that you're using a valid API key. If needed, generate a new key.

## Technologies Used
- **PHP:** Handles the backend logic, scraping, and API key authentication.
- **cURL:** Used to scrape HTML content from external websites.
- **DOMDocument and XPath:** Parse and extract data from the HTML of scraped websites.
- **JavaScript/Chart.js:** Handles the frontend, including dynamic data fetching and visualizing data using charts.

## Contributors
Feel free to contribute to this project by submitting pull requests or reporting issues.

## License
This project is licensed under the MIT License.
