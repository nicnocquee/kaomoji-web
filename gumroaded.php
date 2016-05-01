<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$params = $_POST;
	if (isset($params['test'])) {
		echo "http://kaomojiapp.com/test/".$_SERVER['REMOTE_HOST']."/".$params['email'];
	} else {
		echo "http://kaomojiapp.com/;
	}
	//echo print_r($params, true);
} else {
	echo "not ok";
}

?>