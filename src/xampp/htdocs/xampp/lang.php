<?php
	$fp = fopen("lang.tmp", "w");
	fwrite($fp, basename($_SERVER['QUERY_STRING']));
	fclose($fp);
	if (!empty($_SERVER['HTTPS']) && ('on' == $_SERVER['HTTPS'])) {
		$uri = 'https://';
	} else {
		$uri = 'http://';
	}
	$uri .= $_SERVER['HTTP_HOST'];
	header('Location: '.$uri.'/xampp/index.php');
	exit;
?>
