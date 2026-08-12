<?php

$title   = $title ?? '';
$icon    = $icon ?? null;
$content = $content ?? '';

?>

<div class="card shadow-sm">

    <div class="card-header bg-primary text-white">

        <h5 class="mb-0">

            <?php if ($icon): ?>

                <i class="<?= esc($icon) ?> mr-2"></i>

            <?php endif; ?>

            <?= esc($title) ?>

        </h5>

    </div>

    <div class="card-body">

        <?= $content ?>

    </div>

</div>