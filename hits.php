<?php

$hits = file_get_contents('hits.log');

echo "<pre>";

$_hits = explode(PHP_EOL, $hits);
foreach($_hits as $hit) {
	if (strpos($hit,'10.10.10.1') == false) {
		echo $hit;
		echo "\r\n";
	}
}


#echo $hits;
