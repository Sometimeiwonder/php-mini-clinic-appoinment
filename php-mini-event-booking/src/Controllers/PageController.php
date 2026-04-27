<?php
namespace App\Controllers;

class PageController {
    public function home(array $config, array $shows) {
        return [
            'title' => $config['app']['name'],
            'company' => $config['app']['company'],
            'shows' => $shows
        ];
    }
}