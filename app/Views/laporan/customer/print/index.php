<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <title>
        <?= esc($title ?? 'Laporan Customer') ?>
    </title>

    <?= $this->include(
        'laporan/customer/print/_style'
    ) ?>

</head>

<body>

    <!-- HEADER -->

    <?= $this->include(
        'laporan/customer/print/_header'
    ) ?>


    <!-- CUSTOMER -->

    <?= $this->include(
        'laporan/customer/print/_customer'
    ) ?>


    <!-- SUMMARY -->

    <?= $this->include(
        'laporan/customer/print/_summary'
    ) ?>


    <!-- PIUTANG -->

    <?= $this->include(
        'laporan/customer/print/_piutang'
    ) ?>


    <!-- PEMBAYARAN -->

    <?= $this->include(
        'laporan/customer/print/_pembayaran'
    ) ?>


    <!-- FOOTER -->

    <?= $this->include(
        'laporan/customer/print/_footer'
    ) ?>

</body>

</html>