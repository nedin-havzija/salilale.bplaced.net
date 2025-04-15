<?php
session_start();
include __DIR__ . '/config/config.php';

// Check if admin is logged in
if (!isset($_SESSION["admin"]) || $_SESSION["admin"] !== true) {
    header("Location: index.php");
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

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0775, true);
        }

        $fileName = basename($_FILES["image"]["name"]);
        $targetFilePath = $targetDir . $fileName;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
            $image = $targetFilePath;
        } else {
            echo "<p style='color:red;'>File upload failed.</p>";
            $image = null;
        }
    }

    $stmt = $conn->prepare("INSERT INTO food_items (name, description, price, category, image) VALUES (:name, :description, :price, :category, :image)");
    $stmt->execute([
        "name" => $name,
        "description" => $description,
        "price" => $price,
        "category" => $category,
        "image" => $image
    ]);
}

// Handle editing food items
if (isset($_POST["edit_food"])) {
    $id = $_POST["edit_id"];
    $name = trim($_POST["edit_name"]);
    $description = trim($_POST["edit_description"]);
    $price = $_POST["edit_price"];
    $category = trim($_POST["edit_category"]);

    $stmt = $conn->prepare("UPDATE food_items 
                            SET name = :name, description = :description, price = :price, category = :category 
                            WHERE id = :id");
    $stmt->execute([
        "name" => $name,
        "description" => $description,
        "price" => $price,
        "category" => $category,
        "id" => $id
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
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Admin Panel - Manage Food</title>

  <!-- Bootstrap CSS -->
  <link 
    rel="stylesheet" 
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
  >

  <style>
    body {
        background-color: #f8f9fa;
        font-family: Arial, sans-serif;
    }
    .container {
        max-width: 900px;
        margin: auto;
        background: #ffffff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    h2, h3 {
        text-align: center;
        font-weight: bold;
    }
    .btn {
        border-radius: 5px;
        font-size: 14px;
        padding: 8px 12px;
    }
    .d-flex {
        margin-bottom: 20px;
    }
    .table {
        margin-top: 20px;
    }
    .table img {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 5px;
    }
    .table th, .table td {
        vertical-align: middle;
        text-align: center;
    }
    form .form-control, .form-select {
        border-radius: 5px;
    }
    .btn-success:hover {
        background-color: #218838;
    }
    .btn-danger:hover {
        background-color: #c82333;
    }
    .btn-primary:hover {
        background-color: #0056b3;
    }
    td img {
        max-width: 100px;
        height: auto;
        display: block;
        margin: auto;
    }
    td form {
        display: inline-block;
    }
    td .btn {
        margin: 3px;
    }
    @media (max-width: 768px) {
        .table img {
            width: 60px;
            height: 60px;
        }
    }
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
        <button type="submit" name="add_food" class="btn btn-success">Add Food</button>
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
            <td>
              <img 
                src="<?= htmlspecialchars($item["image"]) ?>" 
                alt="Food" 
                onerror="this.src='assets/images/no-image.png';"
              >
            </td>
            <td><?= htmlspecialchars($item["name"]) ?></td>
            <td><?= htmlspecialchars($item["description"]) ?></td>
            <td>$<?= number_format($item["price"], 2) ?></td>
            <td><?= htmlspecialchars($item["category"]) ?></td>
            <td>
              <!-- Delete form -->
              <form method="POST" style="display:inline;">
                <input type="hidden" name="delete_id" value="<?= $item["id"] ?>">
                <button type="submit" class="btn btn-danger">Delete</button>
              </form>

              <!-- Edit button triggers modal -->
              <button 
                type="button" 
                class="btn btn-primary" 
                onclick='showEditForm(
                  <?= json_encode($item["id"]) ?>, 
                  <?= json_encode($item["name"]) ?>, 
                  <?= json_encode($item["description"]) ?>, 
                  <?= json_encode($item["price"]) ?>, 
                  <?= json_encode($item["category"]) ?>
                )'
              >
                Edit
              </button>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- Bootstrap Modal for Editing -->
  <div 
    class="modal fade" 
    id="editModal" 
    tabindex="-1" 
    aria-labelledby="editModalLabel" 
    aria-hidden="true"
  >
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="POST" class="needs-validation" novalidate>
          <input type="hidden" name="edit_id" id="edit_id" />
          <div class="modal-header">
            <h5 class="modal-title" id="editModalLabel">Edit Food Item</h5>
            <button 
              type="button" 
              class="btn-close" 
              data-bs-dismiss="modal" 
              aria-label="Close"
            ></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label for="edit_name" class="form-label">Name</label>
              <input 
                type="text" 
                name="edit_name" 
                id="edit_name" 
                class="form-control" 
                required
              >
            </div>
            <div class="mb-3">
              <label for="edit_description" class="form-label">Description</label>
              <input 
                type="text" 
                name="edit_description" 
                id="edit_description" 
                class="form-control" 
                required
              >
            </div>
            <div class="mb-3">
              <label for="edit_price" class="form-label">Price</label>
              <input 
                type="number" 
                name="edit_price" 
                id="edit_price" 
                class="form-control" 
                step="0.01" 
                required
              >
            </div>
            <div class="mb-3">
              <label for="edit_category" class="form-label">Category</label>
              <select 
                name="edit_category" 
                id="edit_category" 
                class="form-select" 
                required
              >
                <option value="Breakfast">Breakfast</option>
                <option value="Lunch">Lunch</option>
                <option value="Dinner">Dinner</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button 
              type="button" 
              class="btn btn-secondary" 
              data-bs-dismiss="modal"
            >
              Close
            </button>
            <button 
              type="submit" 
              name="edit_food" 
              class="btn btn-primary"
            >
              Save Changes
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Bootstrap Bundle JS (enables the modal) -->
  <script 
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js">
  </script>

  <script>
    // This function populates the modal inputs and shows the modal
    function showEditForm(id, name, description, price, category) {
      // Get references to modal fields
      var editId = document.getElementById('edit_id');
      var editName = document.getElementById('edit_name');
      var editDescription = document.getElementById('edit_description');
      var editPrice = document.getElementById('edit_price');
      var editCategory = document.getElementById('edit_category');

      // Fill the modal fields
      editId.value = id;
      editName.value = name;
      editDescription.value = description;
      editPrice.value = price;
      editCategory.value = category;

      // Show the Bootstrap modal
      var editModal = new bootstrap.Modal(document.getElementById('editModal'));
      editModal.show();
    }
  </script>

</body>
</html>
