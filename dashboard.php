<?php
session_start();

// Redirect jika belum login
if (!isset($_SESSION['username'])) {
    header("Location: loginregister.php");
    exit();
}

// Inisialisasi preferensi dengan nilai default jika belum diset
if (!isset($_SESSION['theme'])) $_SESSION['theme'] = 'light';
if (!isset($_SESSION['font_size'])) $_SESSION['font_size'] = '16px';
if (!isset($_SESSION['font_family'])) $_SESSION['font_family'] = 'Arial, sans-serif';
if (!isset($_SESSION['navbar_color'])) $_SESSION['navbar_color'] = '#343a40';

// Fungsi untuk menentukan warna teks kontras (hitam/putih) berdasarkan warna background
function getContrastColor($hexColor) {
    $hexColor = ltrim($hexColor, '#');
    if (strlen($hexColor) == 3) {
        $hexColor = $hexColor[0].$hexColor[0].$hexColor[1].$hexColor[1].$hexColor[2].$hexColor[2];
    }
    $r = hexdec(substr($hexColor, 0, 2));
    $g = hexdec(substr($hexColor, 2, 2));
    $b = hexdec(substr($hexColor, 4, 2));
    // Perhitungan luminance
    $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
    return ($luminance > 0.5) ? '#222' : '#fff';
}
$navTextColor = getContrastColor($_SESSION['navbar_color']);

// Proses form pengaturan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_theme_font'])) {
        if (isset($_POST['theme'])) $_SESSION['theme'] = $_POST['theme'];
        if (isset($_POST['font_size'])) $_SESSION['font_size'] = $_POST['font_size'];
    }
    if (isset($_POST['save_font_navbar'])) {
        if (isset($_POST['font_family'])) $_SESSION['font_family'] = $_POST['font_family'];
        if (isset($_POST['navbar_color'])) $_SESSION['navbar_color'] = $_POST['navbar_color'];
    }
    header("Location: dashboard.php");
    exit();
}

// Tentukan kelas tema berdasarkan preferensi
$themeClass = $_SESSION['theme'] === 'dark' ? 'dark-mode' : 'light-mode';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <style>
        :root {
            --font-size: <?php echo $_SESSION['font_size']; ?>;
            --font-family: <?php echo $_SESSION['font_family']; ?>;
            --navbar-color: <?php echo $_SESSION['navbar_color']; ?>;
            --navbar-text-color: <?php echo $navTextColor; ?>;
        }

        body {
            font-family: var(--font-family);
            font-size: var(--font-size);
            margin: 0;
            background-color: #f8f9fa;
            color: #212529;
            transition: background-color 3s, color 3s, opacity 3s;
            opacity: 1;
        }

        body.dissolving {
            opacity: 0;
            transition: opacity 3s;
        }

        /* Ubah warna font menjadi putih saat dark mode, kecuali navbar */
        .dark-mode body,
        body.dark-mode {
            background-color: #212529;
            color: #f8f9fa;
            transition: background-color 3s, color 3s, opacity 3s;
        }
        .dark-mode .container,
        body.dark-mode .container {
            background: #343a40;
            transition: background 3s, opacity 3s;
        }
        .dark-mode h2,
        body.dark-mode h2 {
            color: #f8f9fa;
            transition: color 3s, opacity 3s;
        }
        .dark-mode p,
        body.dark-mode p {
            color: #ccc;
            transition: color 3s, opacity 3s;
        }
        /* Navbar tetap warna font #fff */
        .navbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: var(--navbar-color);
            padding: 10px 24px;
            transition: background-color 3s, opacity 3s;
        }

        .navbar .brand {
            color: var(--navbar-text-color);
            font-size: 1.3em;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.2s;
        }

        .navbar .brand:hover {
            color: #dc3545;
        }

        .navbar .nav-links {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .navbar .nav-links a {
            color: var(--navbar-text-color);
            text-decoration: none;
            padding: 8px 14px;
            border-radius: 4px;
            transition: background 0.2s, color 0.2s;
        }

        .navbar .nav-links a:hover {
            background: #495057;
            color: #dc3545;
        }

        .navbar .logout-btn {
            margin-left: 24px;
            background: #dc3545;
            color: #fff;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-weight: bold;
            transition: background 0.2s;
        }

        .navbar .logout-btn:hover {
            background: #b52a37;
        }

        .container {
            max-width: 700px;
            margin: 40px auto;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
            padding: 32px;
            text-align: center;
        }

        .dark-mode .container {
            background: #343a40;
        }

        h2 {
            margin-top: 0;
            color: #343a40;
        }

        .dark-mode h2 {
            color: #f8f9fa;
        }

        p {
            color: #555;
        }

        .dark-mode p {
            color: #ccc;
        }

        .settings-flex {
            display: flex;
            gap: 32px;
            justify-content: center;
            align-items: flex-start;
            margin-top: 32px;
        }

        .settings-form {
            flex: 1 1 0;
            min-width: 220px;
            max-width: 320px;
            background: rgba(0,0,0,0.01);
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 20px 18px 18px 18px;
            box-sizing: border-box;
        }

        .left-form {
            margin-right: 0;
        }

        .right-form {
            margin-left: 0;
        }

        @media (max-width: 700px) {
            .settings-flex {
                flex-direction: column;
                gap: 18px;
            }
            .settings-form {
                max-width: 100%;
            }
        }

        .settings-form label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .settings-form select,
        .settings-form input[type="color"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 16px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
        /* Tambahan agar input color jadi persegi panjang besar */
        .settings-form input[type="color"] {
            height: 40px;
            min-width: 100px;
            border: 1px solid #ccc;
            border-radius: 6px;
            background: none;
            padding: 0;
            cursor: pointer;
            /* Hilangkan border default browser */
            appearance: none;
            -webkit-appearance: none;
        }
        .settings-form input[type="color"]::-webkit-color-swatch-wrapper {
            padding: 0;
            border-radius: 6px;
        }
        .settings-form input[type="color"]::-webkit-color-swatch {
            border: none;
            border-radius: 6px;
        }
        .settings-form input[type="color"]::-moz-color-swatch {
            border: none;
            border-radius: 6px;
        }
        .settings-form input[type="color"]::-ms-color-swatch {
            border: none;
            border-radius: 6px;
        }

        .settings-form button {
            background: #007bff;
            color: #fff;
            border: none;
            padding: 10px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: background 0.2s;
        }

        .settings-form button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body class="<?php echo $themeClass; ?>">
    <nav class="navbar">
        <a href="#" class="brand">Dashboard</a>
        <div class="nav-links">
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </nav>
    <div class="container">
        <h2>Selamat datang, <?php echo htmlspecialchars(isset($_SESSION['nama_lengkap']) ? $_SESSION['nama_lengkap'] : $_SESSION['username']); ?>!</h2>
        <p>Anda berhasil login ke dashboard.</p>

        <div class="settings-flex">
            <form method="post" class="settings-form left-form">
                <h3>Pengaturan Tampilan</h3>
                <label for="theme">Tema:</label>
                <select name="theme" id="theme">
                    <option value="light" <?php if ($_SESSION['theme'] === 'light') echo 'selected'; ?>>Terang</option>
                    <option value="dark" <?php if ($_SESSION['theme'] === 'dark') echo 'selected'; ?>>Gelap</option>
                </select>

                <label for="font_size">Ukuran Font:</label>
                <select name="font_size" id="font_size">
                    <option value="14px" <?php if ($_SESSION['font_size'] === '14px') echo 'selected'; ?>>14px</option>
                    <option value="16px" <?php if ($_SESSION['font_size'] === '16px') echo 'selected'; ?>>16px</option>
                    <option value="18px" <?php if ($_SESSION['font_size'] === '18px') echo 'selected'; ?>>18px</option>
                    <option value="20px" <?php if ($_SESSION['font_size'] === '20px') echo 'selected'; ?>>20px</option>
                </select>
                <button type="submit" name="save_theme_font">Simpan</button>
            </form>

            <form method="post" class="settings-form right-form">
                <h3>Gaya & Warna</h3>
                <label for="font_family">Gaya Font:</label>
                <select name="font_family" id="font_family">
                    <option value="Arial, sans-serif" <?php if ($_SESSION['font_family'] === 'Arial, sans-serif') echo 'selected'; ?>>Arial</option>
                    <option value="'Times New Roman', serif" <?php if ($_SESSION['font_family'] === "'Times New Roman', serif") echo 'selected'; ?>>Times New Roman</option>
                    <option value="Verdana, sans-serif" <?php if ($_SESSION['font_family'] === 'Verdana, sans-serif') echo 'selected'; ?>>Verdana</option>
                    <option value="'Courier New', monospace" <?php if ($_SESSION['font_family'] === "'Courier New', monospace") echo 'selected'; ?>>Courier New</option>
                </select>

                <label for="navbar_color">Warna Navbar:</label>
                <input type="color" name="navbar_color" id="navbar_color" value="<?php echo $_SESSION['navbar_color']; ?>">
                <button type="submit" name="save_font_navbar">Simpan</button>
            </form>
        </div>
    </div>
    <script>
        // Dissolve effect on form submit
        document.querySelectorAll('.settings-form').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                document.body.classList.add('dissolving');
            });
        });
    </script>
</body>
</html>