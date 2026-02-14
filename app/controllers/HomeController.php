<?php
/**
 * Home Controller
 * 
 * Handles the home page and public-facing pages
 * 
 * @author Senior PHP Developer
 * @version 1.0.0
 */

class HomeController extends Controller
{
    /**
     * Display home page
     * 
     * @return void
     */
    public function index(): void
    {
        $data = [
            'title' => 'Welcome to ' . APP_NAME,
            'version' => APP_VERSION
        ];

        $this->view('home/index', $data);
    }

    /**
     * Display about page
     * 
     * @return void
     */
    public function about(): void
    {
        $data = [
            'title' => 'About Us'
        ];

        $this->view('home/about', $data);
    }

    /**
     * Display contact page
     * 
     * @return void
     */
    public function contact(): void
    {
        $data = [
            'title' => 'Contact Us'
        ];

        $this->view('home/contact', $data);
    }
}
