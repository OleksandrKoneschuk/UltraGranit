<?php
/** @var int $errorCode */
/**@var string $header */
/** @var string $errorMessage */
?>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<title>Помилка</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="icon" href="/public/uploads/logo3.png" type="image/x-icon">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<main>
    <div class="container text-center mt-5">
        <div class="alert alert-danger d-flex align-items-center justify-content-center mx-auto" style="width: 40%;" role="alert">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                 class="bi bi-exclamation-triangle-fill me-3" viewBox="0 0 16 16">
                <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5m.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2"/>
            </svg>
            <div class="text-start">
                <h1 class="fs-1">Error <?= htmlspecialchars($errorCode) ?></h1>
                <h4 class="fs-1"><?= htmlspecialchars($header) ?></h4>
                <p class="mb-2"><?= htmlspecialchars($errorMessage) ?></p>
            </div>
        </div>
        <a href="javascript:history.back()" class="btn btn-secondary">Повернутися на попередню сторінку</a>
    </div>
</main>