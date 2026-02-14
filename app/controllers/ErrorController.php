<?php
/**
 * Error Controller
 * 
 * Handles error pages (404, 500, etc.)
 * 
 * @author Senior PHP Developer
 * @version 1.0.0
 */

class ErrorController extends Controller
{
    /**
     * Display 404 Not Found page
     * 
     * @return void
     */
    public function notFound(): void
    {
        $data = [
            'title' => '404 - Page Not Found'
        ];

        $this->view('errors/404', $data, null);
    }

    /**
     * Display 403 Forbidden page
     * 
     * @return void
     */
    public function forbidden(): void
    {
        $data = [
            'title' => '403 - Forbidden'
        ];

        $this->view('errors/403', $data, null);
    }

    /**
     * Display 500 Internal Server Error page
     * 
     * @return void
     */
    public function serverError(): void
    {
        $data = [
            'title' => '500 - Internal Server Error'
        ];

        $this->view('errors/500', $data, null);
    }
}
