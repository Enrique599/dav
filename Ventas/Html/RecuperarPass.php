<?php
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
$step    = isset($_POST["step"]) ? (int)$_POST["step"] : 1;

// PASO 1: Verificar teléfono
if ($step === 1 && $_SERVER["REQUEST_METHOD"] === "POST") {
    $telefono = trim($_POST["telefono"] ?? "");
    if (!$telefono) {
        $error = "Ingresa tu número de teléfono.";
        $step = 1;
    } else {
        $stmt = $mysqli->prepare("SELECT Id_cliente, Nombre FROM cliente WHERE Telefono = ?");
        $stmt->bind_param("s", $telefono);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            $error = "No encontramos ninguna cuenta con ese teléfono.";
            $step = 1;
        } else {
            $cliente = $result->fetch_assoc();
            $step = 2; // Avanzar al paso de nueva contraseña
        }
    }
}

// PASO 2: Cambiar contraseña
if ($step === 2 && isset($_POST["nueva"]) && isset($_POST["confirmar"])) {
    $telefono  = trim($_POST["telefono"] ?? "");
    $nueva     = $_POST["nueva"] ?? "";
    $confirmar = $_POST["confirmar"] ?? "";

    if (!$nueva || !$confirmar) {
        $error = "Completa ambos campos de contraseña.";
    } elseif ($nueva !== $confirmar) {
        $error = "Las contraseñas no coinciden.";
    } elseif (strlen($nueva) < 6) {
        $error = "La contraseña debe tener al menos 6 caracteres.";
    } else {
        $hash = password_hash($nueva, PASSWORD_DEFAULT);
        $upd = $mysqli->prepare("UPDATE cliente SET Contrasena = ? WHERE Telefono = ?");
        $upd->bind_param("ss", $hash, $telefono);
        if ($upd->execute()) {
            $success = true;
        } else {
            $error = "Error al actualizar. Intenta de nuevo.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar contraseña — DAV Tienda</title>
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

        /* Steps indicator */
        .steps {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0;
            margin-bottom: 28px;
        }
        .step-dot {
            width: 32px; height: 32px;
            border-radius: 50%;
            background: rgba(255,255,255,0.4);
            border: 2px solid rgba(255,255,255,0.6);
            display: flex; align-items: center; justify-content: center;
            font-weight: 800; font-size: 14px; color: #4a6fa5;
        }
        .step-dot.active { background: #4f8ef7; border-color: #2563eb; color: #fff; }
        .step-dot.done { background: #22c55e; border-color: #16a34a; color: #fff; }
        .step-line { width: 40px; height: 2px; background: rgba(255,255,255,0.5); }
        .step-line.done { background: #22c55e; }

        h1 { text-align: center; color: #1a3a6e; font-size: 26px; font-weight: 800; margin-bottom: 6px; }
        .subtitle { text-align: center; color: #4a6fa5; font-size: 14px; margin-bottom: 28px; }

        .form-card {
            background: rgba(255,255,255,0.6);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255,255,255,0.7);
            border-radius: 20px;
            padding: 36px 32px;
            box-shadow: 0 8px 32px rgba(31,77,153,0.15);
        }

        .alert { padding: 12px 16px; border-radius: 10px; margin-bottom: 18px; font-weight: 600; font-size: 14px; }
        .alert-error { background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }
        .alert-success { background: #dcfce7; color: #166534; border: 1px solid #86efac; }

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
        .fg input:focus { outline: none; border-color: #4f8ef7; box-shadow: 0 0 0 3px rgba(79,142,247,0.2); }
        .fg input::placeholder { color: #9ab4d0; }

        .help-text { font-size: 12px; color: #6b7280; margin-top: 4px; }

        .btn-primary {
            width: 100%;
            background: linear-gradient(135deg, #4f8ef7, #2563eb);
            color: #fff;
            border: none;
            padding: 14px;
            border-radius: 10px;
            font-family: 'Nunito', sans-serif;
            font-size: 16px;
            font-weight: 800;
            cursor: pointer;
            transition: transform .15s, box-shadow .15s;
            box-shadow: 0 4px 15px rgba(79,142,247,0.4);
            margin-bottom: 12px;
        }
        .btn-primary:hover { transform: translateY(-2px); }
        .btn-back { display: block; text-align: center; color: #4a6fa5; font-size: 14px; font-weight: 700; text-decoration: none; }
        .btn-back:hover { color: #1a3a6e; }

        .success-screen { text-align: center; padding: 10px 0; }
        .success-icon { font-size: 60px; margin-bottom: 14px; }
        .success-screen h2 { font-size: 22px; font-weight: 800; color: #1a3a6e; margin-bottom: 8px; }
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

        .cliente-info {
            background: rgba(79,142,247,0.1);
            border: 1px solid rgba(79,142,247,0.3);
            border-radius: 10px;
            padding: 12px 16px;
            margin-bottom: 18px;
            font-size: 14px;
            color: #1a3a6e;
        }
        .cliente-info strong { font-weight: 800; }
    </style>
</head>
<body>

<header class="site-header">
    <img src="../Imagenes/logo.jpg" alt="Logo">
    <div class="brand">DAV <span>Tienda</span></div>
</header>

<div class="card-wrap">

    <div class="steps">
        <div class="step-dot <?php echo $step >= 1 ? ($step > 1 ? 'done' : 'active') : ''; ?>">
            <?php echo $step > 1 ? '✓' : '1'; ?>
        </div>
        <div class="step-line <?php echo $step > 1 ? 'done' : ''; ?>"></div>
        <div class="step-dot <?php echo $step >= 2 ? ($success ? 'done' : 'active') : ''; ?>">
            <?php echo $success ? '✓' : '2'; ?>
        </div>
        <div class="step-line <?php echo $success ? 'done' : ''; ?>"></div>
        <div class="step-dot <?php echo $success ? 'done' : ''; ?>">
            <?php echo $success ? '✓' : '3'; ?>
        </div>
    </div>

    <h1>🔑 Recuperar contraseña</h1>
    <p class="subtitle">
        <?php
        if ($success) echo "Contraseña actualizada";
        elseif ($step === 2) echo "Crea tu nueva contraseña";
        else echo "Verifica tu número de teléfono";
        ?>
    </p>

    <div class="form-card">

        <?php if ($success): ?>
        <div class="success-screen">
            <div class="success-icon">🎊</div>
            <h2>¡Contraseña actualizada!</h2>
            <p>Tu contraseña fue cambiada exitosamente.<br>Ahora puedes iniciar sesión.</p>
            <a href="Inicioses.php" class="btn-go">Iniciar sesión →</a>
        </div>

        <?php elseif ($step === 2): ?>
        <?php if ($error): ?>
        <div class="alert alert-error">⚠️ <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if (isset($cliente)): ?>
        <div class="cliente-info">👤 Cuenta encontrada: <strong><?php echo htmlspecialchars($cliente['Nombre']); ?></strong></div>
        <?php endif; ?>

        <form method="POST" action="RecuperarPass.php">
            <input type="hidden" name="step" value="2">
            <input type="hidden" name="telefono" value="<?php echo htmlspecialchars($_POST["telefono"] ?? ""); ?>">

            <div class="fg">
                <label>Nueva contraseña</label>
                <div class="icon-input"><span>🔒</span>
                <input type="password" name="nueva" placeholder="Mínimo 6 caracteres" required autofocus></div>
            </div>

            <div class="fg">
                <label>Confirmar contraseña</label>
                <div class="icon-input"><span>🔑</span>
                <input type="password" name="confirmar" placeholder="Repite la nueva contraseña" required></div>
            </div>

            <button type="submit" class="btn-primary">💾 Guardar contraseña</button>
            <a href="RecuperarPass.php" class="btn-back">← Volver</a>
        </form>

        <?php else: ?>
        <?php if ($error): ?>
        <div class="alert alert-error">⚠️ <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="RecuperarPass.php">
            <input type="hidden" name="step" value="1">

            <div class="fg">
                <label>Número de teléfono</label>
                <div class="icon-input"><span>📞</span>
                <input type="tel" name="telefono" placeholder="El teléfono de tu cuenta" value="<?php echo htmlspecialchars($_POST['telefono'] ?? ''); ?>" required autofocus></div>
                <span class="help-text">Ingresa el número con el que creaste tu cuenta</span>
            </div>

            <button type="submit" class="btn-primary">🔍 Buscar cuenta</button>
            <a href="Inicioses.php" class="btn-back">← Volver al inicio de sesión</a>
        </form>
        <?php endif; ?>

    </div>
</div>

</body>
</html>
