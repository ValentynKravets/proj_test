<?php
require 'db.php'; // Підключення до бази

try {
    $stmt = $pdo->query("SELECT * FROM leads ORDER BY created_at DESC");
    $leads = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Помилка отримання даних: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Список заявок</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>

    <h2>Список заявок</h2>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Ім'я</th>
                <th>Прізвище</th>
                <th>Email</th>
                <th>Телефон</th>
                <th>Послуга</th>
                <th>Ціна</th>
                <th>Коментар</th>
                <th>FBP</th>
                <th>Google Tag</th>
                <th>Дата</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($leads)): ?>
                <?php foreach ($leads as $lead): ?>
                    <tr>
                        <td><?= htmlspecialchars($lead['id']) ?></td>
                        <td><?= htmlspecialchars($lead['first_name']) ?></td>
                        <td><?= htmlspecialchars($lead['last_name']) ?></td>
                        <td><?= htmlspecialchars($lead['email']) ?></td>
                        <td><?= htmlspecialchars($lead['phone']) ?></td>
                        <td><?= htmlspecialchars($lead['select_service']) ?></td>
                        <td><?= htmlspecialchars($lead['select_price']) ?></td>
                        <td><?= htmlspecialchars($lead['comments']) ?></td>
                        <td><?= htmlspecialchars($lead['fbp']) ?></td>
                        <td><?= htmlspecialchars($lead['ggl']) ?></td>
                        <td><?= htmlspecialchars($lead['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="11">Немає заявок</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</body>

</html>