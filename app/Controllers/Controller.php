<?php

namespace App\Controllers;

use App\Core\Application;

/**
 * Base Controller
 * 
 * All controllers extend this class.
 * Provides access to the application instance and common helpers.
 */
abstract class Controller
{
    protected Application $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Render a view.
     */
    protected function view(string $view, array $data = []): void
    {
        view($view, $data);
    }

    /**
     * Redirect to a URL.
     */
    protected function redirect(string $url): void
    {
        redirect($url);
    }

    /**
     * Redirect back with errors and old input.
     */
    protected function backWithErrors(array $errors, array $input = []): void
    {
        session()->flashErrors($errors);
        if (!empty($input)) {
            session()->flashInput($input);
        }
        back();
    }

    /**
     * Send a JSON response.
     */
    protected function json(array $data, int $status = 200): void
    {
        json_response($data, $status);
    }
}
