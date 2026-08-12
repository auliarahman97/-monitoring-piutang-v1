<table class="summary">

    <tr>

        <td width="20%">

            <div class="summary-label">
                JUMLAH PIUTANG
            </div>

            <div class="summary-value">

                <?= number_format(
                    (int) (
                        $summary[
                            'jumlah_piutang'
                        ] ?? 0
                    ),
                    0,
                    ',',
                    '.'
                ) ?>

            </div>

        </td>


        <td width="20%">

            <div class="summary-label">
                TOTAL TAGIHAN
            </div>

            <div class="summary-value">

                Rp
                <?= number_format(
                    (float) (
                        $summary[
                            'total_tagihan'
                        ] ?? 0
                    ),
                    0,
                    ',',
                    '.'
                ) ?>

            </div>

        </td>


        <td width="20%">

            <div class="summary-label">
                TOTAL PEMBAYARAN
            </div>

            <div class="summary-value">

                Rp
                <?= number_format(
                    (float) (
                        $summary[
                            'total_pembayaran'
                        ] ?? 0
                    ),
                    0,
                    ',',
                    '.'
                ) ?>

            </div>

        </td>


        <td width="20%">

            <div class="summary-label">
                TOTAL SISA
            </div>

            <div class="summary-value">

                Rp
                <?= number_format(
                    (float) (
                        $summary[
                            'total_sisa'
                        ] ?? 0
                    ),
                    0,
                    ',',
                    '.'
                ) ?>

            </div>

        </td>


        <td width="20%">

            <div class="summary-label">
                TOTAL MENUNGGAK
            </div>

            <div class="summary-value">

                Rp
                <?= number_format(
                    (float) (
                        $summary[
                            'total_menunggak'
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