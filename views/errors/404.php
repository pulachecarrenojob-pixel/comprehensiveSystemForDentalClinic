<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>404 — Page Not Found</title>
  <style>
    body { font-family: system-ui, sans-serif; display:flex; align-items:center; justify-content:center; min-height:100vh; background:#f4f6f8; margin:0; }
    .box { text-align:center; padding:40px; }
    h1 { font-size:4rem; font-weight:700; color:#1D9E75; margin:0; }
    h2 { font-size:1.2rem; color:#374151; margin:8px 0; }
    p  { color:#6b7280; margin-bottom:24px; }
    a  { background:#1D9E75; color:#fff; padding:10px 20px; border-radius:8px; text-decoration:none; font-weight:500; }
  </style>
</head>
<body>
  <div class="box">
    <h1>404</h1>
    <h2>Page not found</h2>
    <p>The page you are looking for does not exist.</p>
    <a href="<?= defined('BASE_URL') ? BASE_URL : '/' ?>">Go to Dashboard</a>
  </div>
</body>
</html>
