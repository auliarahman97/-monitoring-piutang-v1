<!DOCTYPE html>

<html lang="id">

<head>

    <meta charset="UTF-8">

    <title>
        Laporan Pembayaran
    </title>

    <?= $this->include(
        'laporan/pembayaran/pdf/_style'
    ) ?>

</head>


<body>

    <?= $this->include(
        'laporan/pembayaran/pdf/_header'
    ) ?>


    <?= $this->include(
        'laporan/pembayaran/pdf/_summary'
    ) ?>


    <?= $this->include(
        'laporan/pembayaran/pdf/_table'
    ) ?>


    <?= $this->include(
        'laporan/pembayaran/pdf/_footer'
    ) ?>

</body>

</html>