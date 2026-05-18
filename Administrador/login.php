<?php
session_start();

// Si ya está logueado, redirigir al dashboard
if (isset($_SESSION['admin_id'])) {
    header("Location: Cliente/dashboard.php");
    exit;
}

$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "dav";
$mysqli = new mysqli($servername, $username, $password, $dbname);

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $telefono  = trim($_POST["telefono"] ?? "");
    $contrasena = $_POST["contrasena"] ?? "";

    if (!$telefono || !$contrasena) {
        $error = "Completa todos los campos.";
    } else {
        // Verificar en tabla empleado
        $stmt = $mysqli->prepare("SELECT Id_empleado, Nombre, Ap_paterno, Contrasena, rol FROM empleado WHERE Telefono = ?");
        $stmt->bind_param("s", $telefono);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $emp = $result->fetch_assoc();
            if (password_verify($contrasena, $emp['Contrasena'])) {
                $_SESSION['admin_id']     = $emp['Id_empleado'];
                $_SESSION['admin_nombre'] = $emp['Nombre'] . ' ' . $emp['Ap_paterno'];
                $_SESSION['admin_rol']    = $emp['rol'];
                header("Location: Cliente/dashboard.php");
                exit;
            }
        }

        // Si no hay empleados, permitir acceso con credenciales por defecto (solo desarrollo)
        // Credenciales por defecto: admin / admin123
        if ($telefono === "admin" && $contrasena === "admin123") {
            $_SESSION['admin_id']     = 0;
            $_SESSION['admin_nombre'] = "Administrador";
            $_SESSION['admin_rol']    = "admin";
            header("Location: Cliente/dashboard.php");
            exit;
        }

        $error = "Credenciales incorrectas.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso — Sistema Administrativo</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #0f1117;
            --card: #1e2130;
            --border: #2a2f45;
            --accent: #4f8ef7;
            --accent2: #22d3a5;
            --text: #e4e8f0;
            --muted: #7a8099;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            min-height: 100vh;
            background: var(--bg);
            font-family: 'DM Sans', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        /* Decorative background */
        body::before {
            content: '';
            position: absolute;
            width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(79,142,247,0.12) 0%, transparent 70%);
            top: -100px; right: -100px;
            border-radius: 50%;
            pointer-events: none;
        }
        body::after {
            content: '';
            position: absolute;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(34,211,165,0.08) 0%, transparent 70%);
            bottom: -80px; left: -80px;
            border-radius: 50%;
            pointer-events: none;
        }

        .login-box {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 48px 44px;
            width: 100%;
            max-width: 420px;
            position: relative;
            z-index: 1;
            box-shadow: 0 20px 60px rgba(0,0,0,0.5);
        }

        .logo-area {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 36px;
        }
        .logo-icon {
            width: 44px; height: 44px;
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px;
        }
        .logo-text { font-family: 'Syne', sans-serif; font-weight: 800; font-size: 20px; color: var(--text); }
        .logo-text span { color: var(--accent); }

        h1 { font-family: 'Syne', sans-serif; font-size: 26px; font-weight: 800; color: var(--text); margin-bottom: 6px; }
        .sub { color: var(--muted); font-size: 14px; margin-bottom: 32px; }

        .alert-error {
            background: rgba(239,68,68,0.12);
            border: 1px solid rgba(239,68,68,0.3);
            color: #fca5a5;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 14px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .fg { margin-bottom: 18px; }
        .fg label {
            display: block;
            font-size: 11px;
            font-weight: 600;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }
        .input-wrap { position: relative; }
        .input-wrap i {
            position: absolute;
            left: 14px; top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            font-size: 14px;
        }
        .fg input {
            width: 100%;
            background: rgba(255,255,255,0.04);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 13px 14px 13px 40px;
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
            font-size: 15px;
            transition: border-color .2s, background .2s;
        }
        .fg input:focus {
            outline: none;
            border-color: var(--accent);
            background: rgba(79,142,247,0.05);
        }
        .fg input::placeholder { color: #3a4060; }

        .btn-login {
            width: 100%;
            background: linear-gradient(135deg, var(--accent), #2563eb);
            color: #fff;
            border: none;
            padding: 14px;
            border-radius: 10px;
            font-family: 'Syne', sans-serif;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: opacity .2s, transform .15s;
            box-shadow: 0 4px 20px rgba(79,142,247,0.3);
            margin-top: 8px;
            letter-spacing: .5px;
        }
        .btn-login:hover { opacity: .9; transform: translateY(-1px); }

        .footer-note {
            text-align: center;
            margin-top: 28px;
            font-size: 12px;
            color: var(--muted);
        }
        .footer-note a { color: var(--accent); text-decoration: none; }

        .hint-box {
            background: rgba(79,142,247,0.07);
            border: 1px solid rgba(79,142,247,0.2);
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 12px;
            color: var(--muted);
            margin-bottom: 20px;
        }
        .hint-box strong { color: var(--accent); }
    </style>
</head>
<body>

<div class="login-box">
    <div class="logo-area">
        <div class="logo-icon"><i class="fas fa-shield-alt" style="color:#fff"></i></div>
        <div class="logo-text">DAV <span>Admin</span></div>
    </div>

    <h1>Bienvenido</h1>
    <p class="sub">Acceso al panel de administración</p>

    <?php if ($error): ?>
    <div class="alert-error"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="hint-box">
        💡 Acceso por defecto: <strong>admin</strong> / <strong>admin123</strong><br>
        O usa las credenciales del empleado registrado en BD.
    </div>

    <form method="POST" action="login.php">
        <div class="fg">
            <label>Usuario / Teléfono</label>
            <div class="input-wrap">
                <i class="fas fa-user"></i>
                <input type="text" name="telefono" placeholder="Teléfono o 'admin'" value="<?php echo htmlspecialchars($_POST['telefono'] ?? ''); ?>" required autofocus>
            </div>
        </div>

        <div class="fg">
            <label>Contraseña</label>
            <div class="input-wrap">
                <i class="fas fa-lock"></i>
                <input type="password" name="contrasena" placeholder="••••••••" required>
            </div>
        </div>

        <button type="submit" class="btn-login">
            <i class="fas fa-sign-in-alt"></i> Ingresar al sistema
        </button>
    </form>

    <div class="footer-note">
        ¿Eres cliente? <a href="../Ventas/Html/Inicial.html">Ir a la tienda</a>
    </div>
</div>

</body>
</html>
