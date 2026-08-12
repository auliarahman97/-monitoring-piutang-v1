<?php

declare(strict_types=1);

$backUrl    = $backUrl ?? '';
$submitText = $submitText ?? 'Simpan';
$backText   = $backText ?? 'Kembali';

?>

<div class="d-flex justify-content-between">

    <a
        href="<?= site_url($backUrl) ?>"
        class="btn btn-secondary">

        <i class="fas fa-arrow-left mr-1"></i>

        <?= esc($backText) ?>

    </a>

    <button
        type="submit"
        class="btn btn-primary">

        <i class="fas fa-save mr-1"></i>

        <?= esc($submitText) ?>

    </button>

</div>