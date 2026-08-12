<?php

declare(strict_types=1);

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

abstract class BaseController extends Controller
{
    /**
     * ----------------------------------------------------------------------
     * Global Helpers
     * ----------------------------------------------------------------------
     */

    protected $helpers = [
        'auth',
        'application',
        'format',
        'menu',
        'badge',
        'status',
        'laporan',
        'url',
        'form',
    ];

    /**
     * ----------------------------------------------------------------------
     * Initialize Controller
     * ----------------------------------------------------------------------
     */

    public function initController(
        RequestInterface $request,
        ResponseInterface $response,
        LoggerInterface $logger
    ): void {
        parent::initController($request, $response, $logger);
    }

    /**
     * ----------------------------------------------------------------------
     * Validation
     * ----------------------------------------------------------------------
     */

    /**
     * Validate request using Model validation rules.
     *
     * @param array<string, mixed> $data
     */
    protected function validateModel(
        object $model,
        array $data = []
    ): bool {
        return $this->validate(
            $model->rules(),
            $model->messages(),
            $data
        );
    }

    /**
     * ----------------------------------------------------------------------
     * Authentication
     * ----------------------------------------------------------------------
     */

    /**
     * Mengambil ID user yang sedang login.
     */
    protected function currentUserId(): ?int
    {
        $user = auth()->user();

        return $user?->id;
    }

    /**
     * ----------------------------------------------------------------------
     * Redirect
     * ----------------------------------------------------------------------
     */

    /**
     * Redirect back with old input.
     */
    protected function backWithInput(): RedirectResponse
    {
        return redirect()
            ->back()
            ->withInput();
    }

    /**
     * Redirect with success message.
     */
    protected function redirectSuccess(
        string $route,
        string $message
    ): RedirectResponse {
        return redirect()
            ->to(site_url($route))
            ->with('success', $message);
    }

    /**
     * Redirect with error message.
     */
    protected function redirectError(
        string $route,
        string $message
    ): RedirectResponse {
        return redirect()
            ->to(site_url($route))
            ->with('error', $message);
    }
}