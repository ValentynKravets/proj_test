<?php
require 'db.php';
// Підключення до бази

// Обробка запитів від форми
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    // Search
    if (isset($_POST['search'])) {
        $search = trim($_POST['search']);
        $query = "SELECT * FROM leads WHERE phone LIKE :search OR email LIKE :search OR first_name LIKE :search OR last_name LIKE :search ORDER BY created_at DESC";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':search' => "%$search%"]);
        $leads = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'leads' => $leads]);
        exit;
    }

    // Edit info in database
    if (isset($_POST['edit'])) {
        $id = $_POST['id'];
        $firstName = trim($_POST['first_name']);
        $lastName = trim($_POST['last_name']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $selectService = trim($_POST['select_service']);
        $selectPrice = trim($_POST['select_price']);
        $comments = trim($_POST['comments']);

        // Validate phone (US format: +1 followed by 10 digits)
        if (!preg_match('/^\+1\d{10}$/', $phone)) {
            $response = ['success' => false, 'message' => 'Invalid phone format (e.g., +12025550123)'];
            echo json_encode($response);
            exit;
        }

        // Validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $response = ['success' => false, 'message' => 'Invalid email format'];
            echo json_encode($response);
            exit;
        }

        $data = [
            ':id' => $id,
            ':first_name' => $firstName,
            ':last_name' => $lastName,
            ':email' => $email,
            ':phone' => $phone,
            ':select_service' => $selectService,
            ':select_price' => $selectPrice,
            ':comments' => $comments
        ];
        $stmt = $pdo->prepare("
            UPDATE leads SET first_name = :first_name, last_name = :last_name, email = :email, phone = :phone,
            select_service = :select_service, select_price = :select_price, comments = :comments WHERE id = :id
        ");
        $stmt->execute($data);

        // Повертаємо оновлений запис із бази
        $stmt = $pdo->prepare("SELECT * FROM leads WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $updatedLead = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode(['success' => true, 'message' => 'Data updated', 'lead' => $updatedLead]);
        exit;
    }

    // Delete info
    if (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM leads WHERE id = :id");
        $stmt->execute([':id' => $id]);
        echo json_encode(['success' => true, 'message' => 'Record deleted']);
        exit;
    }

    // Full database clear
    if (isset($_POST['clear'])) {
        $pdo->exec("DELETE FROM leads");
        echo json_encode(['success' => true, 'message' => 'Database cleared']);
        exit;
    }
}

// Load all records
try {
    $stmt = $pdo->query("SELECT * FROM leads ORDER BY created_at DESC");
    $leads = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Помилка отримання даних: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leads List</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
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

        .search-container {
            margin-bottom: 20px;
        }

        .search-container input {
            padding: 8px;
            width: 300px;
        }

        .btn {
            padding: 5px 10px;
            cursor: pointer;
        }

        .btn-danger {
            background-color: #dc3545;
            color: white;
            border: none;
        }

        .btn-edit {
            background-color: #007bff;
            color: white;
            border: none;
        }

        .btn-clear {
            background-color: #ff851b;
            color: white;
            border: none;
            margin-top: 10px;
        }

        .editable:hover {
            background-color: #f9f9f9;
            cursor: pointer;
        }
    </style>
</head>

<body>

    <h2>Leads List</h2>

    <div class="search-container">
        <input type="text" id="search" placeholder="Search by phone, email, first name, or last name">
        <button class="btn" onclick="searchLeads()">Search</button>
    </div>

    <table id="leads-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Service</th>
                <th>Price</th>
                <th>Comments</th>
                <th>FBP</th>
                <th>Google Tag</th>
                <th>Country</th>
                <th>City</th>
                <th>IP</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="leads-body">
            <?php if (!empty($leads)): ?>
                <?php foreach ($leads as $lead): ?>
                    <tr data-id="<?= htmlspecialchars($lead['id']) ?>">
                        <td><?= htmlspecialchars($lead['id']) ?></td>
                        <td class="editable" data-field="first_name" contenteditable><?= htmlspecialchars($lead['first_name']) ?></td>
                        <td class="editable" data-field="last_name" contenteditable><?= htmlspecialchars($lead['last_name']) ?></td>
                        <td class="editable" data-field="email" contenteditable><?= htmlspecialchars($lead['email']) ?></td>
                        <td class="editable" data-field="phone" contenteditable><?= htmlspecialchars($lead['phone']) ?></td>
                        <td class="editable" data-field="select_service" contenteditable><?= htmlspecialchars($lead['select_service'] ?? '') ?></td>
                        <td class="editable" data-field="select_price" contenteditable><?= htmlspecialchars($lead['select_price'] ?? '') ?></td>
                        <td class="editable" data-field="comments" contenteditable><?= htmlspecialchars($lead['comments'] ?? '') ?></td>
                        <td><?= htmlspecialchars($lead['fbp'] ?? '') ?></td>
                        <td><?= htmlspecialchars($lead['ggl'] ?? '') ?></td>
                        <td><?= htmlspecialchars($lead['country'] ?? 'Unknown') ?></td>
                        <td><?= htmlspecialchars($lead['city'] ?? 'Unknown') ?></td>
                        <td><?= htmlspecialchars($lead['ip'] ?? 'Unknown') ?></td>
                        <td><?= htmlspecialchars($lead['created_at']) ?></td>
                        <td>
                            <button class="btn btn-edit" onclick="editLead(<?= $lead['id'] ?>)">Save</button>
                            <button class="btn btn-danger" onclick="deleteLead(<?= $lead['id'] ?>)">Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="15">No leads</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <button class="btn btn-clear" onclick="clearLeads()">Clear Database</button>

    <script>
        function searchLeads() {
            const searchValue = document.getElementById('search').value;
            fetch('view_leads.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'search=' + encodeURIComponent(searchValue)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateTable(data.leads);
                    }
                })
                .catch(error => console.error('Error searching:', error));
        }

        function updateTable(leads) {
            const tbody = document.getElementById('leads-body');
            tbody.innerHTML = '';
            if (leads.length === 0) {
                tbody.innerHTML = '<tr><td colspan="15">No leads</td></tr>';
                return;
            }
            leads.forEach(lead => {
                const row = document.createElement('tr');
                row.dataset.id = lead.id;
                row.innerHTML = `
                    <td>${lead.id}</td>
                    <td class="editable" data-field="first_name" contenteditable>${lead.first_name}</td>
                    <td class="editable" data-field="last_name" contenteditable>${lead.last_name}</td>
                    <td class="editable" data-field="email" contenteditable>${lead.email}</td>
                    <td class="editable" data-field="phone" contenteditable>${lead.phone}</td>
                    <td class="editable" data-field="select_service" contenteditable>${lead.select_service || ''}</td>
                    <td class="editable" data-field="select_price" contenteditable>${lead.select_price || ''}</td>
                    <td class="editable" data-field="comments" contenteditable>${lead.comments || ''}</td>
                    <td>${lead.fbp || ''}</td>
                    <td>${lead.ggl || ''}</td>
                    <td>${lead.country || 'Unknown'}</td>
                    <td>${lead.city || 'Unknown'}</td>
                    <td>${lead.ip || 'Unknown'}</td>
                    <td>${lead.created_at}</td>
                    <td>
                        <button class="btn btn-edit" onclick="editLead(${lead.id})">Save</button>
                        <button class="btn btn-danger" onclick="deleteLead(${lead.id})">Delete</button>
                    </td>
                `;
                tbody.appendChild(row);
            });
        }

        function editLead(id) {
            const row = document.querySelector(`tr[data-id="${id}"]`);
            const data = {
                id: id,
                first_name: row.querySelector('[data-field="first_name"]').textContent.trim(),
                last_name: row.querySelector('[data-field="last_name"]').textContent.trim(),
                email: row.querySelector('[data-field="email"]').textContent.trim(),
                phone: row.querySelector('[data-field="phone"]').textContent.trim(),
                select_service: row.querySelector('[data-field="select_service"]').textContent.trim(),
                select_price: row.querySelector('[data-field="select_price"]').textContent.trim(),
                comments: row.querySelector('[data-field="comments"]').textContent.trim()
            };

            fetch('view_leads.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        edit: true,
                        ...data
                    }).toString()
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        // Оновлюємо рядок у таблиці з повними даними
                        const updatedRow = row;
                        updatedRow.querySelector('[data-field="first_name"]').textContent = data.lead.first_name;
                        updatedRow.querySelector('[data-field="last_name"]').textContent = data.lead.last_name;
                        updatedRow.querySelector('[data-field="email"]').textContent = data.lead.email;
                        updatedRow.querySelector('[data-field="phone"]').textContent = data.lead.phone;
                        updatedRow.querySelector('[data-field="select_service"]').textContent = data.lead.select_service || '';
                        updatedRow.querySelector('[data-field="select_price"]').textContent = data.lead.select_price || '';
                        updatedRow.querySelector('[data-field="comments"]').textContent = data.lead.comments || '';
                        updatedRow.cells[8].textContent = data.lead.fbp || ''; // FBP
                        updatedRow.cells[9].textContent = data.lead.ggl || ''; // Google Tag
                        updatedRow.cells[10].textContent = data.lead.country || 'Unknown';
                        updatedRow.cells[11].textContent = data.lead.city || 'Unknown';
                        updatedRow.cells[12].textContent = data.lead.ip || 'Unknown';
                        updatedRow.cells[13].textContent = data.lead.created_at;
                    } else {
                        alert(data.message || 'Editing error');
                    }
                })
                .catch(error => console.error('Editing error:', error));
        }

        function deleteLead(id) {
            if (!confirm('Are you sure you want to delete this record?')) return;
            fetch('view_leads.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'delete=' + id
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.querySelector(`tr[data-id="${id}"]`).remove();
                        alert(data.message);
                    }
                })
                .catch(error => console.error('Deletion error:', error));
        }

        function clearLeads() {
            if (!confirm('Are you sure you want to clear the entire database?')) return;
            fetch('view_leads.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'clear=true'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateTable([]);
                        alert(data.message);
                    }
                })
                .catch(error => console.error('Clearing error:', error));
        }
    </script>

</body>

</html>