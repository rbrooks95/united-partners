<?php
// Database connection variables (adjust as needed)
$dbhost = getenv('DB_HOST') ?: 'db';
$dbuser = getenv('DB_USER') ?: 'myuser';
$dbpass = getenv('DB_PASSWORD') ?: 'mypassword';
$dbname = getenv('DB_NAME') ?: 'mydb';

try {
    $dsn = "pgsql:host=$dbhost;dbname=$dbname";
    $pdo = new PDO($dsn, $dbuser, $dbpass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (Exception $e) {
    die("Error connecting to the database: " . $e->getMessage());
}

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['_method']) && $_POST['_method'] === 'DELETE') {
        // Simulate DELETE
        $idToDelete = (int)$_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM contacts WHERE id = :id");
        $stmt->execute(['id' => $idToDelete]);
    } else {
        // Handle create (POST)
        $name = $_POST['name'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $email = $_POST['email'] ?? '';

        // Validate input (simple example)
        if (!empty($name) && !empty($phone) && !empty($email)) {
            $stmt = $pdo->prepare("INSERT INTO contacts (name, phone, email) VALUES (:name, :phone, :email)");
            $stmt->execute(['name' => $name, 'phone' => $phone, 'email' => $email]);
        }
    }
}

// Fetch current contacts (GET)
$stmt = $pdo->query("SELECT id, name, phone, email FROM contacts ORDER BY id ASC");
$contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>United Partners</title>
<style>
    body {
        font-family: Arial, sans-serif;
        margin: 30px;
        background: #f8f8f8;
        color: #333;
    }
    h1 {
        color: #333;
        text-align: center;
    }
    form {
        margin-bottom: 20px;
        background: #fff;
        padding: 15px;
        border-radius: 8px;
        max-width: 400px;
        margin: 20px auto;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    form h2 {
        margin-top: 0;
        text-align: center;
    }
    label {
        display: block;
        margin-bottom: 8px;
        font-weight: bold;
    }
    input[type="text"], input[type="email"], input[type="tel"] {
        width: 100%;
        padding: 8px;
        margin-bottom: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }
    input[type="submit"] {
        padding: 10px 15px;
        background: #007bff;
        color: #fff;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        width: 100%;
        font-size: 16px;
    }
    input[type="submit"]:hover {
        background: #0056b3;
    }
    table {
        border-collapse: collapse;
        width: 90%;
        margin: 0 auto;
        background: #fff;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    th, td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #ccc;
    }
    th {
        background: #f1f1f1;
    }
    .delete-btn {
        background: #dc3545;
        border: none;
        color: #fff;
        padding: 6px 10px;
        border-radius: 4px;
        cursor: pointer;
    }
    .delete-btn:hover {
        background: #c82333;
    }
    .container {
        max-width: 800px;
        margin: 0 auto;
    }
</style>
</head>
<body>
<div class="container">
<h1>Contact Manager</h1>

<form action="" method="POST">
    <h2>Add Contact</h2>
    <label for="name">Name:</label>
    <input type="text" name="name" id="name" required placeholder="John Doe">
    
    <label for="phone">Phone Number:</label>
    <input type="tel" name="phone" id="phone" required placeholder="123-456-7890">
    
    <label for="email">Email:</label>
    <input type="email" name="email" id="email" required placeholder="john@example.com">
    
    <input type="submit" value="Add Contact">
</form>

<h2 style="text-align:center;">Existing Contacts</h2>
<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Phone Number</th>
        <th>Email</th>
        <th>Delete</th>
    </tr>
    <?php if (empty($contacts)): ?>
    <tr>
        <td colspan="5" style="text-align:center;">No contacts found.</td>
    </tr>
    <?php else: ?>
    <?php foreach ($contacts as $contact): ?>
    <tr>
        <td><?= htmlspecialchars($contact['id']) ?></td>
        <td><?= htmlspecialchars($contact['name']) ?></td>
        <td><?= htmlspecialchars($contact['phone']) ?></td>
        <td><?= htmlspecialchars($contact['email']) ?></td>
        <td>
            <form action="" method="POST" style="display:inline;">
                <input type="hidden" name="_method" value="DELETE">
                <input type="hidden" name="id" value="<?= $contact['id'] ?>">
                <input class="delete-btn" type="submit" value="X">
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
    <?php endif; ?>
</table>
</div>
</body>
</html>
