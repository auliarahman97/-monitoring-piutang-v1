<?php

declare(strict_types=1);

/**
 * -----------------------------------------------------------------------------
 * Form Component : Select
 * -----------------------------------------------------------------------------
 *
 * Required:
 * - name
 * - label
 * - options
 *
 * Optional:
 * - value
 * - required
 * - disabled
 * - placeholder
 * - helpText
 * - class
 */

$name        = $name ?? '';
$label       = $label ?? '';
$value       = (string) field_old($name, $value ?? '');
$options     = $options ?? [];
$required    = $required ?? false;
$disabled    = $disabled ?? false;
$placeholder = $placeholder ?? null;
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

    <select
        id="<?= esc($name) ?>"
        name="<?= esc($name) ?>"
        class="form-control <?= field_invalid($name) ?> <?= esc($class) ?>"
        <?= $required ? 'required' : '' ?>
        <?= $disabled ? 'disabled' : '' ?>>

        <?php if ($placeholder !== null) : ?>

            <option
                value=""
                disabled
                <?= $value === '' ? 'selected' : '' ?>>

                <?= esc($placeholder) ?>

            </option>

        <?php endif; ?>

        <?php foreach ($options as $key => $text) : ?>

            <option
                value="<?= esc((string) $key) ?>"
                <?= $value === (string) $key ? 'selected' : '' ?>>

                <?= esc($text) ?>

            </option>

        <?php endforeach; ?>

    </select>

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