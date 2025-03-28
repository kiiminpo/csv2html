<?php

function readCSV($callback = null) {
	global $dbId, $dbfile, $dbbase, $relationdb, $relationid, $rdbIx, $csv, $csvIx;
	global $datename, $weekdayname, $wdays, $total, $j_total, $expired;
	global $sort, $sortname, $sort_r, $sortbynumber, $charset, $dbname;

	if (preg_match("/^(\-?)(.*)$/", $sort, $ar)) {
		$sort_r = $ar[1] ? -1 : 1;
		$sortname = $ar[2];
	}
	$sortbynumber = "/^" . str_replace(";", "$|^", preg_quote($sortbynumber)) . "$/";
	mt_srand(floor(time() / 86400));
	$total = 0;
	$j_total = 0;
	//連携するDBのデータを解析
	$rdbIx = array();
	$rdbNo = array();

	if (!empty($relationdb)) {
		if ($fh = @fopen($dbbase . $relationdb . ".csv", "r")) {
			$data = fgetcsv($fh);
			foreach ($data as $value) {
				$value = preg_replace('/^\xef\xbb\xbf|"/', '', $value);
				if (!empty($charset) && !preg_match("/utf\-?8/i", $charset)) {
					$value = mb_convert_encoding($value, "utf-8", $charset);
				}
				$rdbNo[] = $value;
			}
			while (($data = fgetcsv($fh))) {
				$ix = array();
				$col = 0;
				foreach ($data as $value) {
					$ix[$rdbNo[$col++]] = $value;
					if (!empty($charset) && !preg_match("/utf\-?8/i", $charset)) {
						$value = mb_convert_encoding($value, "utf-8", $charset);
					}
				}
				$rdbIx[$ix[$relationid]] = $ix;
			}
			fclose($fh);
		}
	}
	//データベースの読み込み
	$dbname = $_GET['db'] ?? "";
	if ($dbname == '(nm)') {
		$dbfile = date('Ym', strtotime('first day of next month'));
	} else {
		$dbname = preg_replace("/\W/", "", $dbname);
		if (strcmp($dbname, '')) {
			$dbfile = $dbname;
		}
	}

	$csvNo = array();
	$csv = array();
	$csvIx = array();
	if ($fh = @fopen($dbbase . $dbfile . ".csv", "r")) {
		//見出しの読み込み
		$data = fgetcsv($fh);
		foreach ((array)$data as $value) {
			$value = preg_replace('/^\xef\xbb\xbf|"/', '', $value);
			if (!empty($charset) && !preg_match("/utf\-?8/i", $charset)) {
				$value = mb_convert_encoding($value, "utf-8", $charset);
			}
			if (empty($dbId)) {
				$dbId = $value;
			}
			$csvNo[] = $value;
		}
		//絞り込み
		$qar = explode(",", $_GET["q"] ?? "");
		if (count($qar) != 2 || (!in_array($qar[0], $rdbNo) && !in_array($qar[0], $csvNo))) {
			$qar[0] = false;
		}
		//検索
		if (!empty($_GET["s"])) {
			$sar = preg_split("/\s+/", mb_convert_kana($_GET["s"], "KVacs", "utf-8"));
		}
		while (($data = fgetcsv($fh))) {
			$ix = array();
			foreach ($csvNo as $value) {
				$ix[$value] = '';
			}
			$ix["rnd"] = sprintf("%12x", mt_rand());
			$col = 0;
			foreach ($data as $value) {
				if (!empty($charset) && !preg_match("/utf\-?8/i", $charset)) {
					$value = mb_convert_encoding($value, "utf-8", $charset);
				}
				//preg_replace("/㈱|㈲/", "", 
				$ix[$csvNo[$col++]] = $value;
			}
			if (!empty($datename)) {
				$ix[$weekdayname] = $wdays[date('w', strtotime($ix[$datename]))];
			}
			if (!empty($relationdb)) {
				$ix[$relationdb] = $rdbIx[$ix[$relationid] ?? ''] ?? '';
			}
			++$total;
			if ($qar[0]) { //絞り込み(q)
				$ar = preg_split("/[\/,]/", $ix[$qar[0]] ?? "");
				if (!in_array($qar[1], $ar)) {
					if (empty($relationdb)) {
						continue;
					}
					if (($ix[$relationdb][$qar[0]] ?? "") != $qar[1]) {
						continue;
					}
				}
			}
			if (!empty($expired)) {
				$exp1 = 0;
				$ar = explode(",", $expired);
				for ($i = 0; $i < count($ar); $i++) {
					if (!empty($ix[$ar[$i]])) {
						$exp1 = max($exp1, strtotime($ix[$ar[$i]]));
					}
				}
				if ($exp1 && strtotime("today") > $exp1) {
					continue;
				}
			}
			++$j_total;
			if (!empty($callback) && !$callback($ix)) {//個別絞り込み
				continue;
			}
			if (empty($sar)) {
				$csv[] = $ix;
			} else {
				$found = 0;
				if (empty($relationdb)) {
					$ar = $data;
				} else {
					$ar = array_merge($data, $ix[$relationdb]);
				}
				$str = mb_convert_kana(join("\t", $ar), "KVacs", "utf-8");
				foreach ($sar as $search) {
					if (stripos($str, $search) !== false) {
						$found++;
					}
				}
				if ($found >= count($sar)) {
					$csv[] = $ix;
				}
			}
		}
		fclose($fh);
		//ソート
		if (!empty($sortname) && isset($ix[$sortname])) {
			usort($csv, "cmp");
		}
		$i = 0;
		foreach ($csv as $ix) {
			$csvIx[$ix[$dbId]] = $i++;
		}
	}
}

function cmp ($a, $b) {
	global $sortname, $sort_r, $sortbynumber;
	if (preg_match($sortbynumber, $sortname)) {
		return (get_number($a[$sortname]) - get_number($b[$sortname])) * $sort_r;
	}
	return strcasecmp($a[$sortname], $b[$sortname]) * $sort_r;
}

function changeBody($fh_change) {
	if (!empty($fh_change)) {
		$body = ob_get_clean();
		while ($data = fgets($fh_change)) {
			$ar = explode("\t", preg_replace('/^\xef\xbb\xbf|[\r\n]+$/', '', $data));
			if (count($ar) == 2) {
				$body = str_ireplace($ar[0], $ar[1], $body);
			}
		}
		fclose($fh_change);
		echo $body;
	}
}

function getQuery($arg) {
	$db = $arg + $_GET;
	$results = array();
	foreach ($db as $name => $value) {
		if ($value !== 0 && $value !== "") {
			$results[] = urlencode($name) . "=" . urlencode($value);
		}
	}
	return count($results) ? "?" . join("&", $results) : "";
}

function getFormData($arg) {
	$db = $arg + $_GET;
	$results = array();
	foreach ($db as $name => $value) {
		if ($value !== "") {
			$results[] = '<input type="hidden" name="' . htmlspecialchars($name, ENT_QUOTES, "utf-8") . '" value="' . htmlspecialchars($value, ENT_QUOTES, "utf-8") . '">';
		}
	}
	return join("\n", $results);
}

function number_unit($int) {
 	$unit = array('万', '億', '兆' ,'京');
	krsort($unit);

	$int = get_number($int);
	$tmp = '';
	$count = strlen($int);
	foreach ($unit as $k => $v) {
		if ($count > (4 * ($k + 1))) {
			if ($int != 0) {
				$tmp .= number_format(floor( $int / pow(10000, $k + 1))) . $v;
			}
			$int = $int % pow(10000, $k+1);
		}
	}
	if ($int != 0) {
		$tmp .= number_format($int % pow(10000, $k + 1));
	}
	return $tmp;
}

function number_man($int) {
	$int = get_number($int);
	$tmp = ($int / 10000) . '万';
	if (preg_match("/^(\d+)(\.\d+万)$/", $tmp, $res)) {
		$tmp = number_format($res[1]) . $res[2];
	}
	return $tmp;
}

function curl_get_contents($url, $timeout = 30){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
	$result = curl_exec($ch);
	curl_close($ch );
	return $result;
}

function get_number($str){
	$numberlist = array(
		'〇' => '0',    '零' => '0',
		'一' => '1',    '壱' => '1',
		'二' => '2',    '弐' => '2',
		'三' => '3',    '参' => '3',
		'四' => '4',    '肆' => '4',
		'五' => '5',    '伍' => '5',
		'六' => '6',    '陸' => '6',
		'七' => '7',    '漆' => '7',
		'八' => '8',    '捌' => '8',
		'九' => '9',    '玖' => '9'
	);
	$prefix_a = array(
		'十' => '1',    '拾' => '1',
		'百' => '2',    '陌' => '2',    '佰' => '2',
		'千' => '3',    '阡' => '3',    '仟' => '3'
	);
	$prefix_b = array(
		'万' => '4',    '萬' => '4',
		'億' => '8',
		'兆' => '12',
		'京' => '16'
	);

	$numstr = preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY);

	$total = 0;
	$n = 0;
	$s = '';
	foreach ($numstr as $val){
		$val = $numberlist[$val] ?? mb_convert_kana($val, 'KVa');
		if (is_numeric($val) || $val == '.') {
			$s .= $val;
			continue;
		}
		if (array_key_exists($val, $prefix_a)) {
			$n += floatval($s ? $s : 1) * pow(10, $prefix_a[$val]);
			$s = '';
			continue;
		}

		if (array_key_exists($val, $prefix_b)){
			$total += ($n + floatval($s ? $s : 0)) * pow(10, $prefix_b[$val]);
			$n = 0;
			$s = '';
			continue;
		}
	}
	return $total + $n + floatval($s ? $s : 0);
}
