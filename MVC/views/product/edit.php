<?php
/** @var array $categories
 * @var array $materials
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
                <label for="material_id" class="form-label">Матеріал:</label>
                <select class="form-control" id="material_id" name="material_id" required>
                    <?php foreach ($materials as $material) : ?>
                        <option value="<?= $material->id ?>" <?= $material->id == $product->material_id ? 'selected' : '' ?>>
                            <?= htmlspecialchars($material->name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3 row">
                <div class="col">
                    <label for="length_cm" class="form-label">Довжина (см):</label>
                    <input type="number" step="1" id="length_cm" name="length_cm" value="<?= htmlspecialchars($product->length_cm) ?>" class="form-control" list="length-options" required>
                    <datalist id="length-options">
                        <option value="50">
                        <option value="60">
                        <option value="100">
                        <option value="120">
                        <option value="150">
                        <option value="160">
                        <option value="180">
                        <option value="200">
                    </datalist>
                </div>

                <div class="col">
                    <label for="width_cm" class="form-label">Ширина (см):</label>
                    <input type="number" step="1" id="width_cm" name="width_cm" value="<?= htmlspecialchars($product->width_cm) ?>" class="form-control" list="width-options" required>
                    <datalist id="width-options">
                        <option value="30">
                        <option value="50">
                        <option value="60">
                        <option value="800">
                        <option value="90">
                        <option value="120">
                    </datalist>
                </div>

                <div class="col">
                    <label for="height_cm" class="form-label">Товщина (см):</label>
                    <input type="number" step="1" id="height_cm" name="height_cm" value="<?= htmlspecialchars($product->height_cm) ?>" class="form-control" list="height-options" required>
                    <datalist id="height-options">
                        <option value="5">
                        <option value="8">
                        <option value="10">
                        <option value="12">
                    </datalist>
                </div>
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
