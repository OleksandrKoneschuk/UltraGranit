document.addEventListener('DOMContentLoaded', function () {
    const basketButton = document.querySelector('.header-link[data-bs-target="#offcanvasRight"]');
    const offcanvasBody = document.querySelector('.offcanvas-body');

    async function loadBasket() {
        try {
            const response = await fetch('/basket/view');
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            const products = await response.json();

            offcanvasBody.innerHTML = '';
            if (products.length === 0) {
                offcanvasBody.innerHTML = '<p>Ваш кошик порожній.</p>';
            } else {
                products.forEach(product => {
                    const productElement = document.createElement('div');
                    productElement.className = 'product-item row border-bottom border-secondary';
                    productElement.innerHTML = `
                        <div class="col-3">
                            <img src="/${product.main_photo ? product.main_photo : 'files/products/no_image.png'}" class="img-thumbnail" alt="Фото не знайдено">
                        </div>
                        <div class="col-9">
                            <div class="row mb-3">
                                <div class="col-11">
                                    <h5><a href="/product/view/${product.id}">${product.name}</a></h5>
                                </div>
                                <div class="col-1">
                                    <a class="remove-btn" style="cursor: pointer" data-id="${product.basket_id}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                            <div class="col-12">
                                <p>Ціна: ${product.price} грн.</p>
                            </div>
                        </div>
                    `;
                    offcanvasBody.appendChild(productElement);
                });

                const totalPrice = products.reduce((total, product) => total + parseFloat(product.price), 0);
                const totalPriceElement = document.createElement('div');
                totalPriceElement.className = 'total-price';
                totalPriceElement.innerHTML = `<h5>Загальна сума: ${totalPrice} грн.</h5>`;
                offcanvasBody.appendChild(totalPriceElement);

                const orderButton = document.createElement('button');
                orderButton.className = 'btn btn-primary order-btn mt-3';
                orderButton.textContent = 'Оформити замовлення';
                offcanvasBody.appendChild(orderButton);

                orderButton.addEventListener('click', function () {
                    window.location.href = '/order/create';
                });

                updateRemoveButtons();
            }
        } catch (error) {
            console.error('Сталася помилка з fetch operation:', error);
            alert('Сталася помилка при завантаженні кошика. Спробуйте ще раз пізніше.');
        }
    }

    function updateRemoveButtons() {
        document.querySelectorAll('.remove-btn').forEach(button => {
            button.addEventListener('click', async function () {
                const basketItemId = this.getAttribute('data-id');
                try {
                    const response = await fetch(`/basket/remove/${basketItemId}`, {
                        method: 'GET'
                    });
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    const updatedProducts = await response.json();
                    offcanvasBody.innerHTML = '';
                    if (updatedProducts.length === 0) {
                        offcanvasBody.innerHTML = '<p>Ваш кошик порожній.</p>';
                    } else {
                        updatedProducts.forEach(product => {
                            const productElement = document.createElement('div');
                            productElement.className = 'product-item row border-bottom border-secondary';
                            productElement.innerHTML = `
                                <div class="col-3">
                                    <img src="/${product.main_photo ? product.main_photo : 'files/products/no_image.png'}" class="img-thumbnail" alt="Фото не знайдено">
                                </div>
                                <div class="col-9">
                                    <div class="row mb-3">
                                        <div class="col-11">
                                            <h5><a href="/product/view/${product.id}">${product.name}</a></h5>
                                        </div>
                                        <div class="col-1">
                                            <a class="remove-btn" style="cursor: pointer" data-id="${product.basket_id}">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                                                    <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <p>Ціна: ${product.price} грн.</p>
                                    </div>
                                </div>
                            `;
                            offcanvasBody.appendChild(productElement);
                        });

                        const totalPrice = updatedProducts.reduce((total, product) => total + parseFloat(product.price), 0);
                        const totalPriceElement = document.createElement('div');
                        totalPriceElement.className = 'total-price';
                        totalPriceElement.innerHTML = `<h5>Загальна сума: ${totalPrice} грн.</h5>`;
                        offcanvasBody.appendChild(totalPriceElement);

                        const orderButton = document.createElement('button');
                        orderButton.className = 'btn btn-primary order-btn mt-3';
                        orderButton.textContent = 'Оформити замовлення';
                        offcanvasBody.appendChild(orderButton);

                        orderButton.addEventListener('click', function () {
                            window.location.href = '/order/create';
                        });

                        updateRemoveButtons();
                    }
                } catch (error) {
                    console.error('Сталася помилка при видаленні товару:', error);
                    alert('Сталася помилка при видаленні товару. Спробуйте ще раз пізніше.');
                }
            });
        });
    }

    basketButton.addEventListener('click', function () {
        loadBasket();
    });

    document.querySelectorAll(".add-to-cart").forEach(button => {
        button.addEventListener("click", async function () {
            const productId = this.getAttribute("data-product-id");
            try {
                const response = await fetch(`/basket/add/${productId}`, {
                    method: 'GET'
                });
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                const result = await response.json();
                if (result.success) {
                    alert("Товар додано до кошика");
                } else {
                    alert("Не вдалося додати товар до кошика");
                }
            } catch (error) {
                console.error('Помилка:', error);
            }
        });
    });
});