<?php
/** @var array $categories
 * @var stdClass $product */

$this->Title = 'Редагування продукту';
?>

<main>
    <div class="d-flex justify-content-center" style="margin: 2%">

        <form id="edit-product-form" action="" method="post" enctype="multipart/form-data">
            <?php if (!empty($error_message)) : ?>
                <div class="alert alert-danger d-flex p-2 align-items-center" role="alert">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                         class="bi bi-exclamation-triangle-fill" viewBox="0 0 16 16">
                        <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5m.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2"/>
                    </svg>
                    <div style="padding: 0 5px 0 5px">
                        <?= $error_message ?>
                    </div>
                </div>
            <?php endif; ?>

            <h2>Редагування продукту</h2>
            <div class="mb-3">
                <label for="name" class="form-label">Назва продукту</label>
                <input type="text" class="form-control" name="name" id="name" value="<?= htmlspecialchars($product->name) ?>" required>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Ціна</label>
                <input type="number" class="form-control" name="price" id="price" value="<?= htmlspecialchars($product->price) ?>" required>
            </div>
            <div class="mb-3">
                <label for="category_id" class="form-label">Категорія</label>
                <select class="form-control" name="category_id" id="category_id" required>
                    <?php foreach ($categories as $category) : ?>
                        <option value="<?= $category['id'] ?>" <?= $category['id'] == $product->category_id ? 'selected' : '' ?>>
                            <?= htmlspecialchars($category['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="short_description" class="form-label">Короткий опис</label>
                <textarea class="form-control ckeditor" name="short_description" id="short_description"><?= htmlspecialchars($product->short_description) ?></textarea>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Опис</label>
                <textarea class="form-control ckeditor" name="description" id="description"><?= htmlspecialchars($product->description) ?></textarea>
            </div>
            <div class="mb-3">
                <label for="visible" class="form-label">Видимість</label>
                <input type="checkbox" name="visible" id="visible" <?= $product->visible ? 'checked' : '' ?>>
            </div>
            <div class="mb-3">
                <label for="main_photo" class="form-label">Основне фото</label>
                <input class="form-control" type="file" name="main_photo" id="main_photo" accept="image/*">
                <?php if (!empty($product->main_photo)) : ?>
                    <img src="/<?= htmlspecialchars($product->main_photo) ?>" class="img-thumbnail mt-2" style="width: 150px;">
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label for="additional_photos" class="form-label">Додаткові фото</label>
                <input class="form-control" type="file" name="additional_photos[]" id="additional_photos" accept="image/*" multiple>
                <?php if (!empty($product->additional_photos)) : ?>
                    <?php foreach ($product->additional_photos as $photo) : ?>
                        <img src="/<?= htmlspecialchars($photo) ?>" class="img-thumbnail mt-2" style="width: 150px;">
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div>
                <button type="submit" class="btn btn-primary">Зберегти</button>
            </div>
        </form>
    </div>
</main>

<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let editors = document.querySelectorAll('.ckeditor');
        editors.forEach(editor => {
            ClassicEditor
                .create(editor)
                .catch(error => {
                    console.error(error);
                });
        });

        document.getElementById('edit-product-form').addEventListener('submit', function (event) {
            let shortDescription = ClassicEditor.instances.short_description.getData();
            let description = ClassicEditor.instances.description.getData();

            if (!shortDescription.trim() || !description.trim()) {
                alert('Будь ласка, заповніть всі обов\'язкові поля.');
                event.preventDefault();
            }
        });
    });
</script>
