<?php

class encrypt {

    private $cc;
    private $mm;
    private $yyyy;
    private $cvv;
    private $capture_context;

    function __construct($cc, $mm, $yyyy, $cvv, $capture_context){
        $this->cc = $cc;
        $this->mm = $mm;
        $this->yyyy = $yyyy;
        $this->cvv = $cvv;
        $capture_context->cvv = $capture_context;
    }

    function isType($cc) : String
    {
        if (substr($cc, 0, 1) == '5') {
            return "002";
          }
          else if (substr($cc, 0, 1) == '4') {
            return "001";
          }
          else if (substr($cc, 0, 1) == '3') {
            return"003";
          }
          else {
           return "004";
          }
    }

    function gen_str($length) {
      return substr(str_shuffle('abcdefghijklmnopqrstuvwzyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'), 0, $length);
  }

    function getToken(){
       // // ////////////////////////////////////////////////////////////////////////
        $joanna = 'joanna'. $this->gen_str(20). 'txt';
        
        $arguments = json_encode([
        'capture_context' => $this->capture_context,
        'cc' => $this->cc,
        'mm' => $this->mm,
        'yyyy' => $this->$yyyy,
        'type' => $this->isType($this->cc),
        'joanna' => $this->$joanna,
        ]);

        file_put_contents('args.json', $arguments);
        $command = 'node encrypt.js args.json';
        shell_exec($command);
        $encrypted = file_get_contents($joanna);
        $encrypted = json_decode($encrypted, true); // convert JSON string to PHP array
        unlink('args.json');
        unlink($joanna);
        $ch = curl_init("https://flex.cybersource.com/flex/v2/tokens"); 
        $headers = array(); 
        $headers[] = 'Host: flex.cybersource.com';
        $headers[] = 'Accept: */*';
        $headers[] = 'Content-Type: application/jwt; charset=utf-8';
        $headers[] = 'Origin: https://flex.cybersource.com';
        $headers[] = 'Connection: keep-alive';
        $headers[] = 'Sec-Fetch-Dest: empty';
        $headers[] = 'Referer: https://flex.cybersource.com/cybersource/assets/microform/0.11.6/iframe.html';
        $headers[] = 'Sec-Fetch-Mode: cors';
        $headers[] = 'Sec-Fetch-Site: same-origin';
        $options = array(
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => 1, 
            CURLOPT_FOLLOWLOCATION => 1, 
            CURLOPT_SSL_VERIFYPEER => 0, 
            CURLOPT_SSL_VERIFYHOST => 0, 
            CURLOPT_POSTFIELDS => $encrypted);
        curl_setopt_array($ch, $options);
        $flextoken = curl_exec($ch);
        echo $flextoken = trim($flextoken);
        #echo "<hr>";
        $cap = explode('.', $flextoken);

        ##echo "<hr>";
        $dcode = base64_decode($cap[1]);
        #echo "<hr>";
        $token = GetStr2($dcode, '"jti":"','"'); 
        #echo "<hr>";
        $bin = GetStr2($dcode, '"bin":"','"');
        curl_close($ch);
    }
}

$card = extract($_GET);
$cc = explode("|", $card)[0];
$month = explode("|", $card)[1];
$year = explode("|", $card)[2];
$cvv = explode("|", $card)[3];
$capture_context = explode("|", $card)[4];

$encrypt = new encrypt($cc, $month, $year, $cvv, $capture_context);
echo $token = $encrypt->getToken();