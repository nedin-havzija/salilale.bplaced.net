<?php
session_start();
include "config.php"; // DB-Verbindung

$errors = [];
$success = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    $email = trim($_POST["email"]);

    if (empty($username) || empty($password) || empty($email)) {
        $errors[] = "Benutzername, Passwort und E-Mail sind erforderlich.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Bitte gib eine gültige E-Mail-Adresse ein.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            if ($existing["username"] === $username) {
                $errors[] = "Benutzername ist bereits vergeben.";
            }
            if ($existing["email"] === $email) {
                $errors[] = "Diese E-Mail-Adresse wird bereits verwendet.";
            }
        } else {
            $hashed = md5($password);
            $stmt = $conn->prepare("INSERT INTO users (username, password, role, email) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$username, $hashed, 'user', $email])) {
                $success = true;

                $_SESSION["username"] = $username;
                $_SESSION["admin"] = false;

                // Werte an JS weitergeben
                $_SESSION["send_welcome"] = true;
                $_SESSION["user_email"] = $email;
                $_SESSION["user_name"] = $username;

                header("Location: register.php");
                exit;
            } else {
                $errors[] = "Fehler beim Registrieren.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Registrierung – FOODHave</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap">
  <style>
    :root {
        --primary-color: #F8F7FC;
        --secondary-color: #231E1E;
        --accent-color: #FC8C1B;
    }

    * {
        padding: 0;
        margin: 0;
        box-sizing: border-box;
        font-family: 'Poppins', sans-serif;
    }

    body {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        background: var(--primary-color);
    }

    .registration-container {
        background: #ffffff;
        padding: 30px 40px;
        width: 640px;
        border-radius: 12px;
        box-shadow: 10px 14px 90px #F5AC6252;
        text-align: center;
    }

    .registration-header h2 {
        font-size: 22px;
        font-weight: 700;
    }

    .divider {
        width: 15%;
        height: 5px;
        background: var(--accent-color);
        margin: 10px auto 20px;
    }

    .input-box {
        margin-bottom: 15px;
        text-align: left;
    }

    .input-box label {
        display: block;
        margin-bottom: 5px;
        font-weight: 500;
    }

    .input-field {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    .btn-register {
        background: var(--secondary-color);
        color: white;
        border: none;
        padding: 12px 25px;
        width: 100%;
        font-weight: bold;
        font-size: 16px;
        border-radius: 4px;
        cursor: pointer;
        box-shadow: 0px 4px 12px #231E1E50;
    }

    .btn-register:hover {
        background: var(--accent-color);
    }

    .registration-footer {
        margin-top: 20px;
    }

    .registration-footer a {
        color: var(--accent-color);
        text-decoration: none;
        font-weight: 700;
    }

    .error-message {
        background: #ffefef;
        color: #c00;
        padding: 10px 15px;
        border-radius: 5px;
        margin-bottom: 15px;
        text-align: left;
    }

    .success-message {
        background: #e0ffee;
        color: #008040;
        padding: 10px 15px;
        border-radius: 5px;
        margin-bottom: 15px;
        text-align: center;
        font-weight: bold;
    }
  </style>
</head>
<body>

<div class="registration-container">
    <div class="registration-header">
        <h2>Registrieren</h2>
        <span class="divider"></span>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="error-message"><?= implode("<br>", $errors); ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="success-message">Erfolgreich registriert!</div>
    <?php endif; ?>

    <div class="registration-body">
        <form method="POST">
            <div class="input-box">
                <label for="username">Benutzername</label>
                <input type="text" name="username" class="input-field" placeholder="Enter Your Username" required>
            </div>
            <div class="input-box">
                <label for="email">E-Mail</label>
                <input type="email" name="email" class="input-field" placeholder="Enter Your Email" required>
            </div>
            <div class="input-box">
                <label for="password">Passwort</label>
                <input type="password" name="password" class="input-field" placeholder="Enter Your Password" required>
            </div>
            <button class="btn-register" type="submit">Registrieren</button>
        </form>
    </div>
    <div class="registration-footer">
        <p>Bereits ein Konto? <a href="index.php">Login</a></p>
    </div>
</div>

<?php if (isset($_SESSION["send_welcome"])): ?>
<!-- EmailJS Script für Willkommensnachricht -->
<script src="https://cdn.jsdelivr.net/npm/@emailjs/browser@3/dist/email.min.js"></script>
<script>
  emailjs.init("STs-sTNgi5vhV6pIO");

  window.addEventListener("DOMContentLoaded", () => {
    emailjs.send("service_hicsavu", "template_oj8s9dq", {
      to_email: "<?= $_SESSION['user_email']; ?>",
      user_name: "<?= $_SESSION['user_name']; ?>"
    }).then(() => {
      console.log("📩 Willkommens-E-Mail gesendet!");
    }).catch((err) => {
      console.error("❌ Fehler beim Senden:", err);
    });
  });
</script>
<?php
unset($_SESSION["send_welcome"]);
unset($_SESSION["user_email"]);
unset($_SESSION["user_name"]);
endif;
?>

</body>
</html>
