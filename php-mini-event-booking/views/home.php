<?php /** @var array $data */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($data['title']) ?></title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; margin: 20px; max-width: 800px; }
        .show-card { margin-bottom: 16px; padding: 12px; border: 1px solid #ccc; border-radius: 8px; }
        .status-open { color: green; font-weight: bold; }
        .status-full { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <h1><?= htmlspecialchars($data['title']) ?></h1>

    <ul>
        <li><strong>Company Name:</strong> <?= htmlspecialchars($data['company']) ?></li>
    </ul>

    <h2>Available Shows</h2>
    <?php foreach ($data['shows'] as $show): ?>
        <div class="show-card">
            <p><strong>Show Name:</strong> <?= htmlspecialchars($show['name']) ?></p>
            <p><strong>Location:</strong> <?= htmlspecialchars($show['location']) ?></p>
            <p><strong>Date:</strong> <?= htmlspecialchars($show['date']) ?></p>
            <p><strong>Total Tickets:</strong> <?= htmlspecialchars((string)$show['total_tickets']) ?></p>
            <p><strong>Available Tickets:</strong> <?= htmlspecialchars((string)$show['available_tickets']) ?></p>
            <p><strong>Status:</strong> 
                <?php if ($show['available_tickets'] > 0): ?>
                    <span class="status-open">Available</span>
                <?php else: ?>
                    <span class="status-full">Sold Out</span>
                <?php endif; ?>
            </p>
        </div>
    <?php endforeach; ?>

    <h2>API Endpoints</h2>
    <ul>
        <li><code>GET /shows</code> - Lấy danh sách sự kiện</li>
        <li><code>HEAD /shows</code> - Kiểm tra header API</li>
        <li><code>POST /bookings</code> - Gửi thông tin đặt vé (JSON)</li>
        <li><code>OPTIONS /bookings</code> - Kiểm tra các phương thức được phép</li>
    </ul>
</body>
</html>