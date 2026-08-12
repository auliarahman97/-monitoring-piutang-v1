<?php

declare(strict_types=1);

$name        = $name ?? '';
$label       = $label ?? '';
$value       = field_old($name, $value ?? '');
$rows        = $rows ?? 3;
$required    = $required ?? false;
$placeholder = $placeholder ?? '';
$readonly    = $readonly ?? false;
$helpText    = $helpText ?? null;
$class       = trim($class ?? '');

?>

<div class="form-group">

    <label
        for="<?= esc($name) ?>"
        class="form-label font-weight-bold">

        <?= esc($label) ?>

        <?= field_required($required) ?>

    </label>

    <textarea
        id="<?= esc($name) ?>"
        name="<?= esc($name) ?>"
        rows="<?= (int) $rows ?>"
        placeholder="<?= esc($placeholder) ?>"
        class="form-control <?= field_invalid($name) ?> <?= esc($class) ?>"
        <?= $readonly ? 'readonly' : '' ?>><?= esc($value) ?></textarea>

    <?php if ($error = field_error($name)) : ?>
        <div class="invalid-feedback d-block">
            <?= esc($error) ?>
        </div>
    <?php endif; ?>

    <?php if ($helpText !== null) : ?>
        <small class="form-text text-muted">
            <?= esc($helpText) ?>
        </small>
    <?php endif; ?>

</div>