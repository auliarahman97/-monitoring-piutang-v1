<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<div class="card shadow-sm">

    <div class="card-header d-flex justify-content-between align-items-center">

        <h3 class="card-title">
            <i class="fas fa-users-cog mr-2"></i>
            Pengaturan User
        </h3>

        <a href="<?= site_url('pengaturan/user/create') ?>"
        class="btn btn-primary">

            <i class="fas fa-plus"></i>
            Tambah User

        </a>

    </div>

    <div class="card-body">

        <div class="table-responsive">

            <table id="tableUser" class="table table-bordered table-hover">

                <thead class="thead-light">

                    <tr>
                        <th width="5%">No</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Aktivitas Terakhir</th>
                        <th width="15%">Aksi</th>
                    </tr>

                </thead>

                <tbody>

                <?php foreach ($users as $i => $user): ?>

                    <tr>

                        <td><?= $i + 1 ?></td>

                        <td>
                            <strong><?= esc($user->username) ?></strong>
                            <br>
                            <small class="text-muted">
                                <i class="far fa-calendar-alt"></i>
                                Bergabung
                                <?= esc($user->created_at->format('d M Y')) ?>
                            </small>
                        </td>

                        <td>

                            <small class="text-muted">
                                <?= esc($user->email) ?>
                            </small>

                        </td>

                        <td class="text-center">

                            <?php
                            $role = $user->getGroups()[0] ?? '';

                            $badge = match ($role) {
                                'admin'    => 'badge-danger',
                                'petugas'  => 'badge-primary',
                                'pimpinan' => 'badge-success',
                                default    => 'badge-secondary',
                            };
                            ?>

                            <span class="badge <?= $badge ?> px-3 py-2">
                                <?= ucfirst($role) ?>
                            </span>

                        </td>

                        <?php $aktivitas = aktivitasTerakhir($user->last_active); ?>

                        <td>
                            <span
                                data-toggle="tooltip"
                                title="<?= esc($aktivitas['tooltip']) ?>"
                            >
                                <i class="far fa-clock text-muted"></i>
                                <?= esc($aktivitas['tanggal']) ?>
                            </span>

                            <?php if ($aktivitas['jam']) : ?>
                                <br>

                                <small class="text-muted">
                                    <?= esc($aktivitas['jam']) ?>
                                </small>
                            <?php endif ?>
                        </td>

                        <td class="text-center">

                            <a href="<?= site_url('pengaturan/user/' . $user->id . '/edit') ?>"
                            class="btn btn-warning btn-sm"
                            title="Edit User">

                                <i class="fas fa-edit"></i>

                            </a>

                            <a
                                href="<?= site_url('pengaturan/user/' . $user->id . '/reset-password') ?>"
                                class="btn btn-danger btn-sm">

                                <i class="fas fa-key"></i>

                            </a>

                        </td>

                    </tr>

                <?php endforeach ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>

<?= $this->include('user/_script') ?>

<?= $this->endSection() ?>