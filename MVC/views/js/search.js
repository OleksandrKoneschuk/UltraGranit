document.addEventListener("DOMContentLoaded", function () {
    let searchInput = document.getElementById("search-input");
    let searchResults = document.getElementById("search-results");
    let clearBtn = document.getElementById("clear-search");

    searchInput.addEventListener("input", function () {
        let query = this.value.trim();
        if (query.length < 2) {
            searchResults.classList.add("d-none");
            return;
        }

        fetch('/product/search_ajax?query=' + encodeURIComponent(query))
            .then(response => response.json())
            .then(data => {
                searchResults.innerHTML = '';
                searchResults.classList.remove("d-none");

                if (data.categories.length > 0) {
                    let categoriesHTML = '<div class="search-categories">';
                    data.categories.forEach(category => {
                        categoriesHTML += `
                            <a href="/category/view/${category.id}" class="search-category-badge" style="padding: 0.5em">
                                ${category.name}
                            </a>`;
                    });
                    categoriesHTML += '</div>';
                    searchResults.innerHTML += categoriesHTML;
                }

                if (data.products.length > 0) {
                    let productsHTML = '<div class="search-products"><strong class="p-2 d-block" style="text-align: left; color: #333">Результати пошуку:</strong>';

                    data.products.forEach(product => {
                        let imagePath = product.main_photo ? `/${product.main_photo}` : "/files/products/no_image.png";

                        let highlightedName = product.name.replace(new RegExp(query, "gi"), match => `<span class="highlight">${match}</span>`);

                        productsHTML += `
            <div class="search-product-item d-flex align-items-center">
                <a href="/product/view/${product.id}" class="d-flex align-items-center w-100">
                    <img src="${imagePath}" class="search-thumbnail">
                    <span class="search-product-name">${highlightedName} - ${product.price} грн</span>
                </a>
                ${data.isAdmin ? `
                    <a href="/product/edit/${product.id}" class="edit-icon" title="Редагувати">
                        ✏️
                    </a>
                ` : ''}
            </div>`;
                    });

                    productsHTML += '</div>';
                    searchResults.innerHTML += productsHTML;
                } else {
                    searchResults.innerHTML += `<div class="no-results p-2" style="text-align: center; color: #777;">😞 Нічого не знайдено</div>`;
                }

            })
            .catch(error => console.error('Помилка пошуку:', error));
    });

    document.addEventListener("click", function (event) {
        if (!searchInput.contains(event.target) && !searchResults.contains(event.target)) {
            searchResults.classList.add("d-none");
        }
    });

    clearBtn.addEventListener("click", function () {
        searchInput.value = "";
        searchResults.classList.add("d-none");
    });
});
