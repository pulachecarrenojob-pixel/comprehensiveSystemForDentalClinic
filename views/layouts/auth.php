<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $title ?? APP_NAME ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
  <link rel="stylesheet" href="<?= asset('css/auth.css') ?>">
</head>
<body class="auth-body">
  <?= $content ?>
  <script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
