<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title></title>
</head>

<body>
<?= $data["タイトル"]; ?><br>
<?= $data["本文"]; ?><br>
住所：<?= $data["location"]["住所"]; ?><br>
緯度：<?= $data["location"]["緯度"]; ?><br>
経度：<?= $data["location"]["経度"]; ?><br>
</body>
</html>
