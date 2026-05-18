<?php
// layout_header.php — Incluir al inicio de cada página
// Uso: include("layout_header.php"); con $page_id y $page_title definidos antes
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo isset($page_title) ? $page_title . ' — Sistema' : 'Sistema'; ?></title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    :root {
      --bg: #0f1117;
      --sb: #1a1d27;
      --sbw: 255px;
      --accent: #4f8ef7;
      --accent2: #22d3a5;
      --accent3: #f7c948;
      --accent4: #f75e5e;
      --card: #1e2130;
      --border: #2a2f45;
      --text: #e4e8f0;
      --muted: #7a8099;
      --hh: 60px;
      --green: #22c55e;
      --orange: #f97316;
      --red: #ef4444;
      --purple: #a855f7;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', system-ui, sans-serif;
      background: var(--bg);
      color: var(--text);
      display: flex;
      min-height: 100vh;
    }

    /* ── SIDEBAR ── */
    #sb {
      width: var(--sbw);
      background: var(--sb);
      display: flex;
      flex-direction: column;
      border-right: 1px solid var(--border);
      position: fixed;
      top: 0;
      left: 0;
      height: 100vh;
      overflow-y: auto;
      overflow-x: hidden;
      transition: width .25s;
      z-index: 200;
      flex-shrink: 0;
    }

    #sb.col {
      width: 60px;
    }

    .sb-head {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 14px 16px;
      border-bottom: 1px solid var(--border);
      min-height: var(--hh);
    }

    .logo-c {
      width: 34px;
      height: 34px;
      border-radius: 50%;
      background: linear-gradient(135deg, #4f8ef7, #22d3a5);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 15px;
      flex-shrink: 0;
    }

    .logo-t {
      font-weight: 700;
      font-size: 14px;
      white-space: nowrap;
    }

    #sb.col .logo-t {
      display: none;
    }

    .sb-user {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 12px 16px;
      border-bottom: 1px solid var(--border);
    }

    .av {
      width: 32px;
      height: 32px;
      border-radius: 50%;
      background: linear-gradient(135deg, var(--purple), var(--accent));
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 12px;
      font-weight: 700;
      flex-shrink: 0;
    }

    .un {
      font-size: 13px;
      font-weight: 600;
      white-space: nowrap;
    }

    .ur {
      font-size: 11px;
      color: var(--muted);
    }

    #sb.col .sb-user>div {
      display: none;
    }

    .sb-lbl {
      font-size: 10px;
      font-weight: 700;
      letter-spacing: .08em;
      color: var(--muted);
      padding: 12px 16px 5px;
      text-transform: uppercase;
    }

    #sb.col .sb-lbl {
      display: none;
    }

    .ni {
      display: flex;
      align-items: center;
      gap: 11px;
      padding: 10px 16px;
      cursor: pointer;
      color: var(--muted);
      font-size: 13px;
      font-weight: 500;
      text-decoration: none;
      transition: background .15s;
      border-right: 3px solid transparent;
    }

    .ni:hover {
      background: rgba(79, 142, 247, .08);
      color: var(--text);
      border-right-color: transparent;
    }

    .ni.act {
      background: rgba(79, 142, 247, .15);
      color: var(--accent);
      border-right-color: var(--accent);
    }

    .ni i {
      width: 17px;
      text-align: center;
      font-size: 13px;
      flex-shrink: 0;
    }

    .ni span {
      white-space: nowrap;
    }

    #sb.col .ni span {
      display: none;
    }

    .nbadge {
      margin-left: auto;
      background: var(--accent4);
      color: #fff;
      border-radius: 999px;
      font-size: 10px;
      padding: 1px 6px;
      font-weight: 700;
    }

    #sb.col .nbadge {
      display: none;
    }

    /* ── MAIN ── */
    #main {
      flex: 1;
      display: flex;
      flex-direction: column;
      margin-left: var(--sbw);
      transition: margin-left .25s;
    }

    body.col-mode #main {
      margin-left: 60px;
    }

    /* ── TOPBAR ── */
    .topbar {
      height: var(--hh);
      background: var(--sb);
      border-bottom: 1px solid var(--border);
      display: flex;
      align-items: center;
      padding: 0 20px;
      gap: 14px;
      position: sticky;
      top: 0;
      z-index: 100;
    }

    .tb-toggle {
      background: none;
      border: none;
      color: var(--text);
      cursor: pointer;
      font-size: 18px;
    }

    .tb-title {
      font-size: 15px;
      font-weight: 600;
    }

    .tb-right {
      margin-left: auto;
      display: flex;
      align-items: center;
      gap: 14px;
    }

    .tb-icon {
      position: relative;
      cursor: pointer;
      color: var(--muted);
      font-size: 15px;
    }

    .tb-icon:hover {
      color: var(--text);
    }

    .bdot {
      position: absolute;
      top: -4px;
      right: -5px;
      background: var(--accent4);
      color: #fff;
      font-size: 9px;
      border-radius: 999px;
      padding: 1px 4px;
      font-weight: 700;
    }

    /* ── CONTENT ── */
    #content {
      flex: 1;
      padding: 24px;
      overflow-y: auto;
    }

    /* ── CARDS ── */
    .stat-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 16px;
      margin-bottom: 22px;
    }

    .sc {
      border-radius: 12px;
      padding: 20px;
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      overflow: hidden;
      position: relative;
    }

    .sc::after {
      content: '';
      position: absolute;
      right: -10px;
      bottom: -10px;
      width: 75px;
      height: 75px;
      border-radius: 50%;
      background: rgba(255, 255, 255, .08);
    }

    .sc-teal {
      background: linear-gradient(135deg, #0d9488, #14b8a6);
    }

    .sc-green {
      background: linear-gradient(135deg, #16a34a, #22c55e);
    }

    .sc-amber {
      background: linear-gradient(135deg, #d97706, #f59e0b);
    }

    .sc-red {
      background: linear-gradient(135deg, #dc2626, #ef4444);
    }

    .sc-val {
      font-size: 30px;
      font-weight: 800;
      line-height: 1;
    }

    .sc-lbl {
      font-size: 12px;
      margin-top: 6px;
      opacity: .9;
    }

    .sc-more {
      font-size: 11px;
      margin-top: 10px;
      opacity: .75;
      display: flex;
      align-items: center;
      gap: 4px;
    }

    .sc-icon {
      font-size: 36px;
      opacity: .22;
    }

    /* ── CHARTS ── */
    .chart-row {
      display: grid;
      grid-template-columns: 1.5fr 1fr;
      gap: 16px;
      margin-bottom: 22px;
    }

    .chart-card {
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: 12px;
      padding: 20px;
    }

    .ch-hdr {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 14px;
    }

    .ch-ttl {
      font-size: 13.5px;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .ch-btns {
      display: flex;
      gap: 6px;
    }

    .ch-btn {
      padding: 4px 11px;
      border-radius: 6px;
      font-size: 12px;
      cursor: pointer;
      border: 1px solid var(--border);
      background: transparent;
      color: var(--muted);
    }

    .ch-btn.act {
      background: var(--accent);
      color: #fff;
      border-color: var(--accent);
    }

    /* ── TABLE ── */
    .tcard {
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: 12px;
      overflow: hidden;
      margin-bottom: 20px;
    }

    .tcard-hdr {
      padding: 14px 18px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-bottom: 1px solid var(--border);
    }

    .tcard-ttl {
      font-size: 13.5px;
      font-weight: 600;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    th {
      padding: 11px 15px;
      text-align: left;
      font-size: 11px;
      font-weight: 600;
      color: var(--muted);
      background: #11141e;
      text-transform: uppercase;
      letter-spacing: .05em;
    }

    td {
      padding: 12px 15px;
      font-size: 13px;
      border-bottom: 1px solid var(--border);
    }

    tr:last-child td {
      border-bottom: none;
    }

    tr:hover td {
      background: rgba(79, 142, 247, .04);
    }

    /* ── BADGES ── */
    .badge {
      padding: 3px 9px;
      border-radius: 999px;
      font-size: 11px;
      font-weight: 600;
    }

    .bg-green {
      background: rgba(34, 197, 94, .15);
      color: #22c55e;
    }

    .bg-orange {
      background: rgba(249, 115, 22, .15);
      color: #f97316;
    }

    .bg-blue {
      background: rgba(79, 142, 247, .15);
      color: #4f8ef7;
    }

    .bg-red {
      background: rgba(239, 68, 68, .15);
      color: #ef4444;
    }

    .bg-zip {
      background: #2a2f45;
      color: #a0aec0;
      border: 1px solid #3a4060;
      font-size: 10px;
      padding: 2px 6px;
      border-radius: 4px;
      font-family: monospace;
    }

    /* ── BUTTONS ── */
    .btn {
      padding: 8px 16px;
      border-radius: 8px;
      font-size: 13px;
      font-weight: 600;
      cursor: pointer;
      border: none;
      transition: all .15s;
      display: inline-flex;
      align-items: center;
      gap: 7px;
      text-decoration: none;
    }

    .btn-primary {
      background: var(--accent);
      color: #fff;
    }

    .btn-primary:hover {
      background: #3b7de8;
    }

    .btn-success {
      background: var(--green);
      color: #fff;
    }

    .btn-success:hover {
      background: #16a34a;
    }

    .btn-orange {
      background: var(--orange);
      color: #fff;
    }

    .btn-orange:hover {
      background: #ea6c0a;
    }

    .btn-teal {
      background: #0d9488;
      color: #fff;
    }

    .btn-teal:hover {
      background: #0f766e;
    }

    .btn-purple {
      background: var(--purple);
      color: #fff;
    }

    .btn-purple:hover {
      background: #9333ea;
    }

    .btn-danger {
      background: var(--red);
      color: #fff;
    }

    .btn-danger:hover {
      background: #dc2626;
    }

    .btn-outline {
      background: transparent;
      border: 1px solid var(--border);
      color: var(--text);
    }

    .btn-outline:hover {
      border-color: var(--accent);
      color: var(--accent);
    }

    .btn-sm {
      padding: 5px 11px;
      font-size: 12px;
    }

    /* ── FORM ── */
    .fcard {
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: 12px;
      padding: 22px;
      margin-bottom: 18px;
    }

    .fg {
      display: flex;
      flex-direction: column;
      gap: 5px;
    }

    .fg label {
      font-size: 12px;
      color: var(--muted);
      font-weight: 500;
    }

    .fc {
      background: #11141e;
      border: 1px solid var(--border);
      color: var(--text);
      border-radius: 8px;
      padding: 9px 12px;
      font-size: 13px;
      outline: none;
      width: 100%;
      transition: border-color .15s;
    }

    .fc:focus {
      border-color: var(--accent);
    }

    .fc::placeholder {
      color: #444d6b;
    }

    .fgrid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 13px;
      margin-bottom: 13px;
    }

    .fgrid-4 {
      grid-template-columns: repeat(4, 1fr);
    }

    /* ── ACTION BAR ── */
    .abar {
      display: flex;
      gap: 10px;
      margin-bottom: 18px;
      flex-wrap: wrap;
      align-items: center;
    }

    /* ── ICON BTNS ── */
    .ib {
      background: none;
      border: none;
      cursor: pointer;
      font-size: 15px;
      padding: 4px 6px;
      border-radius: 5px;
      transition: background .15s;
      text-decoration: none;
    }

    .ib:hover {
      background: rgba(255, 255, 255, .08);
    }

    .ib-del {
      color: var(--red);
    }

    .ib-edit {
      color: var(--accent3);
    }

    /* ── PAGE TITLE ── */
    .ptitle {
      font-size: 21px;
      font-weight: 700;
      margin-bottom: 4px;
    }

    .psub {
      font-size: 12.5px;
      color: var(--muted);
      margin-bottom: 20px;
    }

    /* ── ALERT ── */
    .alert {
      padding: 12px 16px;
      border-radius: 8px;
      font-size: 13px;
      margin-bottom: 16px;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .alert-ok {
      background: rgba(34, 197, 94, .12);
      border: 1px solid rgba(34, 197, 94, .3);
      color: #4ade80;
    }

    .alert-err {
      background: rgba(239, 68, 68, .12);
      border: 1px solid rgba(239, 68, 68, .3);
      color: #fca5a5;
    }

    .alert-warn {
      background: rgba(249, 115, 22, .12);
      border: 1px solid rgba(249, 115, 22, .3);
      color: #fdba74;
    }

    /* ── SCROLLBAR ── */
    ::-webkit-scrollbar {
      width: 5px;
    }

    ::-webkit-scrollbar-track {
      background: transparent;
    }

    ::-webkit-scrollbar-thumb {
      background: #2a2f45;
      border-radius: 10px;
    }

    @media(max-width:900px) {
      .stat-grid {
        grid-template-columns: repeat(2, 1fr);
      }

      .chart-row {
        grid-template-columns: 1fr;
      }

      .fgrid {
        grid-template-columns: 1fr 1fr;
      }
    }
  </style>
</head>

<body>

  <!-- SIDEBAR -->
  <nav id="sb">
    <div class="sb-head">
      <div class="logo-c"><img src="logo.jpeg" style="width:34px;height:34px;border-radius:50%;object-fit:cover;" onerror="this.style.display='none';this.parentElement.textContent='⚙'"></div>
      <div class="logo-t">SistemaCRUD</div>
    </div>
    <div class="sb-user">
      <div class="av">CM</div>
      <div>
        <div class="un">Carlos Morales</div>
        <div class="ur">Administrador</div>
      </div>
    </div>

    <div class="sb-lbl">Principal</div>
    <a href="Cliente/dashboard.php" class="ni <?php echo (isset($page_id) && $page_id == 'dashboard') ? 'act' : ''; ?>">
      <i class="fas fa-tachometer-alt"></i><span>Dashboard</span>
    </a>

    <div class="sb-lbl">Operatividad</div>

    <a href="Venta/index.php" class="ni <?php echo (isset($page_id) && $page_id == 'ventas') ? 'act' : ''; ?>">
      <i class="fas fa-dollar-sign"></i><span>Ventas</span>
    </a>

    <a href="pedidosclientes/index.php" class="ni <?php echo (isset($page_id) && $page_id == 'pedidos_clientes') ? 'act' : ''; ?>">
      <i class="fas fa-box"></i><span>Pedidos de clientes</span>
    </a>

    <a href="pedidosproveedores/index.php" class="ni <?php echo (isset($page_id) && $page_id == 'ped_proveedores') ? 'act' : ''; ?>">
      <i class="fas fa-truck-loading"></i><span>Pedidos a proveedores</span>
    </a>

    <a href="registroproveedores/index.php" class="ni <?php echo (isset($page_id) && $page_id == 'reg_proveedores') ? 'act' : ''; ?>">
      <i class="fas fa-clipboard-list"></i><span>Registro proveedores</span>
    </a>

    <a href="validacionpago/index.php" class="ni <?php echo (isset($page_id) && $page_id == 'val_pago') ? 'act' : ''; ?>">
      <i class="fas fa-money-check-dollar"></i><span>Validación de pago</span>
    </a>

    <div class="sb-lbl">Catálogos</div>

    <a href="Cliente/index.php" class="ni <?php echo (isset($page_id) && $page_id == 'clientes') ? 'act' : ''; ?>">
      <i class="fas fa-users"></i><span>Clientes</span>
    </a>

    <a href="prenda/index.php" class="ni <?php echo (isset($page_id) && $page_id == 'prendas') ? 'act' : ''; ?>">
      <i class="fas fa-shirt"></i><span>Prendas</span>
    </a>

    <a href="productos/index.php" class="ni <?php echo (isset($page_id) && $page_id == 'productos') ? 'act' : ''; ?>">
      <i class="fas fa-box-open"></i><span>Productos</span>
    </a>

    <a href="usuarios/index.php" class="ni <?php echo (isset($page_id) && $page_id == 'usuarios') ? 'act' : ''; ?>">
      <i class="fas fa-user-gear"></i><span>Usuarios</span>
    </a>

    <a href="inventario/index.php" class="ni <?php echo (isset($page_id) && $page_id == 'inventario') ? 'act' : ''; ?>">
      <i class="fas fa-warehouse"></i><span>Inventario</span>
    </a>

    <a href="#" class="ni">
      <i class="fas fa-truck"></i><span>Proveedores</span>
    </a>

    <div class="sb-lbl">Sistema</div>
    <a href="Cliente/respaldo.php" class="ni <?php echo (isset($page_id) && $page_id == 'respaldo') ? 'act' : ''; ?>">
      <i class="fas fa-download"></i><span>Respaldar BD</span>
    </a>
    <a href="Cliente/restaurar.php" class="ni <?php echo (isset($page_id) && $page_id == 'restaurar') ? 'act' : ''; ?>">
      <i class="fas fa-upload"></i><span>Restaurar BD</span>
    </a>
    <a href="Cliente/reporte.php" class="ni <?php echo (isset($page_id) && $page_id == 'reporte') ? 'act' : ''; ?>">
      <i class="fas fa-file-pdf"></i><span>Reporte PDF</span>
    </a>
  </nav>

  <!-- MAIN -->
  <div id="main">
    <!-- TOPBAR -->
    <div class="topbar">
      <button class="tb-toggle" onclick="toggleSB()"><i class="fas fa-bars"></i></button>
      <div class="tb-title"><?php echo isset($page_title) ? $page_title : 'Sistema'; ?></div>
      <div style="font-size:11.5px;color:var(--muted)">
        Home / <span style="color:var(--accent)"><?php echo isset($page_title) ? $page_title : ''; ?></span>
      </div>
      <div class="tb-right">
        <div class="tb-icon"><i class="fas fa-bell"></i><span class="bdot">3</span></div>
        <div class="av" style="cursor:pointer">AP</div>
      </div>
    </div>
    <!-- CONTENT START -->
    <div id="content">
      <?php
      // layout_header.php end — content goes after this
      ?>