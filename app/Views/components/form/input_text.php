<?php

declare(strict_types=1);

/**
 * -----------------------------------------------------------------------------
 * Form Component : Input Text
 * -----------------------------------------------------------------------------
 *
 * Required:
 * - name
 * - label
 *
 * Optional:
 * - value
 * - type
 * - placeholder
 * - required
 * - readonly
 * - disabled
 * - maxlength
 * - autocomplete
 * - icon
 * - helpText
 * - class
 */

$name         = $name ?? '';
$label        = $label ?? '';
$type         = $type ?? 'text';
$value        = (string) field_old($name, $value ?? '');
$placeholder  = $placeholder ?? '';
$required     = $required ?? false;
$readonly     = $readonly ?? false;
$disabled     = $disabled ?? false;
$maxlength    = $maxlength ?? null;
$autocomplete = $autocomplete ?? 'off';
$icon         = $icon ?? null;
$helpText     = $helpText ?? null;
$class        = trim($class ?? '');
$minlength = $minlength ?? null;
$inputmode = $inputmode ?? null;
$pattern   = $pattern ?? null;
$autofocus = $autofocus ?? false;
$spellcheck = $spellcheck ?? false;

?>

<div class="form-group">

    <label
        for="<?= esc($name) ?>"
        class="form-label font-weight-bold">

        <?= esc($label) ?>

        <?= field_required($required) ?>

    </label>

    <?php if ($icon !== null) : ?>
        <div class="input-group">

            <div class="input-group-prepend">

                <span class="input-group-text">

                    <i class="<?= esc($icon) ?>"></i>

                </span>

            </div>
    <?php endif; ?>

    <input
        type="<?= esc($type) ?>"
        id="<?= esc($name) ?>"
        name="<?= esc($name) ?>"
        value="<?= esc($value) ?>"
        placeholder="<?= esc($placeholder) ?>"
        autocomplete="<?= esc($autocomplete) ?>"
        class="form-control <?= field_invalid($name) ?> <?= esc($class) ?>"
        <?= $maxlength ? 'maxlength="' . (int) $maxlength . '"' : '' ?>
        <?= $readonly ? 'readonly' : '' ?>
        <?= $disabled ? 'disabled' : '' ?>
        <?= $minlength ? 'minlength="' . (int) $minlength . '"' : '' ?>
        <?= $inputmode ? 'inputmode="' . esc($inputmode) . '"' : '' ?>
        <?= $pattern ? 'pattern="' . esc($pattern) . '"' : '' ?>
        <?= $autofocus ? 'autofocus' : '' ?>
        spellcheck="<?= $spellcheck ? 'true' : 'false' ?>">

    <?php if ($icon !== null) : ?>
        </div>
    <?php endif; ?>

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