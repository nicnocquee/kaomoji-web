<?php
/** 
  * AquaticPrime PHP Config
  * Configuration for web server license generation
  * @author Lucas Newman, Aquatic
  * @copyright Copyright &copy; 2005 Lucas Newman
  * @license http://www.opensource.org/licenses/bsd-license.php BSD License
  */

// ----CONFIG----

// When pasting keys here, don't include the leading "0x" that AquaticPrime Developer adds.
$key = "C5D2F90A0B38467CD6E604ECAA750576C049D91C4BB69FC8BE2CE6B699C2D42FF9D80B697478499C48C5F452A359C50E19EFCF49A0A86FB02F72CC234C5B555A8F249A5D9F9F7081F0A071D41BA53F5EFAA666B74A45785758561C783BB064D35C2C895FBD56A4F511620CC8BFB361204BFCAEDD87E85D56518980505038B505";
$privateKey = "0x83E1FB5C077AD9A88F44034871A358F9D58690BD87CF15307EC899CF112C8D75513AB2464DA586683083F83717912E09669FDF866B1AF5201FA1DD6CDD9238E5DE0002B3AEBE969325A61B83F7AF97C1D58B3D8C324CC046C261A153DDAA00A0C7D6A12988364065DE68B8444C98EDE0D759EEBABAD572F062C7D138B7A526EB";

$domain = "kaomojiapp.com";
$product = "Kaomoji for Mac";
$download = "http://kaomojiapp.com/download";

$folder = "30c85d3610bf68e772a2243049e25bfd";

// These fields below should be customized for your application.  You can use ##NAME## in place of the customer's name and ##EMAIL## in place of his/her email
$from = "kaomojiapp@gmail.com";
$fromName = "Nico Prananta";
$subject = "$product License For ##NAME##";
$message =
"Hello ##NAME##!  Here's your license for $product.

If you have not already downloaded $product please do so now: <$download>

Thanks,
Nico Prananta";

// It's a good idea to BCC your own email here so you can have an order history
$bcc = "nicnocquee@yahoo.com";

// This is the name of the license file that will be attached to the email
$licenseName = "##NAME##.kaomojilicense";

// ---KAGI ONLY CONFIG----

$kagiPassword = "testPassword";


// ---PAYPAL ONLY CONFIG----

// Your PDT authorization token
$auth_token = "wmgF2s2yY70FxpIfc7LmNlBUUzcPNjcLwhcWgTW-Z5CadDNbaI5pGWVvRva";
// Put in a URL here to redirect back to after the transaction
$redirect_url = "http://$domain/thanks.html";
$error_url = "http://$domain/error.html";


// ---ESELLERATE ONLY CONFIG----
// Secret text set up in your eSellerate publisher account
$order_notice_secret = "my secret esellerate string";
// List of eSellerate SKUs that should be processed by AquaticPrime.  Included because things like
// eCDs will come through as a separate SKU, but you probably don't want to run the order through
// AquaticPrime.  Anything not in this list will be ignored.
$aquaticPrimeSKUs = array(
		"SKU1234567890"	
		);
		
		
// ---MYSQL CONFIG----

// Database of registrations
$db_host        = "localhost";
$db_user        = "nico";
$db_password    = "0yasuminasa1";
$db_name        = "kaomoji";

?>
