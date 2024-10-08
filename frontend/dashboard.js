document.getElementById('searchButton').addEventListener('click', function() {
    let searchQuery = document.getElementById('searchBar').value.trim();

    // Replace 'your_generated_api_key' with the actual API key you generated
    const apiKey = 'bc5aa3d2cda0492e1b5d0625d97ec6ee';  // <-- Use the correct API key here

    // Construct the API URL with API key and search query
    let apiUrl = `../backend/api.php?api_key=${apiKey}`;
    if (searchQuery !== '') {
        apiUrl += `&query=${encodeURIComponent(searchQuery)}`;
    }

    // Fetch data from the API
    fetch(apiUrl)
        .then(response => {
            if (!response.ok) {
                throw new Error(`Error: ${response.status}`);
            }
            return response.json(); // Parse the JSON response
        })
        .then(data => {
            if (data.error) {
                alert(data.error);  // Alert the error from the backend
                return;
            }
            // Call the display functions to populate the dashboard
            displayData(data);
            displayCategories(data);
            drawCategoryChart(data);
        })
        .catch(error => {
            console.error("Error fetching data:", error);
            alert("Failed to fetch data. Please check the console for more details.");
        });
});

// Function to display product data as cards
function displayData(data) {
    let productList = document.getElementById('productList');
    productList.innerHTML = ''; // Clear previous data

    data.forEach(site => {
        let siteHeader = document.createElement('h2');
        siteHeader.textContent = `Site: ${site.site}`;
        productList.appendChild(siteHeader);

        site.products.forEach(product => {
            let productCard = document.createElement('div');
            productCard.className = 'col-md-4 product-list-item';
            productCard.innerHTML = `
                <div class="card p-3">
                    <h5 class="card-title">${product.name}</h5>
                    <p class="card-text">
                        <strong>Price:</strong> ${product.price}<br>
                        <strong>Category:</strong> ${product.category}<br>
                        <strong>Discount:</strong> ${product.discount}
                    </p>
                </div>`;
            productList.appendChild(productCard);
        });
    });
}

// Function to display categories in a table
function displayCategories(data) {
    let categories = {};
    data.forEach(site => {
        site.products.forEach(product => {
            categories[product.category] = (categories[product.category] || 0) + 1;
        });
    });

    let categoryTable = document.getElementById('categoryTable').querySelector('tbody');
    categoryTable.innerHTML = ''; // Clear previous data

    // Convert categories object to array for sorting
    let categoryArray = Object.keys(categories).map(category => ({
        name: category,
        count: categories[category]
    }));

    // Sort categories by count in descending order
    categoryArray.sort((a, b) => b.count - a.count);

    categoryArray.forEach(category => {
        let row = document.createElement('tr');
        row.innerHTML = `<td>${category.name}</td><td>${category.count}</td>`;
        categoryTable.appendChild(row);
    });
}

// Function to draw category chart using Chart.js
function drawCategoryChart(data) {
    let categories = {};
    data.forEach(site => {
        site.products.forEach(product => {
            categories[product.category] = (categories[product.category] || 0) + 1;
        });
    });

    const ctx = document.getElementById('categoryChart').getContext('2d');
    
    // Destroy previous chart instance if exists
    if (window.categoryChartInstance) {
        window.categoryChartInstance.destroy();
    }

    window.categoryChartInstance = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: Object.keys(categories),
            datasets: [{
                data: Object.values(categories),
                backgroundColor: generateColors(Object.keys(categories).length)
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'right',
                },
                title: {
                    display: true,
                    text: 'Product Categories Distribution'
                }
            }
        }
    });
}

// Helper function to generate random colors for the chart
function generateColors(num) {
    const colors = [];
    for(let i = 0; i < num; i++) {
        const r = Math.floor(Math.random() * 255);
        const g = Math.floor(Math.random() * 255);
        const b = Math.floor(Math.random() * 255);
        colors.push(`rgba(${r}, ${g}, ${b}, 0.6)`);
    }
    return colors;
}
