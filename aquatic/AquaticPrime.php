<?php
/** 
  * AquaticPrime PHP implementation
  * Creates license file from associative arrays and a public-private keypair
  * This implementation requires bcmath, which is included with PHP 4.0.4+
  * @author Lucas Newman, Aquatic, and other contributors
  * @copyright Copyright &copy; 2005-2010 Lucas Newman and other contributors
  * @license http://www.opensource.org/licenses/bsd-license.php BSD License
  */
  
/**
  * hex2bin
  * Converts a hexadecimal string to binary
  * @param string Hex string
  * @return string Binary string
  */
function hex2bin($hex) {
    if (strlen($hex) % 2)
        $hex = "0".$hex;
    
    for ($i = 0; $i < strlen($hex); $i += 2) { 
        $bin .= chr(hexdec(substr($hex, $i, 2))); 
    }
   
   return $bin; 
} 

/**
  * dec2hex
  * Converts a decimal string to a hexadecimal string
  * @param string Decimal string
  * @return string Hex string
  */
function dec2hex($number)
{
    $hexvalues = array('0','1','2','3','4','5','6','7',
                       '8','9','A','B','C','D','E','F');
    $hexval = '';
    while($number != '0')
    {
        $hexval = $hexvalues[bcmod($number,'16')].$hexval;
        $number = bcdiv($number,'16',0);
    }
    return $hexval;
}

/**
  * hex2dec
  * Converts a hexadecimal string to decimal string
  * @param string Hex string
  * @return string Decimal string
  */
function hex2dec($number)
{
    $decvalues = array('0' =>  '0', '1' =>  '1', '2' => '2',
                       '3' =>  '3', '4' =>  '4', '5' => '5',
                       '6' =>  '6', '7' =>  '7', '8' => '8',
                       '9' =>  '9', 'A' => '10', 'B' => '11',
                       'C' => '12', 'D' => '13', 'E' => '14',
                       'F' => '15', 'a' => '10', 'b' => '11',
                       'c' => '12', 'd' => '13', 'e' => '14',
                       'f' => '15');
    $decval = '0';
    
    $number = array_pop(explode("0x", $number, 2));
    
    $number = strrev($number);
    for($i = 0; $i < strlen($number); $i++)
    {
            $decval = bcadd(bcmul(bcpow('16',$i,0),$decvalues[$number{$i}]), $decval);
    }
    return $decval;
}

/**
  * powmod
  * Raise a number to a power mod n
  * This could probably be made faster with some Montgomery trickery, but it's just fallback for now
  * @param string Decimal string to be raised
  * @param string Decimal string of the power to raise to
  * @param string Decimal string the modulus
  * @return string Decimal string
  */
function powmod($num, $pow, $mod)
{
    if (function_exists('bcpowmod')) {
        // bcpowmod is only available under PHP5
        return bcpowmod($num, $pow, $mod);
    }

    // emulate bcpowmod
    $result = '1';
    do {
        if (!bccomp(bcmod($pow, '2'), '1')) {
            $result = bcmod(bcmul($result, $num), $mod);
        }
        $num = bcmod(bcpow($num, '2'), $mod);
        $pow = bcdiv($pow, '2');
    } while (bccomp($pow, '0'));
    return $result;
}

/**
  * getSignature
  * Get the base64 signature of a dictionary
  * @param array Associative array (i.e. dictionary) of key-value pairs
  * @param string Hexadecimal string of public key
  * @param string Hexadecimal string the private key
  * @return string Base64 encoded signature
  */
function getSignature($dict, $key, $privKey)
{
    // Sort keys alphabetically
    uksort($dict, "strcasecmp");

    // Concatenate all values
    $total = '';
    foreach ($dict as $value)
        $total .= $value;
    
    // Escape apostrophes by un-quoting, adding apos, then re-quoting
    // so this turns ' into '\'' ... we have to double-slash for this php.
    $fixedApostrophes = escapeshellarg($total);

    // This part is the most expensive below
    // We try to do it with native code first
    $aquatic_root = preg_replace('!((/[A-Za-z._-]+)+)/AquaticPrime\.php!', '$1', __FILE__);
    ob_start();
    $passthruString = $aquatic_root."/aquaticprime $key $privKey $fixedApostrophes";

    passthru($passthruString, $err);
    $sig = ob_get_contents();
    ob_end_clean();
    if ($err)
    {
        error_log("passthrough yielded $err: $passthruString");
    }

    // If that fails, do it in php
    if ($sig != "")
    {
        $sig = base64_encode($sig);
    }
    else
    {
        // Get the hash
        $hash = sha1(utf8_encode($total));

        // OpenSSL-compatible PKCS1 Padding
        // 128 bytes - 20 bytes hash - 3 bytes extra padding = 105 bytes '0xff'
        $paddedHash = '0001';
        for ($i = 0; $i < 105; $i++)
        {
            $paddedHash .= 'ff';
        }
        $paddedHash .= '00'.$hash;

        $decryptedSig = hex2dec($paddedHash);

        // Encrypt into a signature
        $sig = powmod($decryptedSig, hex2dec($privKey), hex2dec($key));
        $sig = base64_encode(hex2bin(dec2hex($sig)));
    }
    return $sig;
}

/**
  * licenseDataForDictionary
  * Get the signed plist for a dictionary
  * @param array Associative array (i.e. dictionary) of key-value pairs
  * @param string Hexadecimal string of public key
  * @param string Hexadecimal string the private key
  * @return string License file as plist
  */
function licenseDataForDictionary($dict, $pubKey, $privKey)
{
    $sig = chunk_split(getSignature($dict, $pubKey, $privKey));
    
    //$plist = "<?xml version=\"1.0\" encoding=\"UTF-8\"?".">\n";
    //$plist .= "<!DOCTYPE plist PUBLIC \"-//Apple Computer//DTD PLIST 1.0//EN\" \"http://www.apple.com/DTDs/PropertyList-1.0.dtd\">\n";
    //$plist .= "<plist version=\"1.0\">\n<dict>\n";
    $da = "";
    //foreach ($dict as $key => $value) {
        //$da .= htmlspecialchars($key, ENT_NOQUOTES).": ".htmlspecialchars($value, ENT_NOQUOTES)."\n";
        //$plist .= "\t<key>".htmlspecialchars($key, ENT_NOQUOTES)."</key>\n";
        //$plist .= "\t<string>".htmlspecialchars($value, ENT_NOQUOTES)."</string>\n";
    //}
    $da = "Email: The email address where you receive the link to this license.\n";
    /*$plist .= "\t<key>Signature</key>\n";
    $plist .= "\t<data>$sig</data>\n";
    $plist .= "</dict>\n";
    $plist .= "</plist>\n";
    */
    $da .= "License Key: ".$sig;
    return $da;
    //return $plist;

}

function sendMail2($license, $name, $mailto, $from_mail, $from_name, $replyto, $subject, $message) {
  $content = chunk_split(base64_encode($license));
  $uid = md5(uniqid(time()));
  $name = basename($file);
  $header = "From: ".$from_name." <".$from_mail.">\r\n";
    if ($bcc != "")
    $headers .= "Bcc: $bcc\r\n";
  $header .= "Reply-To: ".$replyto."\r\n";
  $header .= "MIME-Version: 1.0\r\n";
  $header .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n\r\n";
  $msg = "This is a multi-part message in MIME format.\r\n";
  $msg .= "–".$uid."\r\n";
  $msg .= "Content-type:text/plain; charset=iso-8859-1\r\n";
  $msg .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
  $msg .= $message."\r\n\r\n";
  $msg .= "–".$uid."\r\n";
  $msg .= "Content-Type: application/octet-stream; name=\"".$filename."\"\r\n";
  $msg .= "Content-Transfer-Encoding: base64\r\n";
  $msg .= "Content-Disposition: attachment; filename=\"".$filename."\"\r\n\r\n";
  $msg .= $content."\r\n\r\n";
  $msg .= "–".$uid."–";
  $mail_message = "";
  if (mail($mailto, $subject, $msg, $header))
  {
    $mail_message = "Mail Success";
//echo $mail_message;
  }
  else {
    $mail_message = "Mail Failure";
//echo $mail_message;
  }
}

function sendMail($to, $from, $subject, $message, $license, $name, $bcc='')
{
	// Create a random boundary
	$boundary = base64_encode(MD5((string)rand()));
	
	$headers  = "From: $from\n";
	if ($bcc != "")
		$headers .= "Bcc: $bcc\n";
	$headers .= "X-Mailer: PHP/".phpversion()."\n";
	$headers .= "MIME-Version: 1.0\n";
	$headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\n";
	$headers .= "Content-Transfer-Encoding: 8bit\n\n";
	$headers .= "This is a MIME encoded message.\n\n";
	
	$headers .= "--$boundary\n";
	
	$headers .= "Content-Type: text/plain; charset=\"utf-8\"\n";
	$headers .= "Content-Transfer-Encoding: 8bit\n\n";
	$headers .= "$message\n\n\n";
	
	$headers .= "--$boundary\n";
	
	$headers .= "Content-Type: application/octet-stream; name=\"$name\"\n";
	$headers .= "Content-Transfer-Encoding: base64\n";
	$headers .= "Content-Disposition: attachment\n\n";

    $headers .= chunk_split(base64_encode($license))."\n";
    
    $headers .= "--$boundary--";
	
	mail($to, $subject, "", utf8_encode($headers));
}

?>