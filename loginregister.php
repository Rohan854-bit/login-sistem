<?php
session_start();

// --- Logika register ---
$reg_error = '';
$reg_success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $koneksi = mysqli_connect("localhost", "root", "", "datalake");
    if (!$koneksi) {
        $reg_error = "Koneksi database gagal: " . mysqli_connect_error();
    } else {
        $nama_lengkap = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
        $email = mysqli_real_escape_string($koneksi, $_POST['email']);
        $username = mysqli_real_escape_string($koneksi, $_POST['username']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        if ($password !== $confirm_password) {
            $reg_error = "Konfirmasi password tidak cocok.";
        } else {
            $cek = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username' OR email='$email'");
            if (mysqli_num_rows($cek) > 0) {
                $reg_error = "Username atau email sudah terdaftar.";
            } else {
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $sql = "INSERT INTO users (nama_lengkap, email, username, password) VALUES ('$nama_lengkap', '$email', '$username', '$password_hash')";
                if (mysqli_query($koneksi, $sql)) {
                    $reg_success = "Registrasi berhasil! Silakan login.";
                } else {
                    $reg_error = "Registrasi gagal: " . mysqli_error($koneksi);
                }
            }
        }
        mysqli_close($koneksi);
    }
}

// --- Logika login (contoh redirect jika sukses) ---
$login_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $koneksi = mysqli_connect("localhost", "root", "", "datalake");
    if (!$koneksi) {
        $login_error = "Koneksi database gagal.";
    } else {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $query = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username'");
        $user = mysqli_fetch_assoc($query);
        if ($user && password_verify($password, $user['password'])) {
            // sukses login (bisa arahkan ke halaman lain)
            $_SESSION['username'] = $user['username'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            header("Location: dashboard.php");
            exit();
        } else {
            $login_error = "Username atau password salah.";
        }
        mysqli_close($koneksi);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login & Register</title>
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap");
        * {
            margin: 0; padding: 0; box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }
        body {
            display: flex; justify-content: center; align-items: center;
            min-height: 100vh; background-color: rgb(255, 165, 165);
        }
        .container {
            width: 420px;
            background: rgb(82, 154, 255);
            padding: 30px 40px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, .1);
        }
        .tab {
            display: flex;
            justify-content: space-around;
            margin-bottom: 20px;
        }
        .tab button {
            padding: 10px 30px;
            background: #fff;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            font-weight: 600;
        }
        .tab button.active {
            background: #45a049;
            color: white;
        }
        h2 {
            text-align: center;
            color: white;
            margin-bottom: 20px;
            margin-right: 18px;
        }
        form {
            display: none;
        }
        form.active {
            display: block;
        }
        input[type="text"], input[type="password"], input[type="email"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid black;
            border-radius: 5px;
            background: transparent;
            color: white;
        }
        input[type="text"]::placeholder,
        input[type="password"]::placeholder,
        input[type="email"]::placeholder {
            color: #fff;
            opacity: 1;
        }
        input[type="submit"] {
            width: 70%;
            height: 45px;
            background: #fff;
            border: none;
            border-radius: 40px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 0 10px rgba(0,0,0,.1);
            margin-top: 10px;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
            color: white;
        }
        .message { text-align: center; color: red; margin-bottom: 10px; }
        .success { color: green; }
    </style>
</head>
<body>
    <div class="container">
        <div class="tab">
            <button onclick="showForm('loginForm')" id="loginTab" class="active">Login</button>
            <button onclick="showForm('registerForm')" id="registerTab">Register</button>
        </div>

        <!-- Login Form -->
        <form method="post" id="loginForm" class="active">
            <?php if ($login_error): ?>
                <div class="message"><?php echo $login_error; ?></div>
            <?php endif; ?>
              <h2>Login</h2>
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <center><input type="submit" name="login" value="Login"></center>
        </form>

        <!-- Register Form -->
        <form method="post" id="registerForm">
            <?php if ($reg_error): ?>
                <div class="message"><?php echo $reg_error; ?></div>
            <?php elseif ($reg_success): ?>
                <div class="message success"><?php echo $reg_success; ?></div>
            <?php endif; ?>
              <h2>Register</h2>
            <input type="text" name="nama_lengkap" placeholder="Nama Lengkap" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="confirm_password" placeholder="Konfirmasi Password" required>
            <center><input type="submit" name="register" value="Register"></center>
        </form>
    </div>

    <script>
        function showForm(formId) {
            document.getElementById('loginForm').classList.remove('active');
            document.getElementById('registerForm').classList.remove('active');
            document.getElementById('loginTab').classList.remove('active');
            document.getElementById('registerTab').classList.remove('active');
            document.getElementById(formId).classList.add('active');
            if (formId === 'loginForm') {
                document.getElementById('loginTab').classList.add('active');
            } else {
                document.getElementById('registerTab').classList.add('active');
            }
        }
    </script>
</body>
</html>