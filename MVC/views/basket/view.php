<?php
/** @var array $products */

if (empty($products)) {
    echo "<p>Ваш кошик порожній.</p>";
} else {
    foreach ($products as $product) {
        echo "<div class='product-item'>";
        echo "<div class='row'>";
        echo "<div class='col-3'>";
        if (!empty($product->main_photo)) {
            echo "<img src='/" . htmlspecialchars($product->main_photo) . "' class='img-thumbnail' alt='Фото не знайдено' style='max-width: 100%;'>";
        } else {
            echo "<img src='/files/products/no_image.png' class='img-thumbnail' alt='Фото не знайдено' style='max-width: 100%;'>";
        }
        echo "</div>";
        echo "<div class='col-9'>";
        echo "<h5>" . htmlspecialchars($product->name) . "</h5>";
        echo "<p>Ціна: " . htmlspecialchars($product->price) . "</p>";
        echo "<a class='remove-btn' data-id='" . htmlspecialchars($product->basket_id) . "'>
                <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-x-lg' viewBox='0 0 16 16'>
                    <path d='M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z'/>
                </svg>
              </a>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
    }
}
?>