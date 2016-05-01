<?php

require("aquatic/Config.php");
require("aquatic/AquaticPrime.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$params = $_POST;
	$dict = array('Email' => $params['email']);
	$license = licenseDataForDictionary($dict, $key, $privateKey);
	$email = $params['email'];
	$ar=explode("@",$email);
	$name = $ar[0];
	$to = $email;

	$from = str_replace(array("##NAME##", "##EMAIL##"), array($name, $email), $from);
	$subject = str_replace(array("##NAME##", "##EMAIL##"), array($name, $email), $subject);
	$message = str_replace(array("##NAME##", "##EMAIL##"), array($name, $email), $message);
	$licenseName = str_replace(array("##NAME##", "##EMAIL##"), array($name, $email), $licenseName);
	$bcc = str_replace(array("##NAME##", "##EMAIL##"), array($name, $email), $bcc);

		//sendMail($to, $from, $subject, $message, $license, $licenseName, $bcc);
		//sendMail2($license, $licenseName, $to, $from, $fromName, $from, $subject, $message);
	$fileName = $folder."/".md5($email.$name);
	file_put_contents($fileName, $license);
	echo "http://kaomojiapp.com/".$fileName;
} else {
	echo "not ok";
}

?>