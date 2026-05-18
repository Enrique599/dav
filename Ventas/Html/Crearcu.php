<?php
// Conexión a la base de datos
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "dav";
$mysqli = new mysqli($servername, $username, $password, $dbname);
if ($mysqli->connect_error) {
    die("Error de conexión: " . $mysqli->connect_error);
}

$error   = "";
$success = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre    = trim($_POST["nombre"] ?? "");
    $paterno   = trim($_POST["paterno"] ?? "");
    $telefono  = trim($_POST["telefono"] ?? "");
    $contrasena = $_POST["contrasena"] ?? "";
    $confirmar  = $_POST["confirmar"] ?? "";
    $direccion  = trim($_POST["direccion"] ?? "");
    $cp         = trim($_POST["cp"] ?? "");

    if (!$nombre || !$paterno || !$telefono || !$contrasena || !$direccion || !$cp) {
        $error = "Por favor completa todos los campos.";
    } elseif ($contrasena !== $confirmar) {
        $error = "Las contraseñas no coinciden.";
    } elseif (strlen($contrasena) < 6) {
        $error = "La contraseña debe tener al menos 6 caracteres.";
    } else {
        // Verificar si el teléfono ya existe
        $check = $mysqli->prepare("SELECT Id_cliente FROM cliente WHERE Telefono = ?");
        $check->bind_param("s", $telefono);
        $check->execute();
        $check->store_result();
        if ($check->num_rows > 0) {
            $error = "Ya existe una cuenta con ese número de teléfono.";
        } else {
            $hash = password_hash($contrasena, PASSWORD_DEFAULT);
            $stmt = $mysqli->prepare("INSERT INTO cliente (Nombre, Ap_paterno, Telefono, Contrasena, Direccion, CP) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $nombre, $paterno, $telefono, $hash, $direccion, $cp);
            if ($stmt->execute()) {
                $success = true;
            } else {
                $error = "Error al guardar. Intenta de nuevo.";
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
    <title>Crear cuenta — DAV Tienda</title>
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

        /* Header */
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

        /* Card */
        .card-wrap {
            width: 100%;
            max-width: 520px;
            margin: 40px auto;
            padding: 0 16px;
        }

        h1 {
            text-align: center;
            color: #1a3a6e;
            font-size: 28px;
            font-weight: 800;
            margin-bottom: 6px;
        }
        .subtitle {
            text-align: center;
            color: #4a6fa5;
            font-size: 14px;
            margin-bottom: 24px;
        }

        .form-card {
            background: rgba(255,255,255,0.6);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255,255,255,0.7);
            border-radius: 20px;
            padding: 36px 32px;
            box-shadow: 0 8px 32px rgba(31,77,153,0.15);
        }

        .alert {
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 18px;
            font-weight: 600;
            font-size: 14px;
        }
        .alert-error { background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }
        .alert-success { background: #dcfce7; color: #166534; border: 1px solid #86efac; }

        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }

        .fg { display: flex; flex-direction: column; margin-bottom: 14px; }
        .fg label { font-size: 12px; font-weight: 700; color: #1a3a6e; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 6px; }
        .fg .icon-input { position: relative; display: flex; align-items: center; }
        .fg .icon-input span { position: absolute; left: 12px; font-size: 18px; pointer-events: none; }
        .fg input {
            width: 100%;
            padding: 11px 14px 11px 40px;
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

        .btn-row { display: flex; gap: 12px; margin-top: 8px; }

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

        .divider { text-align: center; margin: 18px 0 14px; color: #4a6fa5; font-size: 14px; }
        .link-login { text-align: center; font-size: 14px; color: #4a6fa5; }
        .link-login a { color: #2563eb; font-weight: 700; text-decoration: none; }
        .link-login a:hover { text-decoration: underline; }

        /* Success screen */
        .success-screen {
            text-align: center;
            padding: 20px 0;
        }
        .success-icon { font-size: 60px; margin-bottom: 14px; }
        .success-screen h2 { font-size: 24px; font-weight: 800; color: #1a3a6e; margin-bottom: 8px; }
        .success-screen p { color: #4a6fa5; margin-bottom: 24px; }
        .btn-go {
            display: inline-block;
            background: linear-gradient(135deg, #4f8ef7, #2563eb);
            color: #fff;
            padding: 14px 36px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 800;
            font-size: 16px;
            box-shadow: 0 4px 15px rgba(79,142,247,0.4);
        }

        @media(max-width:480px) { .form-row { grid-template-columns: 1fr; } .form-card { padding: 24px 18px; } }
    </style>
</head>
<body>

<header class="site-header">
    <img src="../Imagenes/logo.jpg" alt="Logo">
    <div class="brand">DAV <span>Tienda</span></div>
</header>

<div class="card-wrap">
    <h1>Crear cuenta</h1>
    <p class="subtitle">Regístrate para empezar a comprar</p>

    <div class="form-card">

        <?php if ($success): ?>
        <div class="success-screen">
            <div class="success-icon">🎉</div>
            <h2>¡Cuenta creada!</h2>
            <p>Tu cuenta fue registrada exitosamente.<br>Ya puedes iniciar sesión.</p>
            <a href="Inicioses.php" class="btn-go">Iniciar sesión →</a>
        </div>

        <?php else: ?>

        <?php if ($error): ?>
        <div class="alert alert-error">⚠️ <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="Crearcu.php">
            <div class="form-row">
                <div class="fg">
                    <label>Nombre</label>
                    <div class="icon-input"><span>👤</span>
                    <input type="text" name="nombre" placeholder="Tu nombre" value="<?php echo htmlspecialchars($_POST['nombre'] ?? ''); ?>" required></div>
                </div>
                <div class="fg">
                    <label>Apellido Paterno</label>
                    <div class="icon-input"><span>👥</span>
                    <input type="text" name="paterno" placeholder="Apellido" value="<?php echo htmlspecialchars($_POST['paterno'] ?? ''); ?>" required></div>
                </div>
            </div>

            <div class="fg">
                <label>Teléfono (se usará para iniciar sesión)</label>
                <div class="icon-input"><span>📞</span>
                <input type="tel" name="telefono" placeholder="10 dígitos" value="<?php echo htmlspecialchars($_POST['telefono'] ?? ''); ?>" required></div>
            </div>

            <div class="form-row">
                <div class="fg">
                    <label>Contraseña</label>
                    <div class="icon-input"><span>🔒</span>
                    <input type="password" name="contrasena" placeholder="Mínimo 6 caracteres" required></div>
                </div>
                <div class="fg">
                    <label>Confirmar contraseña</label>
                    <div class="icon-input"><span>🔑</span>
                    <input type="password" name="confirmar" placeholder="Repite la contraseña" required></div>
                </div>
            </div>

            <div class="form-row">
                <div class="fg">
                    <label>Dirección</label>
                    <div class="icon-input"><span>🏠</span>
                    <input type="text" name="direccion" placeholder="Tu dirección" value="<?php echo htmlspecialchars($_POST['direccion'] ?? ''); ?>" required></div>
                </div>
                <div class="fg">
                    <label>Código Postal</label>
                    <div class="icon-input"><span>📮</span>
                    <input type="text" name="cp" placeholder="5 dígitos" maxlength="5" value="<?php echo htmlspecialchars($_POST['cp'] ?? ''); ?>" required></div>
                </div>
            </div>

            <div class="btn-row">
                <button type="submit" class="btn-primary">✅ Crear cuenta</button>
                <a href="Inicial.html" class="btn-cancel">✖ Cancelar</a>
            </div>
        </form>

        <div class="divider">— ¿Ya tienes cuenta? —</div>
        <div class="link-login"><a href="Inicioses.php">Iniciar sesión aquí</a></div>

        <?php endif; ?>

    </div>
</div>

</body>
</html>
