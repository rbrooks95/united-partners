<?php

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
        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');

        // Validate input
        $errors = [];
        if (empty($name)) {
            $errors[] = "Name is required.";
        }
        if (empty($phone)) {
            $errors[] = "Phone number is required.";
        }
        if (empty($email)) {
            $errors[] = "Email is required.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format.";
        }

        if (empty($errors)) {
            $stmt = $pdo->prepare("INSERT INTO contacts (name, phone, email) VALUES (:name, :phone, :email)");
            $stmt->execute(['name' => $name, 'phone' => $phone, 'email' => $email]);
            $success = "Contact added successfully!";
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
<!-- Link to Google Fonts for better typography -->
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
<style>
    /* Reset some default styles */
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }
    body {
        font-family: 'Roboto', sans-serif;
        background-color: #f0f4f8;
        color: #333;
        line-height: 1.6;
        padding: 20px;
    }
    .container {
        max-width: 1200px;
        margin: 0 auto;
    }
    h1 {
        text-align: center;
        margin-bottom: 30px;
        color: #2c3e50;
    }
    /* Form Styling */
    .form-container {
        background: #ffffff;
        padding: 25px;
        border-radius: 8px;
        margin-bottom: 40px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .form-container h2 {
        margin-bottom: 20px;
        color: #34495e;
        text-align: center;
    }
    .form-group {
        display: flex;
        flex-direction: column;
        margin-bottom: 15px;
    }
    label {
        margin-bottom: 5px;
        font-weight: 600;
    }
    input[type="text"], input[type="email"], input[type="tel"] {
        padding: 12px;
        border: 1px solid #bdc3c7;
        border-radius: 4px;
        transition: border-color 0.3s;
    }
    input[type="text"]:focus, input[type="email"]:focus, input[type="tel"]:focus {
        border-color: #2980b9;
        outline: none;
    }
    .error, .success {
        padding: 15px;
        border-radius: 4px;
        margin-bottom: 20px;
        text-align: center;
    }
    .error {
        background-color: #e74c3c;
        color: #ffffff;
    }
    .success {
        background-color: #2ecc71;
        color: #ffffff;
    }
    input[type="submit"] {
        background: #2980b9;
        color: #ffffff;
        border: none;
        padding: 15px;
        border-radius: 4px;
        cursor: pointer;
        width: 100%;
        font-size: 16px;
        transition: background 0.3s;
    }
    input[type="submit"]:hover {
        background: #1f6391;
    }
    /* Table Styling */
    .table-container {
        overflow-x: auto;
        padding: 0 10px; /* Add horizontal padding for small screens */
    }
    table {
        width: 100%;
        border-collapse: collapse;
        background: #ffffff;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        min-width: 600px; /* Ensure table doesn't shrink too much on larger screens */
    }
    th, td {
        padding: 15px;
        text-align: left;
        border-bottom: 1px solid #ecf0f1;
    }
    th {
        background-color: #34495e;
        color: #ffffff;
        font-weight: 700;
    }
    tr:nth-child(even) {
        background-color: #f9f9f9;
    }
    .delete-btn {
        background: #e74c3c;
        border: none;
        color: #ffffff;
        padding: 8px 12px;
        border-radius: 4px;
        cursor: pointer;
        transition: background 0.3s;
    }
    .delete-btn:hover {
        background: #c0392b;
    }
    /* Responsive Design */
    @media (max-width: 768px) {
        body {
            padding: 10px;
        }
        .form-container {
            padding: 20px;
        }
        .table-container {
            padding: 0 5px;
        }
        th, td {
            padding: 10px;
        }
        input[type="submit"] {
            padding: 12px;
            font-size: 14px;
        }
        .delete-btn {
            padding: 6px 10px;
            font-size: 14px;
        }
    }
</style>
</head>
<body>
<div class="container">
    <h1>United Partners</h1>
    
    <!-- Display Success or Error Messages -->
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!empty($errors)) {
            echo '<div class="error"><ul>';
            foreach ($errors as $error) {
                echo '<li>' . htmlspecialchars($error) . '</li>';
            }
            echo '</ul></div>';
        } elseif (isset($success)) {
            echo '<div class="success">' . htmlspecialchars($success) . '</div>';
        }
    }
    ?>
    
    <div class="form-container">
        <h2>Add Contact</h2>
        <form action="" method="POST">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" name="name" id="name" required placeholder="John Doe">
            </div>
            <div class="form-group">
                <label for="phone">Phone Number:</label>
                <input type="tel" name="phone" id="phone" required placeholder="123-456-7890">
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" required placeholder="john@example.com">
            </div>
            <input type="submit" value="Add Contact">
        </form>
    </div>
    
    <div class="table-container">
        <h2>Existing Contacts</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Phone Number</th>
                    <th>Email</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
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
                        <form action="" method="POST" onsubmit="return confirm('Are you sure you want to delete this contact?');">
                            <input type="hidden" name="_method" value="DELETE">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($contact['id']) ?>">
                            <input class="delete-btn" type="submit" value="Delete">
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
