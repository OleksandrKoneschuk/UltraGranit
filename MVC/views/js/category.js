document.addEventListener('DOMContentLoaded', function () {
    let currentPage = 1;
    let itemsPerPage = 9;
    let sortOption = 'name_asc';

    const loadMoreButton = document.getElementById('load-more');
    const sortSelect = document.getElementById('sort');
    const productList = document.getElementById('product-list');

    sortSelect.addEventListener('change', function () {
        sortOption = this.value;
        currentPage = 1;
        productList.innerHTML = '';
        loadProducts();
    });

    loadMoreButton.addEventListener('click', function () {
        loadProducts();
    });

    function loadProducts() {
        const queryParams = new URLSearchParams({
            page: currentPage,
            limit: itemsPerPage,
            sort: sortOption
        });

        const categoryId = window.location.pathname.split('/').pop();

        fetch(`/product/loadMore/${categoryId}?${queryParams.toString()}`)
            .then(response => response.json())
            .then(result => {
                if (result.products.length > 0) {
                    result.products.forEach(product => {
                        const productElement = document.createElement('div');
                        productElement.className = 'col-md-4 mb-3';
                        productElement.innerHTML = `
                            <div class="card text-center h-100">
                                <a href="/product/view/${product.id}">
                                    <img src="/${product.main_photo ? product.main_photo : 'files/products/no_image.png'}" class="card-img-top" style="width: 100%; height: 286px" alt="Фото не знайдено">
                                    <div class="card-body">
                                        <h5 class="card-title">${product.name}</h5>
                                        <p class="card-text">${product.price} грн</p>
                                    </div>
                                </a>
                                ${result.isAdmin ? `
                                <div class="card-body">
                                    <a href="/product/edit/${product.id}" class="btn btn-warning mb-1">Редагувати продукт</a>
                                    <a href="/product/delete/${product.id}" class="btn btn-danger">Видалити продукт</a>
                                </div>
                                ` : ''}
                            </div>
                        `;
                        productList.appendChild(productElement);
                    });
                    currentPage++;
                } else {
                    alert('Більше товарів немає.');
                    loadMoreButton.disabled = true;
                }
                loadMoreButton.style.display = result.hasMore ? 'block' : 'none';
            })
            .catch(error => console.error('Помилка завантаження додаткових товарів:', error));
    }

    loadProducts();
});
