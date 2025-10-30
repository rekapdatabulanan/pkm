<?php include 'config.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Login Absensi <br> PUSKES AMSEL</title>
    <style>
        /* Reset & basic */
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            overflow: hidden;
        }

        /* Floating animated circles */
        body::before, body::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            background: rgba(255,255,255,0.15);
            animation: float 10s ease-in-out infinite;
        }
        body::before { width: 300px; height: 300px; top: -100px; left: -100px; }
        body::after { width: 200px; height: 200px; bottom: -50px; right: -50px; }

        @keyframes float {
            0% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
            100% { transform: translateY(0) rotate(360deg); }
        }

        /* Container */
        .login-container {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(15px);
            padding: 50px 40px;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
            width: 350px;
            text-align: center;
        }

        .login-container h2 {
            margin-bottom: 30px;
            color: #fff;
            font-size: 28px;
            letter-spacing: 1px;
        }

        /* Inputs */
        .login-container input[type="text"],
        .login-container input[type="password"] {
            width: 100%;
            padding: 12px 20px;
            margin: 10px 0 20px 0;
            border: none;
            border-radius: 50px;
            background: rgba(255,255,255,0.25);
            color: #fff;
            font-size: 14px;
            outline: none;
            transition: 0.3s;
        }

        .login-container input:focus {
            background: rgba(255,255,255,0.45);
            box-shadow: 0 0 10px rgba(255,255,255,0.6);
        }

        /* Button */
        .login-container button {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 50px;
            background: linear-gradient(45deg, #6a11cb, #2575fc);
            color: #fff;
            font-weight: bold;
            font-size: 16px;
            cursor: pointer;
            transition: 0.4s;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .login-container button:hover {
            background: linear-gradient(45deg, #2575fc, #6a11cb);
            transform: scale(1.05);
            box-shadow: 0 8px 20px rgba(0,0,0,0.3);
        }

        /* Error message */
        .error {
            margin-top: 15px;
            color: #ff4d4d;
            font-weight: bold;
        }

        /* Links */
        .login-container a {
            display: block;
            margin-top: 15px;
            color: #fff;
            text-decoration: none;
            transition: 0.3s;
        }
        .login-container a:hover {
            text-decoration: underline;
            color: #fffc00;
        }

        /* Responsive */
        @media (max-width: 400px) {
            .login-container {
                width: 90%;
                padding: 40px 20px;
            }
        }
    </style>
</head>
<body>
<div class="login-container">
    <h2>LOGIN</BR> PUSKES AMSEL</h2>
    <form method="post">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button name="login">Login</button>
    </form>
    <?php
    if (isset($_POST['login'])) {
        $u = $_POST['username'];
        $p = $_POST['password'];
        $q = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$u' AND password='$p'");
        $data = mysqli_fetch_assoc($q);

        if ($data) {
            $_SESSION['user'] = $data;
            if ($data['role'] == 'admin') header("Location: admin/dashboard.php");
            else header("Location: user/dashboard.php");
        } else {
            echo "<p class='error'>Username atau password salah!</p>";
        }
    }
    ?>
</div>
</body>
</html>
