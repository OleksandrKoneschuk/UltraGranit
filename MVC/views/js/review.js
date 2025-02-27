document.addEventListener("DOMContentLoaded", function () {
    const reviewForm = document.getElementById("review-form");
    const reviewsContainer = document.getElementById("reviews");
    const messagesContainer = document.getElementById("review-messages");
    const productId = document.querySelector("[name='product_id']").value;
    const isAdmin = document.body.getAttribute("data-is-admin") === "true"; // Отримуємо статус адміністратора

    function showMessage(type, message) {
        messagesContainer.innerHTML = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>`;
    }

    function loadReviews() {
        fetch(`/product/loadReviews?product_id=${productId}`)
            .then(response => response.json())
            .then(data => {
                reviewsContainer.innerHTML = "";
                if (data.reviews.length > 0) {
                    data.reviews.forEach(review => {
                        const reviewElement = document.createElement("div");
                        reviewElement.classList.add("border", "p-3", "mb-2", "rounded", "review-item");
                        reviewElement.setAttribute("data-id", review.id);

                        reviewElement.innerHTML = `
                            <strong>${review.user_name}</strong>
                            <div class="review-stars">${generateStars(review.rating)}</div>
                            <p class="mt-2">${review.review_text ? review.review_text : ''}</p>
                            <small>${formatDate(review.created_at)}</small>
                            ${isAdmin ? `<button class="btn btn-danger btn-sm delete-review-btn" data-id="${review.id}">🗑 Видалити</button>` : ''}
                        `;
                        reviewsContainer.appendChild(reviewElement);
                    });

                } else {
                    reviewsContainer.innerHTML = "<p>Поки що немає відгуків. Будьте першим!</p>";
                }
            })
            .catch(error => console.error("Помилка завантаження відгуків:", error));
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString("uk-UA");
    }

    function generateStars(rating) {
        let starsHTML = "";
        for (let i = 1; i <= 5; i++) {
            starsHTML += i <= rating ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>';
        }
        return starsHTML;
    }

    reviewForm.addEventListener("submit", function (e) {
        e.preventDefault();
        const formData = new FormData(reviewForm);

        fetch("/product/addReview", {
            method: "POST",
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage("success", "Відгук успішно додано!");
                    reviewForm.reset();
                    loadReviews();
                } else {
                    showMessage("danger", "Помилка: " + data.message);
                }
            })
            .catch(error => console.error("Помилка відправки:", error));
    });

    // 🛑 ДЕЛЕГУВАННЯ ПОДІЙ (ОБРОБНИКИ НЕ ПРОПАДАЮТЬ)
    reviewsContainer.addEventListener("click", function (event) {
        if (event.target.classList.contains("delete-review-btn")) {
            event.preventDefault();
            const reviewId = event.target.getAttribute("data-id");

            if (!confirm("Ви впевнені, що хочете видалити цей відгук?")) return;

            // ЛОГУЄМО, ЩО ВІДПРАВЛЯЄТЬСЯ
            console.log("Видалення відгуку ID:", reviewId);

            fetch('/product/deleteReview', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ review_id: reviewId })
            })
                .then(response => response.json())
                .then(data => {
                    console.log("Відповідь сервера:", data); // ЛОГ ВІДПОВІДІ

                    if (data.success) {
                        event.target.closest(".review-item").remove();
                        showMessage("success", "Відгук видалено.");
                    } else {
                        showMessage("danger", data.message);
                    }
                })
                .catch(error => console.error('Помилка видалення відгуку:', error));
        }
    });


    loadReviews(); // Завантажуємо відгуки при завантаженні сторінки
});
