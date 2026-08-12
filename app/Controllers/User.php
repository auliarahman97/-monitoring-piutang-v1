<?php

declare(strict_types=1);

namespace App\Controllers;

use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\Shield\Entities\User as UserEntity;
use CodeIgniter\Shield\Models\UserModel;

/**
 * Controller User
 *
 * Mengelola pengguna aplikasi beserta role dan reset password.
 */
class User extends BaseController
{
    /**
     * --------------------------------------------------------------------------
     * Models
     * --------------------------------------------------------------------------
     */

    /**
     * Model pengguna (CodeIgniter Shield).
     */
    protected UserModel $userModel;

    /**
     * --------------------------------------------------------------------------
     * Validation Rules
     * --------------------------------------------------------------------------
     */

    /**
     * Validation rules untuk menambahkan user.
     */
    protected array $createRules = [
        'username' => [
            'label' => 'Username',
            'rules' => 'required|min_length[3]|max_length[30]|is_unique[users.username]',
        ],
        'email' => [
            'label' => 'Email',
            'rules' => 'required|valid_email|is_unique[auth_identities.secret]',
        ],
        'password' => [
            'label' => 'Password',
            'rules' => 'required|min_length[8]',
        ],
        'password_confirm' => [
            'label' => 'Konfirmasi Password',
            'rules' => 'required|matches[password]',
        ],
        'group' => [
            'label' => 'Role',
            'rules' => 'required|in_list[admin,petugas,pimpinan]',
        ],
    ];

    /**
     * Validation rules untuk mengubah user.
     */
    protected array $updateRules = [
        'username' => [
            'label' => 'Username',
            'rules' => 'required|min_length[3]|max_length[30]',
        ],
        'email' => [
            'label' => 'Email',
            'rules' => 'required|valid_email',
        ],
        'group' => [
            'label' => 'Role',
            'rules' => 'required|in_list[admin,petugas,pimpinan]',
        ],
    ];

    /**
     * Validation rules untuk reset password.
     */
    protected array $resetPasswordRules = [
        'password' => [
            'label' => 'Password Baru',
            'rules' => 'required|min_length[8]',
        ],
        'password_confirm' => [
            'label' => 'Konfirmasi Password',
            'rules' => 'required|matches[password]',
        ],
    ];

    /**
     * --------------------------------------------------------------------------
     * Constants
     * --------------------------------------------------------------------------
     */

    private const MODE_CREATE = 'create';
    private const MODE_EDIT   = 'edit';

    private const ROUTE_INDEX = 'pengaturan/user';

    /**
     * --------------------------------------------------------------------------
     * Constructor
     * --------------------------------------------------------------------------
     */

    public function __construct()
    {
        // Authorization
        authorize(['admin']);

        // Models
        $this->userModel = new UserModel();
    }

    /**
     * --------------------------------------------------------------------------
     * CRUD Methods
     * --------------------------------------------------------------------------
     */

    /**
     * Menampilkan daftar seluruh user.
     */
    public function index(): string
    {
        return view('user/index', [
            'title' => 'Pengaturan User',
            'users' => $this->userModel
                ->withGroups()
                ->withIdentities()
                ->findAll(),
        ]);
    }

    /**
     * Menampilkan form tambah user.
     */
    public function create(): string
    {
        return view('user/create', [
            'title' => 'Tambah User',
            'mode'  => self::MODE_CREATE,
            'user'  => null,
        ]);
    }

    /**
     * Menyimpan user baru.
     */
    public function store(): RedirectResponse
    {
        if (! $this->validate($this->createRules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $user = new UserEntity([
            'username' => $this->request->getPost('username'),
            'email'    => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
        ]);

        if (! $this->userModel->save($user)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->userModel->errors());
        }

        $user = $this->userModel->find($this->userModel->getInsertID());

        $user->addGroup(
            $this->request->getPost('group')
        );

        return redirect()
            ->to(site_url(self::ROUTE_INDEX))
            ->with(
                'success',
                'User berhasil ditambahkan.'
            );
    }

    /**
     * Menampilkan form edit user.
     *
     * @throws PageNotFoundException
     */
    public function edit(int $id): string
    {
        return view('user/edit', [
            'title' => 'Edit User',
            'mode'  => self::MODE_EDIT,
            'user'  => $this->findUserOrFail($id),
        ]);
    }

    /**
     * Memperbarui data user.
     *
     * @throws PageNotFoundException
     */
    public function update(int $id): RedirectResponse
    {
        $user = $this->findUserOrFail($id);

        $rules = $this->updateRules;

        $rules['username']['rules'] .=
            '|is_unique[users.username,id,' . $id . ']';

        $identity = $user->getEmailIdentity();

        if ($identity !== null) {
            $rules['email']['rules'] .=
                '|is_unique[auth_identities.secret,id,' . $identity->id . ']';
        }

        if (! $this->validate($rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $db = db_connect();

        $db->transStart();

        $user->fill([
            'username' => $this->request->getPost('username'),
            'email'    => $this->request->getPost('email'),
        ]);

        if (! $this->userModel->save($user)) {
            $db->transRollback();

            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->userModel->errors());
        }

        $user->syncGroups(
            $this->request->getPost('group')
        );

        $db->transComplete();

        if (! $db->transStatus()) {
            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    'Gagal memperbarui data user.'
                );
        }

        return redirect()
            ->to(site_url(self::ROUTE_INDEX))
            ->with(
                'success',
                'User berhasil diperbarui.'
            );
    }

    /**
     * --------------------------------------------------------------------------
     * Password Methods
     * --------------------------------------------------------------------------
     */

    /**
     * Menampilkan form reset password.
     *
     * @throws PageNotFoundException
     */
    public function resetPasswordForm(int $id): string
    {
        return view('user/reset_password', [
            'title' => 'Reset Password',
            'user'  => $this->findUserOrFail($id),
        ]);
    }

    /**
     * Memproses reset password user.
     *
     * @throws PageNotFoundException
     */
    public function resetPassword(int $id): RedirectResponse
    {
        $user = $this->findUserOrFail($id);

        if (! $this->validate($this->resetPasswordRules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $user->fill([
            'password' => $this->request->getPost('password'),
        ]);

        if (! $this->userModel->save($user)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->userModel->errors());
        }

        return redirect()
            ->to(site_url(self::ROUTE_INDEX))
            ->with(
                'success',
                'Password berhasil diperbarui.'
            );
    }

    /**
     * --------------------------------------------------------------------------
     * Private Methods
     * --------------------------------------------------------------------------
     */

    /**
     * Mengambil user beserta group dan identity.
     *
     * @throws PageNotFoundException
     */
    private function findUserOrFail(int $id): UserEntity
    {
        $user = $this->userModel
            ->withGroups()
            ->withIdentities()
            ->find($id);

        if ($user === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        return $user;
    }
}