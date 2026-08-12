<table class="customer-table">

    <!-- ============================================================
         CUSTOMER NAME & CODE
    ============================================================= -->

    <tr>

        <td class="customer-label">
            Nama Customer
        </td>

        <td class="customer-separator">
            :
        </td>

        <td>

            <?= esc(
                $customer['nama']
                ?? '-'
            ) ?>

        </td>


        <td class="customer-label">
            Kode Customer
        </td>

        <td class="customer-separator">
            :
        </td>

        <td>

            <?= esc(
                $customer['kode_customer']
                ?? '-'
            ) ?>

        </td>

    </tr>


    <!-- ============================================================
         PHONE & STATUS
    ============================================================= -->

    <tr>

        <td class="customer-label">
            No. HP
        </td>

        <td class="customer-separator">
            :
        </td>

        <td>

            <?= esc(
                $customer['no_hp']
                ?? '-'
            ) ?>

        </td>


        <td class="customer-label">
            Status
        </td>

        <td class="customer-separator">
            :
        </td>

        <td>

            <?= ! empty(
                $customer['deleted_at']
            )

                ? 'Tidak Aktif'

                : 'Aktif'
            ?>

        </td>

    </tr>


    <!-- ============================================================
         ADDRESS
    ============================================================= -->

    <tr>

        <td class="customer-label">
            Alamat
        </td>

        <td class="customer-separator">
            :
        </td>

        <td colspan="4">

            <?= ! empty(
                $customer['alamat']
            )

                ? nl2br(
                    esc(
                        $customer['alamat']
                    )
                )

                : '-'
            ?>

        </td>

    </tr>

</table>