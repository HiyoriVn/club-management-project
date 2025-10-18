<?php
// Nạp phần Header (bao gồm <html>, <head>, <body>, <nav>)
require_once ROOT_PATH . '/app/Views/layout/header.php';
?>

<h1><?php echo $data['title']; ?></h1>
<p><?php echo $data['description']; ?></p>
<p>Nếu bạn thấy được dòng này, có nghĩa là <strong>Router</strong> đã hoạt động chính xác!</p>
<p>Và bây giờ chúng ta đã có một <strong>Layout (Header & Footer)</strong> chung.</p>

<?php
// Nạp phần Footer (bao gồm </body>, </html>)
require_once ROOT_PATH . '/app/Views/layout/footer.php';
?>