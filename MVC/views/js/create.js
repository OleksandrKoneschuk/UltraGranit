document.addEventListener('DOMContentLoaded', function () {
    async function loadBasket() {
        try {
            const response = await fetch('/basket/view');
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            const products = await response.json();

            const basketProducts = document.querySelector('#basket-products tbody');
            basketProducts.innerHTML = '';
            if (products.length === 0) {
                basketProducts.innerHTML = '<tr><td colspan="2">Ваш кошик порожній.</td></tr>';
            } else {
                products.forEach(product => {
                    const productElement = document.createElement('tr');
                    productElement.innerHTML = `
                        <td>${product.name}</td>
                        <td>${product.price} грн.</td>
                    `;
                    basketProducts.appendChild(productElement);
                });

                const totalPrice = products.reduce((total, product) => total + parseFloat(product.price), 0);
                document.getElementById('total-price').innerText = `Загальна сума: ${totalPrice} грн.`;
            }
        } catch (error) {
            console.error('Сталася помилка з fetch operation:', error);
            alert('Сталася помилка при завантаженні кошика. Спробуйте ще раз пізніше.');
        }
    }

    loadBasket();

    document.getElementById('order-form').addEventListener('submit', async function (event) {
        event.preventDefault();
        const formData = new FormData(this);
        try {
            const response = await fetch('/order/submit', {
                method: 'POST',
                body: formData
            });
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }

            const responseText = await response.text();
            console.log('Відповідь сервера:', responseText);
            let result;
            try {
                result = JSON.parse(responseText);
            } catch (e) {
                throw new Error('Invalid JSON: ' + responseText);
            }

            console.log('Результат JSON:', result);

            if (result.success) {
                alert('Замовлення успішно оформлено. Очікуйте дзвінка менеджера');
                window.location.href = '/';
            } else {
                alert('Не вдалося оформити замовлення: ' + result.error);
            }
        } catch (error) {
            console.error('Помилка:', error);
            alert('Сталася помилка при оформленні замовлення. Спробуйте ще раз пізніше.');
        }
    });
});
