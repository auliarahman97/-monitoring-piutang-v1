<table class="summary-table">

    <tr>

        <td>

            <div class="summary-label">
                Total Piutang
            </div>

            <div class="summary-value">

                Rp <?= number_format(
                    (float) (
                        $summary[
                            'total_piutang'
                        ] ?? 0
                    ),
                    0,
                    ',',
                    '.'
                ) ?>

            </div>

        </td>


        <td>

            <div class="summary-label">
                Total Tagihan
            </div>

            <div class="summary-value">

                Rp <?= number_format(
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


        <td>

            <div class="summary-label">
                Total Pembayaran
            </div>

            <div class="summary-value">

                Rp <?= number_format(
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


        <td>

            <div class="summary-label">
                Sisa Tagihan
            </div>

            <div class="summary-value">

                Rp <?= number_format(
                    (float) (
                        $summary[
                            'sisa_tagihan'
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