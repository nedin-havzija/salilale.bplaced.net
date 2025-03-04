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
    $name = trim($_POST["name"]);
    $description = trim($_POST["description"]);
    $price = $_POST["price"];
    $category = trim($_POST["category"]);
    $image = null;

    // Handle file upload
    if (!empty($_FILES["image"]["name"])) {
        $targetDir = "uploads/";
    
        // Ensure the directory exists
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0775, true); // Creates the directory if it doesn't exist
        }
    
        $fileName = basename($_FILES["image"]["name"]);
        $targetFilePath = $targetDir . $fileName;
    
        // Move uploaded file
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
            $image = $targetFilePath; // Save the correct file path
        } else {
            echo "<p style='color:red;'>File upload failed.</p>";
            $image = null; // Prevent inserting an invalid path
        }
    }

    // Insert food item into DB
    $stmt = $conn->prepare("INSERT INTO food_items (name, description, price, category, image) VALUES (:name, :description, :price, :category, :image)");
    $stmt->execute([
        "name" => $name,
        "description" => $description,
        "price" => $price,
        "category" => $category,
        "image" => $image
    ]);
}

// Fetch all food items
$stmt = $conn->query("SELECT * FROM food_items ORDER BY category");
$food_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Manage Food</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        body { background-color: #f4f4f4; }
        .container { max-width: 900px; margin: 50px auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1); }
        .table img { width: 50px; height: 50px; object-fit: cover; border-radius: 5px; }
        .btn-delete { background: red; color: white; border: none; padding: 6px 12px; border-radius: 4px; transition: 0.3s; }
        .btn-delete:hover { background: darkred; }
        .btn-add { background: green; color: white; border: none; padding: 8px 14px; border-radius: 4px; transition: 0.3s; }
        .btn-add:hover { background: darkgreen; }
        input, select, button { margin-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center mb-4">Admin Panel</h2>
        <div class="d-flex justify-content-between">
            <a href="index.php" class="btn btn-secondary">← Back to Home</a>
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>

        <h3 class="mt-4">Add Food Item</h3>
        <form method="POST" enctype="multipart/form-data" class="row g-3">
            <div class="col-md-6">
                <input type="text" name="name" class="form-control" placeholder="Food Name" required>
            </div>
            <div class="col-md-6">
                <input type="text" name="description" class="form-control" placeholder="Description" required>
            </div>
            <div class="col-md-4">
                <input type="number" name="price" class="form-control" placeholder="Price" step="0.01" required>
            </div>
            <div class="col-md-4">
                <select name="category" class="form-select" required>
                    <option value="Breakfast">Breakfast</option>
                    <option value="Lunch">Lunch</option>
                    <option value="Dinner">Dinner</option>
                </select>
            </div>
            <div class="col-md-4">
                <input type="file" name="image" class="form-control" accept="image/*">
            </div>
            <div class="col-md-12 text-center">
                <button type="submit" name="add_food" class="btn btn-add">Add Food</button>
            </div>
        </form>

        <h3 class="mt-4">Existing Food Items</h3>
        <table class="table table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Category</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($food_items as $item): ?>
                    <tr>
                    <td><img src="<?= htmlspecialchars($item["image"]) ?>" alt="Food" onerror="this.src='assets/images/no-image.png';"></td>
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
            </tbody>
        </table>
    </div>
</body>
</html>
