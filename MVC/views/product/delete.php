<?php
/** @var array $product */
?>
<main>
    <div class="container text-center mt-5">
        <div class="alert alert-danger" role="alert">
            <h3>Видалення продукту "<?= htmlspecialchars($product->name) ?>"</h3>
            <p>Ви дійсно хочете видалити продукт?</p>
            <a href="/product/delete/<?=$product->id?>/yes" class="btn btn-danger">Видалити</a>
            <a href="javascript:history.back()" class="btn btn-success">Відмінити</a>
        </div>
    </div>
</main>