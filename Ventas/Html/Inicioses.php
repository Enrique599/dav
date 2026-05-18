<?php
session_start();

$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "dav";
$mysqli = new mysqli($servername, $username, $password, $dbname);
if ($mysqli->connect_error) {
    die("Error de conexión: " . $mysqli->connect_error);
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $telefono  = trim($_POST["telefono"] ?? "");
    $contrasena = $_POST["contrasena"] ?? "";

    if (!$telefono || !$contrasena) {
        $error = "Por favor completa todos los campos.";
    } else {
        $stmt = $mysqli->prepare("SELECT Id_cliente, Nombre, Ap_paterno, Contrasena FROM cliente WHERE Telefono = ?");
        $stmt->bind_param("s", $telefono);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            $error = "Teléfono o contraseña incorrectos.";
        } else {
            $row = $result->fetch_assoc();
            if (password_verify($contrasena, $row["Contrasena"])) {
                $_SESSION["id_cliente"]  = $row["Id_cliente"];
                $_SESSION["nombre"]      = $row["Nombre"] . " " . $row["Ap_paterno"];
                header("Location: Tiendaden.html");
                exit;
            } else {
                $error = "Teléfono o contraseña incorrectos.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión —  </title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            min-height: 100vh;
            background: linear-gradient(160deg, #8db8f3 0%, #c5d9f7 45%, #e0e0e0 100%);
            font-family: 'Nunito', sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .site-header {
            width: 100%;
            background: rgba(255,255,255,0.25);
            backdrop-filter: blur(12px);
            padding: 12px 30px;
            display: flex;
            align-items: center;
            gap: 14px;
            border-bottom: 1px solid rgba(255,255,255,0.4);
        }
        .site-header img { width: 52px; height: 52px; border-radius: 50%; object-fit: cover; border: 2px solid #fff; }
        .site-header .brand { font-size: 20px; font-weight: 800; color: #1a3a6e; }
        .site-header .brand span { color: #4f8ef7; }

        .card-wrap {
            width: 100%;
            max-width: 420px;
            margin: 60px auto;
            padding: 0 16px;
        }

        h1 {
            text-align: center;
            color: #1a3a6e;
            font-size: 30px;
            font-weight: 800;
            margin-bottom: 6px;
        }
        .subtitle { text-align: center; color: #4a6fa5; font-size: 14px; margin-bottom: 28px; }

        .form-card {
            background: rgba(255,255,255,0.6);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255,255,255,0.7);
            border-radius: 20px;
            padding: 36px 32px;
            box-shadow: 0 8px 32px rgba(31,77,153,0.15);
        }

        .alert-error {
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 18px;
            font-weight: 600;
            font-size: 14px;
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fca5a5;
        }

        .fg { display: flex; flex-direction: column; margin-bottom: 16px; }
        .fg label { font-size: 12px; font-weight: 700; color: #1a3a6e; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 6px; }
        .icon-input { position: relative; display: flex; align-items: center; }
        .icon-input span { position: absolute; left: 12px; font-size: 18px; pointer-events: none; }
        .fg input {
            width: 100%;
            padding: 13px 14px 13px 40px;
            border: 1.5px solid rgba(141,184,243,0.5);
            border-radius: 10px;
            background: rgba(255,255,255,0.7);
            font-family: 'Nunito', sans-serif;
            font-size: 15px;
            color: #1a3a6e;
            transition: border-color .2s, box-shadow .2s;
        }
        .fg input:focus {
            outline: none;
            border-color: #4f8ef7;
            box-shadow: 0 0 0 3px rgba(79,142,247,0.2);
        }
        .fg input::placeholder { color: #9ab4d0; }

        .recover-link {
            text-align: right;
            margin-top: -8px;
            margin-bottom: 18px;
            font-size: 13px;
        }
        .recover-link a { color: #2563eb; font-weight: 700; text-decoration: none; }
        .recover-link a:hover { text-decoration: underline; }

        .btn-row { display: flex; gap: 12px; }

        .btn-primary {
            flex: 1;
            background: linear-gradient(135deg, #4f8ef7, #2563eb);
            color: #fff;
            border: none;
            padding: 13px;
            border-radius: 10px;
            font-family: 'Nunito', sans-serif;
            font-size: 16px;
            font-weight: 800;
            cursor: pointer;
            transition: transform .15s, box-shadow .15s;
            box-shadow: 0 4px 15px rgba(79,142,247,0.4);
        }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(79,142,247,0.5); }

        .btn-cancel {
            flex: 1;
            background: rgba(239,68,68,0.85);
            color: #fff;
            padding: 13px;
            border-radius: 10px;
            text-decoration: none;
            font-size: 16px;
            font-weight: 800;
            text-align: center;
            transition: background .2s;
        }
        .btn-cancel:hover { background: #dc2626; }

        .divider { text-align: center; margin: 22px 0 14px; color: #4a6fa5; font-size: 13px; }
        .link-register { text-align: center; font-size: 14px; color: #4a6fa5; }
        .link-register a { color: #2563eb; font-weight: 700; text-decoration: none; }
        .link-register a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<header class="site-header">
    <img src="../Imagenes/logo.jpg" alt="Logo">
    <div class="brand">DAV <span>Tienda</span></div>
</header>

<div class="card-wrap">
    <h1>Iniciar sesión</h1>
    <p class="subtitle">Bienvenido de vuelta</p>

    <div class="form-card">

        <?php if ($error): ?>
        <div class="alert-error">⚠️ <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="Inicioses.php">
            <div class="fg">
                <label>Teléfono</label>
                <div class="icon-input"><span>📞</span>
                <input type="tel" name="telefono" placeholder="Tu número de teléfono" value="<?php echo htmlspecialchars($_POST['telefono'] ?? ''); ?>" required autofocus></div>
            </div>

            <div class="fg">
                <label>Contraseña</label>
                <div class="icon-input"><span>🔒</span>
                <input type="password" name="contrasena" placeholder="Tu contraseña" required></div>
            </div>

            <div class="recover-link">
                <a href="RecuperarPass.php">¿Olvidaste tu contraseña?</a>
            </div>

            <div class="btn-row">
                <button type="submit" class="btn-primary">→ Entrar</button>
                <a href="Inicial.html" class="btn-cancel">✖ Cancelar</a>
            </div>
        </form>

        <div class="divider">— ¿No tienes cuenta? —</div>
        <div class="link-register"><a href="Crearcu.php">Crear cuenta nueva</a></div>

    </div>
</div>

</body>
</html>
