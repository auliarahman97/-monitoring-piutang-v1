<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <title>
        <?= esc($title ?? 'Laporan Piutang') ?>
    </title>

    <?= $this->include(
        'laporan/piutang/pdf/_style'
    ) ?>

</head>


<body>

    <?= $this->include(
        'laporan/piutang/pdf/_header'
    ) ?>


    <?= $this->include(
        'laporan/piutang/pdf/_summary'
    ) ?>


    <?= $this->include(
        'laporan/piutang/pdf/_table'
    ) ?>


    <?= $this->include(
        'laporan/piutang/pdf/_footer'
    ) ?>

</body>

</html>