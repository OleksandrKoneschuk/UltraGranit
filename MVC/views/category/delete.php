<?php
/** @var array $category */
?>
<main>
    <div class="container text-center mt-5">
        <div class="alert alert-danger" role="alert">
            <h3>Видалення категорії "<?= htmlspecialchars($category->name) ?>"</h3>
            <p>Ви дійсно хочете видалити категорію?</p>
            <a href="/category/delete/<?=$category->id?>/yes" class="btn btn-danger">Видалити</a>
            <a href="javascript:history.back()" class="btn btn-success">Відмінити</a>
        </div>
    </div>
</main>
