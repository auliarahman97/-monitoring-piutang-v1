<?php

declare(strict_types=1);

$name         = $name ?? '';
$label        = $label ?? '';
$value        = field_old($name, $value ?? '');
$required     = $required ?? false;
$readonly     = $readonly ?? false;
$disabled     = $disabled ?? false;
$helpText     = $helpText ?? null;
$class        = trim($class ?? '');

?>

<div class="form-group">

    <label
        for="<?= esc($name) ?>"
        class="form-label font-weight-bold">

        <?= esc($label) ?>

        <?= field_required($required) ?>

    </label>

    <input
        type="date"
        id="<?= esc($name) ?>"
        name="<?= esc($name) ?>"
        value="<?= esc($value) ?>"
        class="form-control <?= field_invalid($name) ?> <?= esc($class) ?>"
        <?= $readonly ? 'readonly' : '' ?>
        <?= $disabled ? 'disabled' : '' ?>>

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