<?php
    date_default_timezone_set("Asia/kolkata");
    //Data From Webhook
    $content = file_get_contents("php://input");
    $update = json_decode($content, true);
    $chat_id = $update["message"]["chat"]["id"];
    $message = $update["message"]["text"];
    $message_id = $update["message"]["message_id"];
    $id = $update["message"]["from"]["id"];
    $username = $update["message"]["from"]["username"];
    $firstname = $update["message"]["from"]["first_name"];
    $start_msg = $_ENV['START_MSG']; 

if($message == "/start"){
    send_message($chat_id,$message_id, "***Hola $firstname \nUsa .bin xxxxxx Para Checkar Tu BIN \n$start_msg***");
}

//Bin Lookup
if(strpos($message, ".bin") === 0){
    $bin = substr($message, 5);
    $curl = curl_init();
    curl_setopt_array($curl, [
    CURLOPT_URL => "https://bin-checker.net/api/".$bin,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 40,
    CURLOPT_TIMEOUT => 10,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => [
    "accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9",
    "accept-language: es-ES,en-GB,en-US;q=0.9,en;q=0.8,hi;q=0.7",
    "sec-fetch-dest: document",
    "sec-fetch-site: same-origin",
    "user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/99.0.4844.51 Safari/537.36"],
   ]);

 $result = curl_exec($curl);
 curl_close($curl);
 $data = json_decode($result, true);
 $bank = $data['data']['bank'];
 $country = $data['data']['country'];
 $brand = $data['data']['vendor'];
 $level = $data['data']['level'];
 $type = $data['data']['type'];
 $flag = $data['data']['countryInfo']['emoji'];
 $result1 = $data['result'];

            if (empty($level)) {
            	$level = "UNAVAILABLE";
            }

            if (empty($typename)) {
            	$typename = "UNAVAILABLE";
            }
            if (empty($brand)) {
            	$brand = "UNAVAILABLE";
            }
            if (empty($bank)) {
            	$bank = "UNAVAILABLE";
            }
            if (empty($bname)) {
            	$bname = "UNAVAILABLE";
            }
            
    if ($result1 == true) {
    send_message($chat_id,$message_id,"***VALID BIN✅
┏━━━━━━━━━━━━━━━━━━
┠⌬ BIN: $bin
┠⌬ Brand: $brand
┠⌬ Level: $level
┠⌬ Bank: $bank
┠⌬ Country: $country $flag
┠⌬ Type: $type
┗━━━━━━━━━━━━━━━━━━
〄 Checked By: @$username***");
    }
else {
    send_message($chat_id,$message_id, "***INVALID BIN❌***");
}
}
    function send_message($chat_id,$message_id, $message){
        $text = urlencode($message);
        $apiToken = $_ENV['API_TOKEN'];  
        file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?chat_id=$chat_id&reply_to_message_id=$message_id&text=$text&parse_mode=Markdown");
    }
?>
