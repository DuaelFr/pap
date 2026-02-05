<?php

/**
 * Verify PayPal Immediate Payment Notifications (IPN)
 *
 * Example:
 *   $ipn = new PayPalIPN(file_get_contents('php://input'));
 *   if ($ipn->verify()) {
 *     # process the payload
 *   } else {
 *     # log and/or ignore the payload
 *   }
 *
 * Sandbox Example:
 *   $ipn = new PayPalIPN(file_get_contents('php://input'), true);
 *   ...
 */
class PayPalIPN {
  const CACERT_PEM_FILE = "";

  public $raw, $post;

  public function __construct($raw, $sandbox=false) {
    $this->raw = $raw;
    $this->sandbox = $sandbox ? 'sandbox.' : '';
    $this->post = array();   # we can't use $_POST as it's causing serialization issues
    foreach (explode('&', $raw) as $item) {
      $item = explode('=', $item);
      if (count($item) == 2) {
        $this->post[$item[0]] = urldecode($item[1]);
      }
    }
  }

  public function verify() {
    $request = 'cmd=_notify-validate';
    if (function_exists('get_magic_quotes_gpc')) {
      $get_magic_quotes_exists = true;
    }
    foreach ($this->post as $key => $value) {
      if ($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
        $value = urlencode(stripslashes($value));
      } else {
        $value = urlencode($value);
      }
      $request .= "&$key=$value";
    }
    $curl = curl_init("https://www.{$this->sandbox}paypal.com/cgi-bin/webscr");
    curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Connection: Close'));
    if (self::CACERT_PEM_FILE != "") {
      curl_setopt($curl, CURLOPT_CAINFO, self::CACERT_PEM_FILE);
    }
    $result = curl_exec($curl);
    curl_close($curl);
    return $result == "VERIFIED";
  }
}

?>
