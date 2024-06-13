<?php
/** @var array $category */
$this->Title = 'Редагування категорії';
?>

<main>
    <div class="d-flex justify-content-center" style="margin: 2%">
        <form action="" method="post" enctype="multipart/form-data">
            <h2>Редагування категорії</h2>
            <div class="mb-3">
                <label for="name" class="form-label">Назва категорії</label>
                <input type="text" class="form-control" name="name" id="name" value="<?=htmlspecialchars($category->name)?>"  required>
            </div>
            <div class="mb-3">
                <label for="file" class="form-label">Фото для категорії</label>
                <input class="form-control" type="file" name="file" id="file" accept="image/jpeg">
                <?php if (!empty($category->photo)): ?>
                    <img src="/files/category/<?= htmlspecialchars($category->photo) ?>" alt="Фото категорії" class="img-thumbnail mt-3" style="max-width: 200px;">
                <?php endif; ?>
            </div>
            <div>
                <button class="btn btn-primary">Зберегти</button>
            </div>
        </form>
    </div>
</main>