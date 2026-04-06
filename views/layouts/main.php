<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= ($title ?? 'Dashboard') . ' — ' . APP_NAME ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
  <?php if(isset($extraCss)): foreach($extraCss as $css): ?>
  <link rel="stylesheet" href="<?= asset('css/'.$css) ?>">
  <?php endforeach; endif; ?>
  <link rel="stylesheet" href="<?=asset('css/patients.css')?>">
</head>
<body class="app-body">

  <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

  <?php require BASE_PATH . '/views/partials/sidebar.php'; ?>

  <div class="main-wrapper" id="mainWrapper">
    <?php require BASE_PATH . '/views/partials/topbar.php'; ?>
    <main class="main-content">
      <?php require BASE_PATH . '/views/partials/alerts.php'; ?>
      <?= $content ?>
    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
  <script src="<?= asset('js/app.js') ?>"></script>
  <?php if(isset($extraJs)): foreach($extraJs as $js): ?>
  <script src="<?= asset('js/'.$js) ?>"></script>
  <?php endforeach; endif; ?>
</body>
</html>
