# ■設定				：index.php  
# ■リストのテンプレート：list.php  
# ■詳細のテンプレート：details.php  
# ■その他                        ：csv.php  
  
## ▼基本の埋め込み  
`<?= $data["★項目名★"]; ?>`  
### ▽空  
`<?= $data["★項目名★"] ?? ''; ?>`  
### ▽空  
`<?= $data["★項目名★"] ?? 'ない場合の文字列'; ?>`  
### ▽空  
`<?= $data["★項目名★"] ?? $data["☆項目名☆"] ?? 'ない場合の文字列'; ?>`  
### ▽文章（改行）  
`<?= str_replace("\n", "<br>", $data["★項目名★"]); ?>`  
  
## ▼連携CSVの埋め込みは前にCSV名  
`<?= $data["★関連CSV名★"]["★項目名★"]; ?>`  
  
## ▼省略  
`<?php echo` → `<?=`  
  
## ▼詳細リンク/ID  
`<a href="./?c=<?= $data[$dbId] ?>"`  
  
## ▼リスト  
`<?php for ($i = $pos; $i < $pos + $items && $i < count($csv); ++$i): $data = $csv[$i]; if ($data): ?>`  
【内容】  
`<?php endif;endfor; ?>`  
  
## ▼?q=  
`<?php if (!empty($_GET["q"])) : echo "【", htmlentities($_GET["q"]), "】"; endif; ?>`  
`<?php if (!empty($_GET["q"])) : echo "【", htmlentities(preg_replace("/^.*,/", "", $_GET["q"])), "】"; endif; ?>`  
  
## ▼DB  
`<?= preg_replace("/\W/", "", $_GET['db'] ?? "") ?>`  
  
## ▼リンク  
`<a href="./?c=<?= $data[$dbId] ?>"><?= $data["項目名"]; ?></a>`  
## ▼絞り込み（項目名が▲）※完全一致※「/」で区切る（例：▲/# ■）  
`<a href="./?q=★項目名★,▲">▲</a>`  
  
## ▼ifである場合のみ  
`<?php if (!empty($data["★関連CSV名★"]["★項目名★"])): ?>`  
`<?php if (!empty($data["★関連CSV名★"]["★項目名A★"]) && !empty($data["★関連CSV名★"]["★項目名B★"])): ?>`  
`<?php if (!empty($data["★関連CSV名★"]["★項目名A★"]) || !empty($data["★関連CSV名★"]["★項目名B★"])): ?>`  
`<?php else : ?>`  
`<?php endif; ?>`  
  
## ▼ifでない場合のみ  
`<?php if (empty($data["★関連CSV名★"]["★項目名★"])): ?>`  
`<?php if (empty($data["★関連CSV名★"]["★項目名A★"]) && empty($data["★関連CSV名★"]["★項目名B★"])): ?>`  
`<?php if (empty($data["★関連CSV名★"]["★項目名A★"]) || empty($data["★関連CSV名★"]["★項目名B★"])): ?>`  
`<?php else : ?>`  
`<?php endif; ?>`  
  
## ▼★項目名★の値が▲なら# ■、それ以外は◆  
`<?php if ($data["★項目名★"] === "▲"): ?># ■<?php else : ?>◆<?php endif; ?>`  
`<?php if (($data["★項目名★"] ?? $data["★関連CSV名★"]["★項目名★"] ?? '') === "▲"): ?># ■<?php else : ?>◆<?php endif; ?>`  
### ▽大文字小文字区別しない場合は  
`<?php if (!strcasecmp($data["★項目名★"], "▲")): ?># ■<?php else : ?>◆<?php endif; ?>`  
`<?php if (!strcasecmp($data["★項目名★"] ?? $data["★関連CSV名★"]["★項目名★"] ?? '', "▲")): ?># ■<?php else : ?>◆<?php endif; ?>`  
  
## ▼★項目名★の値に▲が含まれた場合は# ■、それ以外は◆  
`<?php if (stripos($data["★項目名★"], "▲") !== false): ?># ■<?php else : ?>◆<?php endif; ?>`  
`<?php if (stripos($data["★項目名★"] ?? $data["★関連CSV名★"]["★項目名★"] ?? '', "▲") !== false): ?># ■<?php else : ?>◆<?php endif; ?>`  
  
## ▼★項目名★の値に▲から始まる場合は# ■、それ以外は◆  
`<?php if (stripos($data["★項目名★"], "▲") === 0): ?># ■<?php else : ?>◆<?php endif; ?>`  
`<?php if (stripos($data["★項目名★"] ?? $data["★関連CSV名★"]["★項目名★"] ?? '', "▲") === 0): ?># ■<?php else : ?>◆<?php endif; ?>`  
  
## ▼★項目名★の値に「https:」と「http:」から始まる場合は# ■、それ以外は◆  
`<?php if (preg_match("/^https?:\/\//", $data["★項目名★"])): ?># ■<?php else : ?>◆<?php endif; ?>`  
  
## ▼DB指定がある場合  
`<?php if (!empty($_GET["db"])): ?>`  
`<?php else : ?>`  
`<?php endif; ?>`  
  
## ▼DB指定がない場合  
`<?php if (empty($_GET["db"])): ?>`  
`<?php else : ?>`  
`<?php endif; ?>`  
  
## ▼日付はphpの機能で表示形式を変更可能  
`<?= date("n月j日", strtotime($data["★年月日★"])) ?>`  
## ▼曜日（要設定）  
`<?= $wdays[(new DateTime($data["★年月日★"]))->format('w')]; ?>`  
  
`<?= $data["★項目名★"], "★文字★"; ?>`  
`<?= "★文字★", $data["★項目名★"]; ?>`  
`<?= "★文字★", $data["★項目名★"], "★文字★"; ?>`  
`<?= empty($data["★項目名★"]) ? "★なしのときだけ★" : $data["★項目名★"] . "★あるときだけ★"; ?>`  
`<?= empty($data["★項目名★"]) ? "★なしのときだけ★" : "★あるときだけ★" . $data["★項目名★"]; ?>`  
`<?= empty($data["★項目名★"]) ? "★なしのときだけ★" : "★あるときだけ★" . $data["★項目名★"] . "★あるときだけ★"; ?>`  
`<?= $data["★関連CSV名★"]["★緯度★"], ",", $data["★関連CSV名★"]["★経度★"], ",", $data["★関連CSV名★"]["★ズーム★"] ?? 16; ?>`  
  
## ▼カウント  
総数：`<?= $total; ?>`  
分類総数：`<?= $j_total; ?>`  
絞り込み：`<?= count($csv); ?>`  
  
## ▼数字  
１．カンマ区切（例：39,800円）  
`<?= number_format(get_number($data["★項目名★"])), '円'; ?>`  
  
２．単位を付加（例：3万9,800円）  
`<?= number_unit($data["★項目名★"]), '円'; ?>`  
  
３．単位を万で固定（例：39.8万円）  
`<?= number_man($data["★項目名★"]), '円'; ?>`  
  
## ▼国土交通省「学校位置情報」  
緯度：`<?= $geojson[$data["学校名"]][1] ?>`  
経度：`<?= $geojson[$data["学校名"]][0] ?>`  
### ▽ある場合  
`<?php if (!empty($geojson[$data["学校名"]])): ?>`  
`<?php endif; ?>`  
### ▽ない場合  
`<?php if (empty($geojson[$data["学校名"]])): ?>`  
`<?php endif; ?>`  
