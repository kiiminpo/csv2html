<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title></title>
</head>

<body>
<ul>
  <?php for ($i = $pos; $i < $pos + $items && $i < count($csv); ++$i): $data = $csv[$i]; if ($data): ?>
  <li>
    <a href="./?c=<?= $data[$dbId] ?>"><?= $data["タイトル"]; ?></a>
  </li>
  <?php endif;endfor; ?>
</ul>
</body>
</html>
