<?php
$this->Title = 'Додавання категорії';
?>
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
<main>
    <div class="d-flex justify-content-center" style="margin: 2%">
        <form action="" method="post" enctype="multipart/form-data">
            <h2>Додававння категорії</h2>
            <div class="mb-3">
                <label for="name" class="form-label">Назва категорії</label>
                <input type="text" class="form-control" name="name" id="name">
            </div>
            <div class="mb-3">
                <label for="file" class="form-label">Фото для категорії</label>
                <input class="form-control" type="file" name="file" id="file" accept="image/jpeg">
            </div>
            <div>
                <button class="btn btn-primary">Додати</button>
            </div>
        </form>
    </div>

</main>
