<?php
namespace App\Controllers;
use App\Support\Response;

class ShowController {
    public function list(array $shows) {
        Response::json(200, ['message' => 'Available Shows', 'data' => $shows]);
    }
    public function head() {
        http_response_code(200); exit;
    }
}