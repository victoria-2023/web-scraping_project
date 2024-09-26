document.getElementById('searchButton').addEventListener('click', function() {
    let searchQuery = document.getElementById('searchBar').value.trim();

    // Replace 'mySuperSecretKey123' with your actual API key
    const apiKey = 'mySuperSecretKey123'; // <-- Replace this

    // Construct the API URL with API key and search query
    let apiUrl = `../backend/api.php?api_key=${apiKey}`;
    if (searchQuery !== '') {
        apiUrl += `&query=${encodeURIComponent(searchQuery)}`;
    }

    // Fetch data from the API
    fetch(apiUrl)
        .then(response => response.json()) // Parse the JSON response
        .then(data => {
            if (data.error) {
                alert(data.error);
                return;
            }
            displayData(data); // Display the data on the dashboard
            displayCategories(data); // Display popular categories
            drawCategoryChart(data); // Draw chart for categories
        })
        .catch(error => {
            console.error("Error fetching data:", error);
            alert("Failed to fetch data. Please check the console for more details.");
        });
});

// Function to display product data
function displayData(data) {
    let productList = document.getElementById('productList');
    productList.innerHTML = ''; // Clear previous data

    data.forEach(site => {
        let siteHeader = document.createElement('h2');
        siteHeader.textContent = `Site: ${site.site}`;
        productList.appendChild(siteHeader);

        site.products.forEach(product => {
            let listItem = document.createElement('li');
            listItem.textContent = `${product.name} - ${product.price} - ${product.category} - ${product.discount}`;
            productList.appendChild(listItem);
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
