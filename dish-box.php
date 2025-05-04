<div class="dish-box text-center">
  <div class="dist-img">
    <?php 
      $imagePath = !empty($item['image']) && file_exists(__DIR__ . "/uploads/" . basename($item['image'])) 
        ? "uploads/" . htmlspecialchars(basename($item['image'])) 
        : "assets/images/no-image.png"; 
    ?>
    <img src="<?= $imagePath ?>" alt="<?= htmlspecialchars($item['name']); ?>" class="dish-img" onerror="this.onerror=null; this.src='assets/images/no-image.png';">
  </div>
  <div class="dish-rating">
    <?= number_format($item['rating'], 1) ?> <i class="uil uil-star"></i>
  </div>
  <div class="dish-title">
    <h3 class="h3-title"><?= htmlspecialchars($item['name']); ?></h3>
    <p>Neu</p>
  </div>
  <div class="dish-info">
    <ul>
      <li><p>Herkunft</p><b>Indien</b></li>
      <li><p>Portionen</p><b><?= htmlspecialchars($item['persons']); ?></b></li>
    </ul>
    <br>
    <div class="dish-description mt-2">
      <p><strong>Beschreibung:</strong> <?= htmlspecialchars($item['description']); ?></p>
    </div>
  </div>
  <div class="dist-bottom-row">
    <ul>
      <li><b>CHF <?= number_format($item['price'], 2); ?></b></li>
      <li>
        <button class="dish-add-btn" onclick="addToCart(<?= $item['id']; ?>)">
          <i class="uil uil-plus"></i>
        </button>
      </li>
    </ul>
  </div>
</div>
