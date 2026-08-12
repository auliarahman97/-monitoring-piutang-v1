<table class="summary">

    <tr>

        <!-- ======================================================
             JUMLAH TRANSAKSI
             ====================================================== -->

        <td width="16.66%">

            <div class="summary-label">
                Jumlah Transaksi
            </div>

            <div class="summary-value">

                <?= number_format(
                    (int) (
                        $summary[
                            'jumlah_transaksi'
                        ] ?? 0
                    ),
                    0,
                    ',',
                    '.'
                ) ?>

            </div>

        </td>


        <!-- ======================================================
             TRANSAKSI VALID
             ====================================================== -->

        <td width="16.66%">

            <div class="summary-label">
                Transaksi Valid
            </div>

            <div class="summary-value">

                <?= number_format(
                    (int) (
                        $summary[
                            'jumlah_valid'
                        ] ?? 0
                    ),
                    0,
                    ',',
                    '.'
                ) ?>

            </div>

        </td>


        <!-- ======================================================
             DIBATALKAN
             ====================================================== -->

        <td width="16.66%">

            <div class="summary-label">
                Dibatalkan
            </div>

            <div class="summary-value">

                <?= number_format(
                    (int) (
                        $summary[
                            'jumlah_dibatalkan'
                        ] ?? 0
                    ),
                    0,
                    ',',
                    '.'
                ) ?>

            </div>

        </td>


        <!-- ======================================================
             TOTAL PEMBAYARAN VALID
             ====================================================== -->

        <td width="16.66%">

            <div class="summary-label">
                Total Pembayaran Valid
            </div>

            <div class="summary-value">

                Rp
                <?= number_format(
                    (float) (
                        $summary[
                            'total_pembayaran_valid'
                        ] ?? 0
                    ),
                    0,
                    ',',
                    '.'
                ) ?>

            </div>

        </td>


        <!-- ======================================================
             TOTAL ALOKASI BUNGA
             ====================================================== -->

        <td width="16.66%">

            <div class="summary-label">
                Total Alokasi Bunga
            </div>

            <div class="summary-value">

                Rp
                <?= number_format(
                    (float) (
                        $summary[
                            'total_alokasi_bunga'
                        ] ?? 0
                    ),
                    0,
                    ',',
                    '.'
                ) ?>

            </div>

        </td>


        <!-- ======================================================
             TOTAL ALOKASI POKOK
             ====================================================== -->

        <td width="16.66%">

            <div class="summary-label">
                Total Alokasi Pokok
            </div>

            <div class="summary-value">

                Rp
                <?= number_format(
                    (float) (
                        $summary[
                            'total_alokasi_pokok'
                        ] ?? 0
                    ),
                    0,
                    ',',
                    '.'
                ) ?>

            </div>

        </td>

    </tr>

</table>