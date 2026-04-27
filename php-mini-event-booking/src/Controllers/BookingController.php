<?php
namespace App\Controllers;
use App\Support\Response;

class BookingController {
    public function book(array $shows, array $config) {
        $headers = function_exists('getallheaders') ? getallheaders() : [];
        $contentType = $headers['Content-Type'] ?? $_SERVER['CONTENT_TYPE'] ?? '';

        if (!str_contains(strtolower($contentType), 'application/json')) {
            Response::json(415, ['error' => 'Unsupported Media Type']);
        }

        $payload = json_decode(file_get_contents('php://input'), true);
        if (!is_array($payload)) {
            Response::json(400, ['error' => 'Bad Request']);
        }

        $showId = $payload['show_id'] ?? null;
        $customer = trim($payload['customer_name'] ?? '');
        $tickets = (int)($payload['ticket_count'] ?? 0);

        if (!$showId || !$customer || $tickets <= 0) {
            Response::json(422, ['error' => 'Unprocessable Content', 'message' => 'Missing fields']);
        }

        if ($tickets > $config['app']['max_tickets']) {
            Response::json(422, ['error' => 'Limit Exceeded', 'message' => "Max {$config['app']['max_tickets']} tickets"]);
        }

        // Logic check vé giống bài lab...
        // ... (Tìm showId, check available_tickets)
        
        Response::json(201, [
            'message' => 'Booking successful',
            'data' => ['booking_id' => time(), 'customer' => $customer, 'tickets' => $tickets]
        ]);
    }
    public function options() {
        http_response_code(204); header('Allow: POST, OPTIONS'); exit;
    }
}