<?php

header("Cache-Control: no-store, no-cache, max-age=0, must-revalidate"); // HTTP 1.1
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // HTTP 1.1
header("Cache-Control: post-check=0, pre-check=0", FALSE); // HTTP 1.0
header("Pragma: no-cache"); // HTTP 1.0
$this->response->type('json');
if (!empty($callback)) {
	echo $callback . "(" . json_encode($ApiOutput) . ");";
} else {
	if (isset($ApiOutput)) {
		echo json_encode($ApiOutput);
	}
}
?>