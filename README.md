FOODHave – Website für Essensbestellungen
Willkommen bei FOODHave, einer webbasierten Essensbestellplattform auf PHP-Basis. Dieses Projekt ermöglicht es Nutzern, Speisen anzusehen, in den Warenkorb zu legen, zur Kasse zu gehen und Bestellungen aufzugeben. Das System beinhaltet ausserdem eine Benutzeranmeldung und eine Admin-Oberfläche zur Verwaltung der Speisen.

🌐 Live-Website
Dieses Projekt ist für den Einsatz auf Shared Hosting ausgelegt http://salilale.bplaced.net/ . Lade einfach die Dateien auf deinen Webserver und konfiguriere deine Datenbankzugangsdaten.

📁 Projektstruktur (MVC-Muster)
Die Anwendung folgt grob dem Model-View-Controller (MVC)-Prinzip:

salilale.bplaced.net/
│
├── app/
│   ├── models/               # Datenlogik (z. B. cart_helpers.php)
│   └── views/                # Ansichtsvorlagen und Assets
│       └── assets/           # CSS, JavaScript, Bilder
│
├── config/
│   └── config.php           # Datenbankkonfiguration
│
├── uploads/                 # Hochgeladene Bilder (z. B. Speisebilder)
│
├── index.php                # Startseite / Einstiegspunkt
├── register.php             # Benutzerregistrierung
├── login.php                # Benutzeranmeldung
├── logout.php               # Logout-Funktion
├── add_to_cart.php          # Artikel in den Warenkorb legen
├── update_cart.php          # Warenkorb aktualisieren
├── checkout.php             # Warenkorbübersicht und Bestätigung
├── bestellung_absenden.php  # Bestellung abschicken & E-Mail-Versand
├── sendEmail.php            # E-Mail-Versand (EmailJS oder PHP mail)
├── clear_session.php        # Sitzungsdaten löschen
├── payment.php              # Zahlungslogik
└── admin.php                # Admin-Bereich zur Speisenverwaltung


⚙️ Funktionen
- Benutzerregistrierung und -anmeldung

- Warenkorb: Hinzufügen und Bearbeiten

- Kasse mit Bestellübersicht

- Admin-Bereich zur Speisenverwaltung

- Bestellbestätigung per E-Mail

- Auswahl der Zahlungsmethode

- Bilderupload und Verwaltung


📄 Lizenz
Alle Inhalte sind © 2025 FOODHave. Die unerlaubte Vervielfältigung oder Weitergabe ist untersagt.
