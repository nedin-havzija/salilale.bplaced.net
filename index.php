<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "config.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Website</title>
    <!-- for icons  -->
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    <!-- bootstrap  -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <!-- for swiper slider  -->
    <link rel="stylesheet" href="assets/css/swiper-bundle.min.css">
    <!-- fancy box  -->
    <link rel="stylesheet" href="assets/css/jquery.fancybox.min.css">
    <!-- custom css  -->
    <link rel="stylesheet" href="style.css?v=1.9">
    <style>
/* 🔹 General Header Styles */
.header-right {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 15px;
}

/* 🔹 Login Dropdown */
.login-dropdown {
    display: none;
    position: absolute;
    top: 45px;
    right: 0;
    background: white;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    padding: 15px;
    border-radius: 6px;
    width: 220px;
    z-index: 1000;
}

.login-dropdown input {
    width: 100%;
    padding: 10px;
    margin: 8px 0;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 14px;
}

.login-dropdown button {
    width: 100%;
    padding: 10px;
    background: #ff5733;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
    font-weight: bold;
    transition: background 0.3s;
}

.login-dropdown button:hover {
    background: #e74c3c;
}

/* Admin Panel and Logout Buttons */
.admin-controls {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-left: 15px;
    position: relative;
    z-index: 1000; /* Ensures it's above other elements */
}

.admin-controls a {
    background: white;
    padding: 10px 16px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: bold;
    color: #333;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.15); /* Better shadow */
    font-size: 14px;
    position: relative; /* Ensures z-index applies */
    z-index: 1001; /* Even higher to stay on top */
}

.admin-controls a:hover {
    background: #ff5733;
    color: white;
}

.header-right {
    position: relative;
    z-index: 1000; /* Makes sure it's above other content */
}

/* 🔹 Header Buttons */
.header-btn {
    position: relative;
    display: flex;
    align-items: center;
    cursor: pointer;
    padding: 5px;
    border-radius: 6px;
}

.header-btn i {
    font-size: 20px;
    color: #333;
    transition: color 0.3s ease;
}

.header-btn:hover i {
    color: #ff5733;
}

/* 🔹 Logout Button */
.logout-btn {
    background: #333;
    color: white;
    padding: 8px 14px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: bold;
    font-size: 14px;
    transition: background 0.3s ease, color 0.3s ease;
}

.logout-btn:hover {
    background: #ff5733;
    color: white;
}

.header-right .btn {
    margin-left: 5px;
}

    </style>
</head>

<body class="body-fixed">
    <!-- Beginn der Kopfzeile -->
    <header class="site-header">
    <div class="container">
        <div class="row">
            <div class="col-lg-2">
                <div class="header-logo">
                    <a href="index.php">
                        <img src="logo.png" width="160" height="36" alt="Logo">
                    </a>
                </div>
            </div>
            <div class="col-lg-10">
                <div class="main-navigation d-flex align-items-center justify-content-between">
                    <button class="menu-toggle"><span></span><span></span></button>
                    <nav class="header-menu">
                        <ul class="menu food-nav-menu">
                            <li><a href="#home">Startseite</a></li>
                            <li><a href="#about">Über Uns</a></li>
                            <li><a href="#menu">Speisekarte</a></li>
                            <li><a href="#gallery">Galerie</a></li>
                            <li><a href="#blog">Blog</a></li>
                            <li><a href="#contact">Kontakt</a></li>
                        </ul>
                    </nav>

                    <div class="header-right d-flex align-items-center gap-4">

                        <!-- Suchfunktion -->
                        <form action="#" class="header-search-form for-des">
                            <input type="search" class="form-input" placeholder="Hier suchen...">
                            <button type="submit">
                                <i class="uil uil-search"></i>
                            </button>
                        </form>

                        <!-- Warenkorb -->
                        <?php
                        if (session_status() === PHP_SESSION_NONE) {
                            session_start();
                        }
                        $cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
                        ?>
                        <a href="checkout.php" class="header-btn header-cart">
                            <i class="uil uil-shopping-bag"></i>
                            <span class="cart-number"><?= $cart_count; ?></span>
                        </a>

                        <!-- Login/Userbereich -->
                        <?php if (isset($_SESSION["username"])) : ?>
    <div class="d-flex align-items-center gap-3">
        <div class="d-flex align-items-center gap-2 px-3 py-1" style="background: #f3f3f3; border-radius: 8px;">
            <i class="uil uil-user" style="font-size: 18px;"></i>
            <span style="font-weight: 500;"><?= htmlspecialchars($_SESSION["username"]); ?></span>
        </div>

        <?php if (!empty($_SESSION["admin"])) : ?>
            <a href="admin.php" class="btn btn-sm btn-outline-secondary" style="border-radius: 8px;">Admin-Bereich</a>
        <?php endif; ?>

        <a href="logout.php" class="btn btn-sm btn-dark" style="font-weight: 600; border-radius: 8px;">Abmelden</a>
    </div>
    <?php else : ?>
    <div class="header-btn" id="loginBtn">
        <i class="uil uil-user-md"></i>
        <div class="login-dropdown" id="loginDropdown">
            <input type="text" id="username" placeholder="Benutzername">
            <input type="password" id="password" placeholder="Passwort">
            <button onclick="login()">Anmelden</button>
            <!-- Add the Register Link here -->
            <div class="text-center mt-2">
                <a href="register.php" style="font-size: 14px;">Noch kein Konto? Registrieren</a>
            </div>
        </div>
    </div>
<?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</header>


    <script>
        document.getElementById("loginBtn").addEventListener("click", function (event) {
            let dropdown = document.getElementById("loginDropdown");
            dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
            event.stopPropagation(); // Prevents closing when clicking the button itself
        });

        document.getElementById("loginDropdown").addEventListener("click", function (event) {
            event.stopPropagation(); // Prevents closing when clicking inside the dropdown
        });

        function login() {
            let username = document.getElementById("username").value;
            let password = document.getElementById("password").value;

            fetch("login.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "username=" + encodeURIComponent(username) + "&password=" + encodeURIComponent(password)
            })
            .then(response => response.text())
            .then(data => {
                if (data === "success") {
                    location.reload(); // Reload page to show admin controls
                } else {
                    alert("Invalid credentials! Try again.");
                }
            })
            .catch(error => console.error("Error:", error));
        }

        document.addEventListener("click", function (event) {
            let loginDropdown = document.getElementById("loginDropdown");

            // If the click is outside the dropdown and the button, close it
            if (loginDropdown.style.display === "block") {
                loginDropdown.style.display = "none";
            }
        });
    </script>
</body>

    <!-- header ends  -->

    <div id="viewport">
        <div id="js-scroll-content">
            <section class="main-banner" id="home">
    <div class="js-parallax-scene">
        <div class="banner-shape-1 w-100" data-depth="0.30">
            <img src="assets/images/berry.png" alt="Beere">
        </div>
        <div class="banner-shape-2 w-100" data-depth="0.25">
            <img src="assets/images/leaf.png" alt="Blatt">
        </div>
    </div>
    <div class="sec-wp">
        <div class="container">
            <div class="row">
                <!-- Textbereich -->
                <div class="col-lg-6">
                    <div class="banner-text">
                        <h1 class="h1-title">
                            Willkommen in unserem
                            <span>indischen</span>
                            Restaurant.
                        </h1>
                        <p>Geniessen Sie authentische indische Küche mit frischen Zutaten und traditionellen Gewürzen. Lassen Sie sich von unseren köstlichen Gerichten verwöhnen und erleben Sie einen unvergesslichen Geschmack.</p>
                        <div class="banner-btn mt-4">
                            <a href="#menu" class="sec-btn">Unsere Speisekarte ansehen</a>
                        </div>
                    </div>
                </div>

                <!-- Bildbereich -->
                <div class="col-lg-6">
                    <div class="banner-img-wp">
                        <div class="banner-img" style="background-image: url(assets/images/main-b.jpg);">
                        </div>
                    </div>
                    <div class="banner-img-text mt-4 m-auto">
                        <h5 class="h5-title">Sushi</h5>
                        <p>Probieren Sie unser frisches und handgefertigtes Sushi – eine perfekte Kombination aus Geschmack und Qualität.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


            <section class="brands-sec">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="brand-title mb-5">
                                <h5 class="h5-title">Vertraut von über 70 Unternehmen</h5>
                            </div>
                            <div class="brands-row">
                                <div class="brands-box">
                                    <img src="assets/images/brands/b1.png" alt="">
                                </div>
                                <div class="brands-box">
                                    <img src="assets/images/brands/b2.png" alt="">
                                </div>
                                <div class="brands-box">
                                    <img src="assets/images/brands/b3.png" alt="">
                                </div>
                                <div class="brands-box">
                                    <img src="assets/images/brands/b4.png" alt="">
                                </div>
                                <div class="brands-box">
                                    <img src="assets/images/brands/b5.png" alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="about-sec section" id="about">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="sec-title text-center mb-5">
                    <p class="sec-sub-title mb-3">Über uns</p>
                    <h2 class="h2-title">Entdecken Sie <span>unsere Geschichte</span></h2>
                    <div class="sec-title-shape mb-4">
                        <img src="assets/images/title-shape.svg" alt="Dekorative Linie">
                    </div>
                    <p>Willkommen in unserem Restaurant! Seit unserer Gründung haben wir es uns zur Aufgabe gemacht, unseren Gästen authentische Aromen und hochwertige Zutaten zu bieten. Unser Team vereint traditionelle Rezepte mit moderner Kochkunst, um Ihnen ein einzigartiges Geschmackserlebnis zu garantieren. Lassen Sie sich von unseren Gerichten verzaubern und geniessen Sie eine unvergessliche kulinarische Reise.</p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-8 m-auto">
                <div class="about-video">
                    <div class="about-video-img" style="background-image: url(assets/images/about.jpg);">
                    </div>
                    <div class="play-btn-wp">
                        <a href="assets/images/video.mp4" data-fancybox="video" class="play-btn">
                            <i class="uil uil-play"></i>
                        </a>
                        <span>Unsere Küche in Aktion</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>



            <?php
                include "config.php"; // Include DB connection

                // Fetch all food items from database
                $stmt = $conn->query("SELECT * FROM food_items ORDER BY id DESC");
                $food_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
                ?>

<section style="background-image: url(assets/images/menu-bg.png);" class="our-menu section bg-light repeat-img" id="menu">
    <div class="sec-wp">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="sec-title text-center mb-5">
                        <p class="sec-sub-title mb-3">Unsere Speisekarte</p>
                        <h2 class="h2-title">Starte den Tag <span>mit frischer & gesunder Kost</span></h2>
                        <div class="sec-title-shape mb-4">
                            <img src="assets/images/title-shape.svg" alt="Dekorative Linie">
                        </div>
                    </div>
                </div>
            </div>
            <div class="menu-tab-wp">
                <div class="row">
                    <div class="col-lg-12 m-auto">
                        <div class="menu-tab text-center">
                            <ul class="filters">
                                <div class="filter-active"></div>
                                <li class="filter" data-filter=".all, .breakfast, .lunch, .dinner">
                                    <img src="assets/images/menu-1.png" alt="">
                                    Alle Gerichte
                                </li>
                                <li class="filter" data-filter=".breakfast">
                                    <img src="assets/images/menu-2.png" alt="">
                                    Frühstück
                                </li>
                                <li class="filter" data-filter=".lunch">
                                    <img src="assets/images/menu-3.png" alt="">
                                    Mittagessen
                                </li>
                                <li class="filter" data-filter=".dinner">
                                    <img src="assets/images/menu-4.png" alt="">
                                    Abendessen
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dynamische Speisekarte -->
            <div class="menu-list-row">
                <div class="row g-xxl-5 bydefault_show" id="menu-dish">
                    <?php foreach ($food_items as $item): ?>
                        <div class="col-lg-4 col-sm-6 dish-box-wp <?= strtolower($item['category']); ?>" data-cat="<?= strtolower($item['category']); ?>">
                            <div class="dish-box text-center">
                                <div class="dist-img">
                                    <?php 
                                        // Prüfe, ob das Bild existiert, sonst Standardbild verwenden
                                        $imagePath = !empty($item['image']) && file_exists(__DIR__ . "/uploads/" . basename($item['image'])) 
                                            ? "uploads/" . htmlspecialchars(basename($item['image'])) 
                                            : "assets/images/no-image.png"; 
                                    ?>
                                    <img src="<?= $imagePath ?>" 
                                        alt="<?= htmlspecialchars($item['name']); ?>" 
                                        onerror="this.onerror=null; this.src='assets/images/no-image.png';">
                                </div>
                                <div class="dish-rating">
                                    <?= number_format($item['rating'], 1) ?>
                                    <i class="uil uil-star"></i>
                                </div>
                                <div class="dish-title">
                                    <h3 class="h3-title"><?= htmlspecialchars($item['name']); ?></h3>
                                    <p><?= htmlspecialchars($item['calories']); ?> Kalorien</p>
                                </div>
                                <div class="dish-info">
                                    <ul>
                                        <li>
                                            <p>Art</p>
                                            <b><?= htmlspecialchars($item['type']); ?></b>
                                        </li>
                                        <li>
                                            <p>Portionen</p>
                                            <b><?= htmlspecialchars($item['persons']); ?></b>
                                        </li>
                                    </ul>
                                </div>
                                <div class="dist-bottom-row">
                                    <ul>
                                        <li>
                                            <b>€ <?= number_format($item['price'], 2); ?></b>
                                        </li>
                                        <li>
                                        <button class="dish-add-btn" onclick="addToCart(<?= $item['id']; ?>)">
                                            <i class="uil uil-plus"></i>
                                        </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
let adding = false;

function addToCart(itemId) {
    if (adding) return; // ignore rapid clicks
    adding = true;

    $.ajax({
        url: 'add_to_cart.php',
        method: 'POST',
        data: { id: itemId },
        dataType: 'json', // important!
        success: function(response) {
            if (response.success) {
                $('.cart-number').text(response.cart_count);
            }
        },
        complete: function() {
            adding = false;
        }
    });
}
</script>


<section class="two-col-sec section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-5">
                <div class="sec-img mt-5">
                    <img src="assets/images/pizza.png" alt="Leckere Pizza">
                </div>
            </div>
            <div class="col-lg-7">
                <div class="sec-text">
                    <h2 class="xxl-title">Hähnchen-Peperoni-Pizza</h2>
                    <p>Unsere **Hähnchen-Peperoni-Pizza** ist eine köstliche Kombination aus zartem Hühnchen, würziger Peperoni und schmelzendem Mozzarella. Perfekt für alle, die Pizza mit einem extra würzigen Kick lieben!</p>
                    <p>Mit handgemachtem Teig und einer hausgemachten Tomatensauce ist dieses Gericht ein absoluter Favorit unserer Gäste. Frische Zutaten und aromatische Gewürze machen jeden Bissen zu einem Geschmackserlebnis.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="two-col-sec section pt-0">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 order-lg-1 order-2">
                <div class="sec-text">
                    <h2 class="xxl-title">Japanische Sushi-Spezialität</h2>
                    <p>Unsere **Sushi-Kreationen** bestehen aus feinsten Zutaten, perfekt zubereitet nach traditioneller japanischer Handwerkskunst. Frischer Lachs, knackige Gurken und cremige Avocado vereinen sich zu einem echten Genuss.</p>
                    <p>Jede Sushi-Rolle wird mit höchster Sorgfalt und Liebe zum Detail zubereitet. Egal ob klassisch oder modern – unser Sushi bringt ein authentisches Geschmackserlebnis direkt auf deinen Teller.</p>
                </div>
            </div>
            <div class="col-lg-6 order-lg-2 order-1">
                <div class="sec-img">
                    <img src="assets/images/sushi.png" alt="Frisches Sushi">
                </div>
            </div>
        </div>
    </div>
</section>

            <section class="book-table section bg-light">
                <div class="book-table-shape">
                    <img src="assets/images/table-leaves-shape.png" alt="">
                </div>

                <div class="book-table-shape book-table-shape2">
                    <img src="assets/images/table-leaves-shape.png" alt="">
                </div>

                <div class="sec-wp">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="sec-title text-center mb-5">
                    <p class="sec-sub-title mb-3">Tischreservierung</p>
                    <h2 class="h2-title">Öffnungszeiten & Reservierung</h2>
                    <div class="sec-title-shape mb-4">
                        <img src="assets/images/title-shape.svg" alt="Dekoratives Element">
                    </div>
                </div>
            </div>
        </div>

        <div class="book-table-info">
            <div class="row align-items-center">
                <div class="col-lg-4">
                    <div class="table-title text-center">
                        <h3>Montag bis Donnerstag</h3>
                        <p>09:00 - 22:00 Uhr</p>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="call-now text-center">
                        <i class="uil uil-phone"></i>
                        <a href="tel:+41-123456789">+41 12 345 67 89</a>
                        <p>Rufen Sie uns für Reservierungen an</p>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="table-title text-center">
                        <h3>Freitag bis Sonntag</h3>
                        <p>11:00 - 20:00 Uhr</p>
                    </div>
                </div>
            </div>
        </div>

                        <div class="row" id="gallery">
                            <div class="col-lg-10 m-auto">
                                <div class="book-table-img-slider" id="icon">
                                    <div class="swiper-wrapper">
                                        <a href="assets/images/bt1.jpg" data-fancybox="table-slider"
                                            class="book-table-img back-img swiper-slide"
                                            style="background-image: url(assets/images/bt1.jpg)"></a>
                                        <a href="assets/images/bt2.jpg" data-fancybox="table-slider"
                                            class="book-table-img back-img swiper-slide"
                                            style="background-image: url(assets/images/bt2.jpg)"></a>
                                        <a href="assets/images/bt3.jpg" data-fancybox="table-slider"
                                            class="book-table-img back-img swiper-slide"
                                            style="background-image: url(assets/images/bt3.jpg)"></a>
                                        <a href="assets/images/bt4.jpg" data-fancybox="table-slider"
                                            class="book-table-img back-img swiper-slide"
                                            style="background-image: url(assets/images/bt4.jpg)"></a>
                                        <a href="assets/images/bt1.jpg" data-fancybox="table-slider"
                                            class="book-table-img back-img swiper-slide"
                                            style="background-image: url(assets/images/bt1.jpg)"></a>
                                        <a href="assets/images/bt2.jpg" data-fancybox="table-slider"
                                            class="book-table-img back-img swiper-slide"
                                            style="background-image: url(assets/images/bt2.jpg)"></a>
                                        <a href="assets/images/bt3.jpg" data-fancybox="table-slider"
                                            class="book-table-img back-img swiper-slide"
                                            style="background-image: url(assets/images/bt3.jpg)"></a>
                                        <a href="assets/images/bt4.jpg" data-fancybox="table-slider"
                                            class="book-table-img back-img swiper-slide"
                                            style="background-image: url(assets/images/bt4.jpg)"></a>
                                    </div>

                                    <div class="swiper-button-wp">
                                        <div class="swiper-button-prev swiper-button">
                                            <i class="uil uil-angle-left"></i>
                                        </div>
                                        <div class="swiper-button-next swiper-button">
                                            <i class="uil uil-angle-right"></i>
                                        </div>
                                    </div>
                                    <div class="swiper-pagination"></div>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>

            </section>

            <section class="our-team section">
                <div class="sec-wp">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="sec-title text-center mb-5">
                                    <p class="sec-sub-title mb-3">Unser Team</p>
                                    <h2 class="h2-title">Treffe unsere Chefs</h2>
                                    <div class="sec-title-shape mb-4">
                                        <img src="assets/images/title-shape.svg" alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row team-slider">
                            <div class="swiper-wrapper">
                                <div class="col-lg-4 swiper-slide">
                                    <div class="team-box text-center">
                                        <div style="background-image: url(assets/images/chef/c1.jpg);"
                                            class="team-img back-img">

                                        </div>
                                        <h3 class="h3-title">Nilay Hirpara</h3>
                                        <div class="social-icon">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 swiper-slide">
                                    <div class="team-box text-center">
                                        <div style="background-image: url(assets/images/chef/c2.jpg);"
                                            class="team-img back-img">

                                        </div>
                                        <h3 class="h3-title">Ravi Kumawat</h3>
                                        <div class="social-icon">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 swiper-slide">
                                    <div class="team-box text-center">
                                        <div style="background-image: url(assets/images/chef/c3.jpg);"
                                            class="team-img back-img">

                                        </div>
                                        <h3 class="h3-title">Navnit Kumar</h3>
                                        <div class="social-icon">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 swiper-slide">
                                    <div class="team-box text-center">
                                        <div style="background-image: url(assets/images/chef/c4.jpg);"
                                            class="team-img back-img">

                                        </div>
                                        <h3 class="h3-title">Pranav Badgal</h3>
                                        <div class="social-icon">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 swiper-slide">
                                    <div class="team-box text-center">
                                        <div style="background-image: url(assets/images/chef/c5.jpg);"
                                            class="team-img back-img">

                                        </div>
                                        <h3 class="h3-title">Priyotosh Dey</h3>
                                        <div class="social-icon">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="swiper-button-wp">
                                <div class="swiper-button-prev swiper-button">
                                    <i class="uil uil-angle-left"></i>
                                </div>
                                <div class="swiper-button-next swiper-button">
                                    <i class="uil uil-angle-right"></i>
                                </div>
                            </div>
                            <div class="swiper-pagination"></div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="testimonials section bg-light">
                <div class="sec-wp">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-12">
                            <div class="sec-title text-center mb-5">
                                <p class="sec-sub-title mb-3">Was sie sagen</p>
                                <h2 class="h2-title">Was unsere Kunden <span>über uns sagen</span></h2>
                                <div class="sec-title-shape mb-4">
                                    <img src="assets/images/title-shape.svg" alt="">
                                </div>
                            </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-5">
                                <div class="testimonials-img">
                                    <img src="assets/images/testimonial-img.png" alt="">
                                </div>
                            </div>
                            <div class="col-lg-7">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="testimonials-box">
                                            <div class="testimonial-box-top">
                                                <div class="testimonials-box-img back-img"
                                                    style="background-image: url(assets/images/testimonials/t1.jpg);">
                                                </div>
                                                <div class="star-rating-wp">
                                                    <div class="star-rating">
                                                        <span class="star-rating__fill" style="width:85%"></span>
                                                    </div>
                                                </div>

                                                </div>
                                                <div class="testimonials-box-text">
                                                    <h3 class="h3-title">
                                                        Nilay Hirpara
                                                    </h3>
                                                    <p>Ich war anfangs skeptisch, aber der Service hat mich komplett überzeugt. Schnelle Lieferung, tolle Qualität – absolut empfehlenswert!</p>
                                                </div>
                                                </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="testimonials-box">
                                                        <div class="testimonial-box-top">
                                                            <div class="testimonials-box-img back-img"
                                                                style="background-image: url(assets/images/testimonials/t2.jpg);">
                                                            </div>
                                                            <div class="star-rating-wp">
                                                                <div class="star-rating">
                                                                    <span class="star-rating__fill" style="width:80%"></span>
                                                                </div>
                                                            </div>

                                                        </div>
                                                        <div class="testimonials-box-text">
                                                            <h3 class="h3-title">
                                                                Ravi Kumawat
                                                            </h3>
                                                            <p>Ich habe selten so einen freundlichen Kundenservice erlebt! Die Mitarbeiter haben sich wirklich Zeit genommen, um meine Fragen zu beantworten.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="testimonials-box">
                                                        <div class="testimonial-box-top">
                                                            <div class="testimonials-box-img back-img"
                                                                style="background-image: url(assets/images/testimonials/t3.jpg);">
                                                            </div>
                                                            <div class="star-rating-wp">
                                                                <div class="star-rating">
                                                                    <span class="star-rating__fill" style="width:89%"></span>
                                                                </div>
                                                            </div>

                                                        </div>
                                                        <div class="testimonials-box-text">
                                                            <h3 class="h3-title">
                                                                Navnit Kumar
                                                            </h3>
                                                            <p>Top Qualität! Ich nutze das Produkt nun seit mehreren Monaten und bin immer noch begeistert. Es hält genau das, was versprochen wurde.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="testimonials-box">
                                                        <div class="testimonial-box-top">
                                                            <div class="testimonials-box-img back-img"
                                                                style="background-image: url(assets/images/testimonials/t4.jpg);">
                                                            </div>
                                                            <div class="star-rating-wp">
                                                                <div class="star-rating">
                                                                    <span class="star-rating__fill" style="width:100%"></span>
                                                                </div>
                                                            </div>

                                                        </div>
                                                        <div class="testimonials-box-text">
                                                            <h3 class="h3-title">
                                                                Somyadeep Bhowmik
                                                            </h3>
                                                            <p>Perfekt! Ich bin rundum zufrieden – von der Bestellung bis zur Lieferung lief alles reibungslos. Ich werde auf jeden Fall wieder hier kaufen.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="faq-sec section-repeat-img" style="background-image: url(assets/images/faq-bg.png);">
    <div class="sec-wp">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="sec-title text-center mb-5">
                        <p class="sec-sub-title mb-3">FAQs</p>
                        <h2 class="h2-title">Häufig <span>gestellte Fragen</span></h2>
                        <div class="sec-title-shape mb-4">
                            <img src="assets/images/title-shape.svg" alt="">
                        </div>
                    </div>
                </div>
            </div>

            <div class="faq-row">
                <div class="faq-box">
                    <h4 class="h4-title">Wie sind die Anmeldezeiten?</h4>
                    <p>Unsere Plattform ist rund um die Uhr verfügbar. Bei Wartungsarbeiten informieren wir Sie im Voraus.</p>
                </div>
                <div class="faq-box">
                    <h4 class="h4-title">Wann erhalte ich meine Rückerstattung?</h4>
                    <p>Rückerstattungen werden in der Regel innerhalb von 3–5 Werktagen bearbeitet. In einigen Fällen kann es je nach Bank bis zu 10 Tage dauern.</p>
                </div>
                <div class="faq-box">
                    <h4 class="h4-title">Wie lange dauert die Lieferung des Essens?</h4>
                    <p>Die Lieferzeit hängt vom Standort ab, beträgt aber in der Regel zwischen 30 und 60 Minuten.</p>
                </div>
                <div class="faq-box">
                    <h4 class="h4-title">Bietet Ihr Restaurant sowohl vegetarische als auch nicht-vegetarische Speisen an?</h4>
                    <p>Ja, wir haben eine grosse Auswahl an vegetarischen und nicht-vegetarischen Gerichten. Alle Speisen sind deutlich gekennzeichnet.</p>
                </div>
                <div class="faq-box">
                    <h4 class="h4-title">Wie hoch sind die Lieferkosten?</h4>
                    <p>Die Lieferkosten variieren je nach Entfernung und Bestellwert. Ab einem Bestellwert von 30 € ist die Lieferung kostenlos.</p>
                </div>
                <div class="faq-box">
                    <h4 class="h4-title">Wer kann eine Pro-Mitgliedschaft erhalten?</h4>
                    <p>Jeder Kunde kann eine Pro-Mitgliedschaft abschliessen, um exklusive Rabatte und schnellere Lieferungen zu erhalten.</p>
                </div>
            </div>
        </div>
    </div>
</section>



<div class="bg-pattern bg-light repeat-img"
    style="background-image: url(assets/images/blog-pattern-bg.png);">
    <section class="blog-sec section" id="blog">
        <div class="sec-wp">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="sec-title text-center mb-5">
                            <p class="sec-sub-title mb-3">Unser Blog</p>
                            <h2 class="h2-title">Neueste Beiträge</h2>
                            <div class="sec-title-shape mb-4">
                                <img src="assets/images/title-shape.svg" alt="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4">
                        <div class="blog-box">
                            <div class="blog-img back-img"
                                style="background-image: url(assets/images/blog/blog1.jpg);"></div>
                            <div class="blog-text">
                                <p class="blog-date">15. September 2021</p>
                                <a href="#" class="h4-title">Energie-Drink ganz einfach zuhause zubereiten</a>
                                <p>Entdecke, wie du aus natürlichen Zutaten einen leckeren Energie-Drink mixen kannst – perfekt für den Start in den Tag.</p>
                                <a href="#" class="sec-btn">Mehr lesen</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="blog-box">
                            <div class="blog-img back-img"
                                style="background-image: url(assets/images/blog/blog2.jpg);"></div>
                            <div class="blog-text">
                                <p class="blog-date">15. Oktober 2021</p>
                                <a href="#" class="h4-title">Frische Gemüse-Reis-Kombination für das Abendessen</a>
                                <p>Gesund, leicht und schnell zubereitet: So kombinierst du frisches Gemüse und Reis für ein ausgewogenes Abendessen.</p>
                                <a href="#" class="sec-btn">Mehr lesen</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="blog-box">
                            <div class="blog-img back-img"
                                style="background-image: url(assets/images/blog/blog3.jpg);"></div>
                            <div class="blog-text">
                                <p class="blog-date">15. November 2021</p>
                                <a href="#" class="h4-title">Chicken-Burger mit doppelten Nuggets</a>
                                <p>Ein Genuss für Fleischliebhaber: Saftiger Chicken-Burger mit doppelt knusprigen Nuggets und hausgemachter Sosse.</p>
                                <a href="#" class="sec-btn">Mehr lesen</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</div>
<section class="section pt-0">
  <div class="container">
    <div class="row align-items-start gx-5">
      
      <!-- Kontaktformular -->
      <div class="col-lg-6">
        <div class="contact-form-box">
          <h3>Kontaktiere uns</h3>
          <form id="contact-form">
            <label for="name">Name:</label>
            <input type="text" id="name" name="from_name" placeholder="Dein Name" required>

            <label for="email">E-Mail:</label>
            <input type="email" id="email" name="from_email" placeholder="Deine E-Mail-Adresse" required>

            <label for="message">Nachricht:</label>
            <textarea id="message" name="message" rows="4" placeholder="Deine Nachricht" required></textarea>

            <button type="submit" id="button">Nachricht senden</button>
          </form>

          <script src="https://cdn.jsdelivr.net/npm/@emailjs/browser@3/dist/email.min.js"></script>
          <script>
            emailjs.init("jBo_qyx7uWt6jlBvq");
            document.getElementById('contact-form').addEventListener('submit', function(event) {
              event.preventDefault();
              emailjs.sendForm('service_v31ou6n', 'template_es29i9l', this)
                .then(function() {
                  console.log('✅ Nachricht erfolgreich gesendet!');
                  document.getElementById('contact-form').reset();
                }, function(error) {
                  console.log('❌ Fehler beim Senden der Nachricht:', error);
                });
            });
          </script>
        </div>
      </div>

      <!-- Newsletter Box -->
      <div class="col-lg-6">
        <div class="newsletter-box-wrapper" style="background-image: url('assets/images/news.jpg');">
          <div class="bg-overlay"></div>
          <div class="content-wrap">
            <h2>Abonniere unseren Newsletter</h2>
            <p>Erhalte exklusive Rezepte, Tipps und Aktionen direkt in dein Postfach – bleib immer informiert!</p>
            <form action="#" class="newsletter-form">
              <input type="email" placeholder="Gib deine E-Mail-Adresse ein" required>
              <button type="submit">Absenden</button>
            </form>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>



            <!-- footer starts  -->
<footer class="site-footer" id="contact">
    <div class="top-footer section">
        <div class="sec-wp">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4">
                        <div class="footer-info">
                            <div class="footer-logo">
                                <a href="index.html">
                                    <img src="logo.png" alt="Logo">
                                </a>
                            </div>
                            <p>Besuchen Sie unser Restaurant und geniesen Sie frische, hausgemachte Spezialitäten in angenehmer Atmosphäre.</p>
                            <div class="social-icon">
                                <!-- Optional: Add social icons here -->
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-8">
                        <div class="footer-flex-box">
                            <div class="footer-table-info">
                                <h3 class="h3-title">Öffnungszeiten</h3>
                                <ul>
                                    <li><i class="uil uil-clock"></i> Montag - Donnerstag: 09:00 - 22:00 Uhr</li>
                                    <li><i class="uil uil-clock"></i> Freitag - Sonntag: 11:00 - 20:00 Uhr</li>
                                </ul>
                            </div>
                            <div class="footer-menu food-nav-menu">
                                <h3 class="h3-title">Links</h3>
                                <ul class="column-2">
                                    <li><a href="#home" class="footer-active-menu">Startseite</a></li>
                                    <li><a href="#about">Über uns</a></li>
                                    <li><a href="#menu">Speisekarte</a></li>
                                    <li><a href="#gallery">Galerie</a></li>
                                    <li><a href="#blog">Blog</a></li>
                                    <li><a href="#contact">Kontakt</a></li>
                                </ul>
                            </div>
                            <div class="footer-menu">
                                <h3 class="h3-title">Unternehmen</h3>
                                <ul>
                                    <li><a href="#">AGB</a></li>
                                    <li><a href="#">Datenschutz</a></li>
                                    <li><a href="#">Cookie-Richtlinie</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="bottom-footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <div class="copyright-text">
                        <p>Copyright &copy; 2025 <span class="name">FOODHave.</span> Alle Rechte vorbehalten.</p>
                    </div>
                </div>
            </div>
            <button class="scrolltop"><i class="uil uil-angle-up"></i></button>
        </div>
    </div>
</footer>



        </div>
    </div>





    <!-- jquery  -->
    <script src="assets/js/jquery-3.5.1.min.js"></script>
    <!-- bootstrap -->
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/popper.min.js"></script>

    <!-- fontawesome  -->
    <script src="assets/js/font-awesome.min.js"></script>

    <!-- swiper slider  -->
    <script src="assets/js/swiper-bundle.min.js"></script>

    <!-- mixitup -- filter  -->
    <script src="assets/js/jquery.mixitup.min.js"></script>

    <!-- fancy box  -->
    <script src="assets/js/jquery.fancybox.min.js"></script>

    <!-- parallax  -->
    <script src="assets/js/parallax.min.js"></script>

    <!-- gsap  -->
    <script src="assets/js/gsap.min.js"></script>

    <!-- scroll trigger  -->
    <script src="assets/js/ScrollTrigger.min.js"></script>
    <!-- scroll to plugin  -->
    <script src="assets/js/ScrollToPlugin.min.js"></script>
    <!-- rellax  -->
    <!-- <script src="assets/js/rellax.min.js"></script> -->
    <!-- <script src="assets/js/rellax-custom.js"></script> -->
    <!-- smooth scroll  -->
    <script src="assets/js/smooth-scroll.js"></script>
    <!-- custom js  -->
    <script src="main.js"></script>

</body>

</html>