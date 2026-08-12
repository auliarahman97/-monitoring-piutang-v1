<?php if(session()->getFlashdata('success')): ?>

<div class="alert alert-success alert-dismissible">

<button type="button"
class="close"
data-dismiss="alert">

×

</button>

<?= session()->getFlashdata('success') ?>

</div>

<?php endif; ?>


<?php if(session()->getFlashdata('error')): ?>

<div class="alert alert-danger">

<?= session()->getFlashdata('error') ?>

</div>

<?php endif; ?>