<?php
session_start();
include "config.php";

// Check if admin is logged in
if (!isset($_SESSION["admin"]) || $_SESSION["admin"] !== true) {
    header("Location: index.php"); // Redirect to home if not admin
    exit;
}

// Handle deleting food items
if (isset($_POST["delete_id"])) {
    $stmt = $conn->prepare("DELETE FROM food_items WHERE id = :id");
    $stmt->execute(["id" => $_POST["delete_id"]]);
}

// Handle adding food items
if (isset($_POST["add_food"])) {
    $name = $_POST["name"];
    $description = $_POST["description"];
    $price = $_POST["price"];
    $category = $_POST["category"];
    
    $stmt = $conn->prepare("INSERT INTO food_items (name, description, price, category) VALUES (:name, :description, :price, :category)");
    $stmt->execute([
        "name" => $name,
        "description" => $description,
        "price" => $price,
        "category" => $category
    ]);
}

// Fetch all food items
$stmt = $conn->query("SELECT * FROM food_items");
$food_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Manage Food</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .container { max-width: 800px; margin: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        .btn { padding: 8px 12px; border: none; cursor: pointer; }
        .btn-delete { background: red; color: white; }
        .btn-add { background: green; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Admin Panel</h2>
        <a href="index.php">← Back to Home</a> | <a href="logout.php">Logout</a>

        <h3>Add Food Item</h3>
        <form method="POST">
            <input type="text" name="name" placeholder="Food Name" required>
            <input type="text" name="description" placeholder="Description" required>
            <input type="number" name="price" placeholder="Price" step="0.01" required>
            <input type="text" name="category" placeholder="Category" required>
            <button type="submit" name="add_food" class="btn btn-add">Add Food</button>
        </form>

        <h3>Existing Food Items</h3>
        <table>
            <tr>
                <th>Name</th>
                <th>Description</th>
                <th>Price</th>
                <th>Category</th>
                <th>Action</th>
            </tr>
            <?php foreach ($food_items as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item["name"]) ?></td>
                    <td><?= htmlspecialchars($item["description"]) ?></td>
                    <td>$<?= number_format($item["price"], 2) ?></td>
                    <td><?= htmlspecialchars($item["category"]) ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="delete_id" value="<?= $item["id"] ?>">
                            <button type="submit" class="btn btn-delete">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
