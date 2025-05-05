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

// Handle deleting users
if (isset($_POST["delete_user_id"])) {
    $stmt = $conn->prepare("DELETE FROM users WHERE id = :id");
    $stmt->execute(["id" => $_POST["delete_user_id"]]);
}

// Handle adding food items
if (isset($_POST["add_food"])) {
    $name = trim($_POST["name"]);
    $description = trim($_POST["description"]);
    $price = $_POST["price"];
    $category = trim($_POST["category"]);
    $image = null;

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

    $stmt = $conn->prepare("UPDATE food_items SET name = :name, description = :description, price = :price, category = :category WHERE id = :id");
    $stmt->execute([
        "name" => $name,
        "description" => $description,
        "price" => $price,
        "category" => $category,
        "id" => $id
    ]);
}

// Fetch all food items
$food_items = $conn->query("SELECT * FROM food_items ORDER BY category")->fetchAll(PDO::FETCH_ASSOC);

// Fetch all users
$users = $conn->query("SELECT id, username, email, role FROM users ORDER BY role DESC, username ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Admin Panel</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <style>
    body {
      background-color: #f8f9fa;
      font-family: Arial, sans-serif;
    }
    .container {
      max-width: 1000px;
      margin: auto;
      background: #fff;
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
    .form-control, .form-select {
      border-radius: 5px;
    }
    .btn-delete-small {
      height: 20px;
      line-height: 1;
      padding: 0 10px;
      font-size: 12px;
    }
    .password-input {
      max-width: 300px;
      min-width: 180px;
    }
    td form {
      display: inline-block;
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
  <h2>Admin-Bereich</h2>
  <div class="d-flex justify-content-between mb-3">
    <a href="index.php" class="btn btn-secondary">← Zurück</a>
    <a href="logout.php" class="btn btn-danger">Abmelden</a>
  </div>

  <h3>Speise hinzufügen</h3>
  <form method="POST" enctype="multipart/form-data" class="row g-3">
    <div class="col-md-6">
      <input type="text" name="name" class="form-control" placeholder="Name" required>
    </div>
    <div class="col-md-6">
      <input type="text" name="description" class="form-control" placeholder="Beschreibung" required>
    </div>
    <div class="col-md-4">
      <input type="number" name="price" class="form-control" placeholder="Preis" step="0.01" required>
    </div>
    <div class="col-md-4">
      <select name="category" class="form-select" required>
        <option value="Breakfast">Frühstück</option>
        <option value="Lunch">Mittagessen</option>
        <option value="Dinner">Abendessen</option>
      </select>
    </div>
    <div class="col-md-4">
      <input type="file" name="image" class="form-control" accept="image/*">
    </div>
    <div class="col-12 text-center">
      <button type="submit" name="add_food" class="btn btn-success">Hinzufügen</button>
    </div>
  </form>

  <h3 class="mt-4">Speisen</h3>
  <table class="table table-striped">
    <thead class="table-dark">
      <tr>
        <th>Bild</th>
        <th>Name</th>
        <th>Beschreibung</th>
        <th>Preis</th>
        <th>Kategorie</th>
        <th>Aktion</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($food_items as $item): ?>
      <tr>
        <td><img src="<?= htmlspecialchars($item["image"]) ?>" onerror="this.src='assets/images/no-image.png';"></td>
        <td><?= htmlspecialchars($item["name"]) ?></td>
        <td><?= htmlspecialchars($item["description"]) ?></td>
        <td>€<?= number_format($item["price"], 2) ?></td>
        <td><?= htmlspecialchars($item["category"]) ?></td>
        <td>
          <form method="POST" style="display:inline;">
            <input type="hidden" name="delete_id" value="<?= $item["id"] ?>">
            <button type="submit" class="btn btn-danger btn-sm">Löschen</button>
          </form>
          <button class="btn btn-primary btn-sm" onclick='showEditForm(<?= json_encode($item["id"]) ?>, <?= json_encode($item["name"]) ?>, <?= json_encode($item["description"]) ?>, <?= json_encode($item["price"]) ?>, <?= json_encode($item["category"]) ?>)'>Bearbeiten</button>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <h3 class="mt-5">Benutzerübersicht</h3>
  <table class="table table-striped">
    <thead class="table-dark">
      <tr>
        <th>Username</th>
        <th>Email</th>
        <th>Rolle</th>
        <th>Passwort ändern</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($users as $user): ?>
      <tr>
        <td><?= htmlspecialchars($user["username"]) ?></td>
        <td><?= htmlspecialchars($user["email"]) ?></td>
        <td><?= htmlspecialchars($user["role"]) ?></td>
        <td>
          <div class="d-flex justify-content-center align-items-center gap-2 flex-wrap">
            <form method="POST" action="app/models/change_password.php" class="d-flex align-items-center gap-2">
              <input type="hidden" name="user_id" value="<?= $user["id"] ?>">
              <div class="input-group password-input">
                <input type="password" name="new_password" class="form-control" placeholder="Neues Passwort" required>
                <button type="submit" class="btn btn-primary btn-sm">Ändern</button>
              </div>
            </form>
            <?php if ($user["id"] != 1): ?>
              <form method="POST" action="admin.php" onsubmit="return confirm('Benutzer wirklich löschen?');">
                <input type="hidden" name="delete_user_id" value="<?= $user["id"] ?>">
                <button type="submit" class="btn btn-danger btn-sm btn-delete-small">Löschen</button>
              </form>
            <?php endif; ?>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<!-- Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST">
        <input type="hidden" name="edit_id" id="edit_id">
        <div class="modal-header">
          <h5 class="modal-title">Speise bearbeiten</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="text" name="edit_name" id="edit_name" class="form-control mb-3" required>
          <input type="text" name="edit_description" id="edit_description" class="form-control mb-3" required>
          <input type="number" name="edit_price" id="edit_price" class="form-control mb-3" step="0.01" required>
          <select name="edit_category" id="edit_category" class="form-select mb-3" required>
            <option value="Breakfast">Frühstück</option>
            <option value="Lunch">Mittagessen</option>
            <option value="Dinner">Abendessen</option>
          </select>
        </div>
        <div class="modal-footer">
          <button type="submit" name="edit_food" class="btn btn-primary">Speichern</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  function showEditForm(id, name, description, price, category) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_description').value = description;
    document.getElementById('edit_price').value = price;
    document.getElementById('edit_category').value = category;
    new bootstrap.Modal(document.getElementById('editModal')).show();
  }
</script>
</body>
</html>
