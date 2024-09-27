

**Project:** 
Web Scraping Dashboard

**Overview:** 
This project scrapes product data from e-commerce websites and displays it in a dashboard. The backend is built with PHP, featuring API key authentication for secure access, and the frontend presents scraped data using charts.

**Key Features:**
- API key authentication for secure access.
- Scraping product data (name, price, category, discounts).
- Query filters for searching products.
- Visualization of data through a frontend dashboard.
- Optionally stores scraped data in JSON format.

**Project Structure:**
- **Backend:** Handles API requests, scraping, and API key management.
- **Frontend:** Displays scraped data and charts in a dashboard.
- **Data Storage:** Optionally saves scraped data in JSON format.

**Prerequisites:**
- XAMPP or any PHP server with PHP 7+.
- cURL enabled in PHP.

**Setup Steps:**
1. Install XAMPP and place the project in the `htdocs` folder.
2. Enable cURL by editing `php.ini`.
3. Add e-commerce URLs to `urls.txt` for scraping.

**Usage:**
- Generate an API key via a POST request.
- Use the key to make GET requests to scrape data.
- Access the dashboard via `index.html`.

**Troubleshooting:**
- Ensure cURL is enabled and that the correct file permissions are set.

**Technologies Used:** PHP, cURL, JavaScript, Chart.js for frontend visualization.

**License:** MIT License. 

