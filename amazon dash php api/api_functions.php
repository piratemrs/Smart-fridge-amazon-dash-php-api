<?php
error_reporting(0);

session_start();
function login(){

$now=date("h:i:sa");
$date = new DateTime($now);
$date->add(new DateInterval('PT1H'));
$_SESSION["time"]=$date->format('Y-m-d H:i:s');

$debug=true;
$now=date("h:i:sa");
$date = new DateTime($now);
$date->add(new DateInterval('PT1H'));
$ch = curl_init();
$parms="grant_type=authorization_code&code=". urlencode($_GET['code'])."&client_id=amzn1.application-oa2-client.b63589f13fe74455982f0dc8069f2a2d&client_secret=c5a805cb512ab8a13ffc2a0d12665b2e27bdf7368203b0a2ab23fbd21a61cdc5&redirect_uri=https://dkyf2jsa9ujaq.cloudfront.net/handle_login.php";
curl_setopt($ch, CURLOPT_URL,"https://api.amazon.com/auth/o2/token");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch,CURLOPT_HTTPHEADER,array('Content-Type: application/x-www-form-urlencoded'));
curl_setopt($ch, CURLOPT_POSTFIELDS,$parms);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$server_output = curl_exec ($ch);
if (curl_errno($ch)&&$debug) { 
   print curl_error($ch); 
} 
$d = json_decode($server_output,true);
echo json_encode($d);
    $t1=$d["refresh_token"];
    $t2=$d["access_token"];
    $_SESSION["access_token"]=$t2;
    $_SESSION["refresh_token"]=$t1;
    $_SESSION["time"]=$date->format("Y-m-d H:i:s");
    
    $access_token=$t2;
    require_once 'DB_Functions.php';

    if($t1!=''){
    $refresh_file = $t1;
    }else{echo 'refresh_token is NULL';}
    if($t2!=''){
    $access_file =$t2;
    }else{echo 'access_token is NULL';}

    if($debug){
        echo "\n".json_encode($d);
        var_dump($d);
        
    }

curl_close ($ch);
}

function complete_login(){
    require_once 'DB_Functions.php';
   $db = new DB_Functions();
   if (isset($_SESSION['email'])){
        if ($db->isUserExisted($_SESSION["email"])) {
        echo "<br> User already registered ";
        if($_SESSION["refresh_token"]!='' AND $_SESSION["access_token"]!=''){

        $db->updateuser($_SESSION["email"],$_SESSION["access_token"],$_SESSION["refresh_token"],$_SESSION["time"]);

    }
    }else{
    if($_SESSION["refresh_token"]!='' AND $_SESSION["access_token"]!=''){

        $db->storeUser($_SESSION["name"],$_SESSION["email"],$_SESSION["access_token"],$_SESSION["refresh_token"],$_SESSION["time"]);
    }}

   }
     $_SESSION['loggedin']='yes';

}

function slots_threshold_update($val1,$val2,$val3) {
  checktoreplensish();
   require_once 'DB_Functions.php';
   $db = new DB_Functions();
$db->slots_threshold($val1,$val2,$val3,$_SESSION["email"]);
       
    }

function refresh(){

 $now=date("Y-m-d H:i:s");
$date = new DateTime($now);
$date->add(new DateInterval('PT1H')); 
$date->format('Y-m-d H:i:s');

    $time=$_SESSION["time"];
  
        print_r($time);
        echo" SAVED ( time at which next refresh called +1 HR )"."<br>"."<br>";
        echo date("Y-m-d H:i:s")." Time now "."<br>"."<br>";
       
    if ($time<date("Y-m-d H:i:s")) {
        refresh_token();
        echo "expired"."<br>";
    }else{
        echo "Access Token Not expired yet"." remaining"."<br>";
        $i=(strtotime($time)-strtotime($now));
        echo gmdate("H:i:s", $i)."<br>"." Till requesting new one "."<br>";

}
    

}

function refresh_token(){
    require_once 'DB_Functions.php';
   $db = new DB_Functions();
    $debug=true;

$now=date("Y-m-d H:i:s");
$date = new DateTime($now);
$date->add(new DateInterval('PT1H')); 
echo $date->format('Y-m-d H:i:s');

    $refresh_token= $_SESSION["refresh_token"];
    echo ''.($debug ? $refresh_token."<br>" : '');


    $access_token= $_SESSION["access_token"];
    echo ''.($debug ? $access_token."<br>" : '');
$ch = curl_init();
$parms="grant_type=refresh_token&refresh_token=". $refresh_token."&client_id=amzn1.application-oa2-client.b63589f13fe74455982f0dc8069f2a2d&client_secret=c5a805cb512ab8a13ffc2a0d12665b2e27bdf7368203b0a2ab23fbd21a61cdc5";
curl_setopt($ch, CURLOPT_URL,"https://api.amazon.com/auth/o2/token");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch,CURLOPT_HTTPHEADER,array('Content-Type: application/x-www-form-urlencoded'));
curl_setopt($ch, CURLOPT_POSTFIELDS,$parms);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$server_output = curl_exec ($ch);
if (curl_errno($ch)&&$debug) { 
   print curl_error($ch); 
} 
$d = json_decode($server_output,true);
json_encode($d);
$t1=$d["refresh_token"];
$t2=$d["access_token"];
if($t1!=''){
$_SESSION["refresh_token"] = $t1;
}else{echo 'refresh_token is NULL';}
if($t2!=''){
$_SESSION["access_token"] =$t2;
}else{echo 'access_token is NULL';}
$_SESSION["time"]=$date;
if($t1!='' AND $t2!=''){
    $db->updateuser($_SESSION["email"],$t2,$t1,$date->format('Y-m-d H:i:s'));
}else {
    echo "Acces token or refresh_token are Null ";
}
if($debug){
    echo "\n".json_encode($d);
    var_dump($d);
   
}
curl_close ($ch);
}



function status(){
require_once 'DB_Functions.php';
$db = new DB_Functions();
$user = $db->getUserByEmail($_SESSION["email"]);
echo('<div class="alert alert-success"><br> SLots Status<br>');
echo('cantaloupees now =');
echo($user['SLOT1']);
echo('<br>Tangerines now =');

echo($user['SLOT2']);
echo('<br>yogurt now =');

echo($user['SLOT3']);
echo("<br><br> SLots threshold at which the device replenishes<br>");
echo('<br>cantaloupees threshold =');

echo($user['pref_slot1']);
echo('<br>Tangerines threshold =');

echo($user['pref_slot2']);
echo('<br>yogurt threshold =');

echo($user['pref_slot3']);

$c = curl_init('https://dash-replenishment-service-na.amazon.com/subscriptionInfo');
curl_setopt($c, CURLOPT_HTTPHEADER, array(
    'Authorization:' .'Bearer '. $_SESSION['access_token'],
        "x-amzn-accept-type: com.amazon.dash.replenishment.DrsSubscriptionInfoResult@1.0",
        "x-amzn-type-version: com.amazon.dash.replenishment.DrsSubscriptionInfoInput@1.0"

    ));

curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
$r = curl_exec($c);
curl_close($c);
$d = json_decode($r,true);

echo "\n";
  echo "<br>";
  echo ''.$d['slotsSubscriptionStatus']["393b494e-7c13-4e37-986c-36743000c687"]? '<br>You are Subscribed to cantaloupees' : '<br>You are not Subscribed to cantaloupees';
echo ''.$d['slotsSubscriptionStatus']["a85b05c4-49b7-497b-84c2-882cda942666"]? '<br>You are Subscribed to tangerine' : 'You are not Subscribed to tangerine';
echo ''.$d['slotsSubscriptionStatus']["09e0bfaf-60ff-41dc-863c-05f770213191"]? '<br>You are Subscribed to yogurt<br>' : 'You are not Subscribed to yogurt<br>';
}

function replenish($slot){
$c = curl_init('https://dash-replenishment-service-na.amazon.com/replenish/'.$slot);
curl_setopt($c, CURLOPT_HTTPHEADER, array(
    'Authorization:' .'Bearer '. $_SESSION['access_token'],
        "x-amzn-accept-type: com.amazon.dash.replenishment.DrsReplenishResult@1.0",
        "x-amzn-type-version: com.amazon.dash.replenishment.DrsReplenishInput@1.0"

    ));

curl_setopt($c, CURLOPT_POST, true);
curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);

$r = curl_exec($c);
    if (curl_getinfo($c, CURLINFO_HTTP_CODE) == 200)
        {
            $d = json_decode($r,true);
            print_r($d['detailCode']);

            echo "<br>";
        } else{
             $d = json_decode($r,true);
            print_r($d['message']);
              echo "<br>";
        }  
curl_close($c);

}
function updateslot1($slotval){
  checktoreplensish();
    require_once 'DB_Functions.php';
   $db = new DB_Functions();
   if (isset($_SESSION['email'])){
        if ($db->isUserExisted($_SESSION["email"])) {
        $db->slot1($slotval,$_SESSION["email"]);
    }
}
}
function updateslot2($slotval){
  checktoreplensish();
    require_once 'DB_Functions.php';
   $db = new DB_Functions();
   if (isset($_SESSION['email'])){
        if ($db->isUserExisted($_SESSION["email"])) {
        

        $db->slot2($slotval,$_SESSION["email"]);

    }
}
}
function updateslot3($slotval){
  checktoreplensish();
    require_once 'DB_Functions.php';
   $db = new DB_Functions();
   if (isset($_SESSION['email'])){
        if ($db->isUserExisted($_SESSION["email"])) {
         $db->slot3($slotval,$_SESSION["email"]);

    }
}
}




function login_registered_user($email){
require_once 'DB_Functions.php';
  $cantaloupe='393b494e-7c13-4e37-986c-36743000c687';
  $tangerine='a85b05c4-49b7-497b-84c2-882cda942666';
  $yogurt='09e0bfaf-60ff-41dc-863c-05f770213191';
print_r($email);
$db = new DB_Functions();
if ($db->isUserExisted($email)) {
  echo "<br> User found <br>";
  $user = $db->getUserByEmail($email);
  echo($user['ID']);
$_SESSION["email"]=$user['EMAIL'];
$_SESSION["name"]=$user['NAME'];
$_SESSION["access_token"]=$user['ACCESS_TOKEN'];
$_SESSION["refresh_token"]=$user['REFRESH_TOKEN'];
$_SESSION["time"]=date("Y-m-d H:i:s",strtotime($user['TIME']));
if($user['SLOT1']<$user['pref_slot1'])
{
  echo "<br> replenish slot 1 <br>";
  replenish($cantaloupe);
}
if($user['SLOT2']<$user['pref_slot2'])
{
    echo "<br> replenish slot 2 <br>";
    replenish($tangerine);
}
if($user['SLOT3']<$user['pref_slot3'])
{
      echo "<br> replenish slot 3 <br>";
      replenish($yogurt);
}
print_r($_SESSION["time"]);
echo "<br> session time <br>";
  var_dump($user);
}else{
  echo "<br> no user found <br>";
}

}

function checktoreplensish(){
require_once 'DB_Functions.php';
  $cantaloupe='393b494e-7c13-4e37-986c-36743000c687';
  $tangerine='a85b05c4-49b7-497b-84c2-882cda942666';
  $yogurt='09e0bfaf-60ff-41dc-863c-05f770213191';
$db = new DB_Functions();

  $user = $db->getUserByEmail($_SESSION['email']);
 
if($user['SLOT1']<$user['pref_slot1'])
{
  echo "<br> replenish 1 <br>";
  replenish($cantaloupe);
}
if($user['SLOT2']<$user['pref_slot2'])
{
    echo "<br> replenish slot 2 <br>";
    replenish($tangerine);
}
if($user['SLOT3']<$user['pref_slot3'])
{
      echo "<br> replenish slot 3 <br>";
      replenish($yogurt);
}



}


function deregister(){
$c = curl_init('https://dash-replenishment-service-na.amazon.com/deviceModels/7843a0b9-a47a-42e5-ad06-69dbbf3ae78e/devices/123456/registration');
curl_setopt($c, CURLOPT_HTTPHEADER, array(
    'Authorization:' .'Bearer '. $_SESSION['access_token'],
        "x-amzn-accept-type: com.amazon.dash.replenishment.DrsDeregisterResult@1.0",
        "x-amzn-type-version: com.amazon.dash.replenishment.DrsDeregisterInput@1.0"

    ));

curl_setopt($c, CURLOPT_CUSTOMREQUEST, "DELETE");
curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);

curl_exec($c);

if (curl_getinfo($c, CURLINFO_HTTP_CODE) == 200)
    {
    echo "Deregistgered Succesfully from Amazon DRS logout then login to register again <br>" ;
    }   
curl_close($c);


}

function slotstaus($slot_id,$name){

    $c = curl_init('https://dash-replenishment-service-na.amazon.com/slotStatus/'.$slot_id);

curl_setopt($c, CURLOPT_HTTPHEADER, array(
    'Authorization:' .'Bearer '. $_SESSION['access_token'],
        "x-amzn-accept-type: com.amazon.dash.replenishment.DrsSlotStatusResult@1.0",
        "x-amzn-type-version: com.amazon.dash.replenishment.DrsSlotStatusInput@1.0"

    ));
curl_setopt($c, CURLOPT_POSTFIELDS,'{
    "expectedReplenishmentDate" : "2015-12-28T10:00:00Z",
    "remainingQuantityInUnit" : 3.5,
    "originalQuantityInUnit" : 10,
    "totalQuantityOnHand" : 20,
    "lastUseDate" : "2015-12-21T10:00:00Z"
}');

curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
 
$r = curl_exec($c);
if (curl_errno($c)&&$debug) { 
   print curl_error($c); 
} 
$code = curl_getinfo($c, CURLINFO_HTTP_CODE);
            echo "<pre/>";
            if ($code == 200)
            {
                echo " Slot ".$name." with id ".$slot_id." Consumption report sent Succesfully to Amazon DRS <br>" ;
            }           

curl_close($c);

}


function report_Device_activity(){
$datetime = new DateTime();


$c = curl_init('https://dash-replenishment-service-na.amazon.com/deviceStatus');

curl_setopt($c, CURLOPT_HTTPHEADER, array(
    'Authorization:' .'Bearer '. $_SESSION['access_token'],
        "x-amzn-accept-type: com.amazon.dash.replenishment.DrsDeviceStatusResult@1.0",
        "x-amzn-type-version: com.amazon.dash.replenishment.DrsDeviceStatusInput@1.0"

    ));
curl_setopt($c, CURLOPT_POSTFIELDS,'{
    "mostRecentlyActiveDate" : '."\"".$datetime->format(\DateTime::ISO8601)."\"".'
    }');

curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
 
$r = curl_exec($c);
if (curl_errno($c)) { 
   print curl_error($c); 
} 
$code = curl_getinfo($c, CURLINFO_HTTP_CODE);
            echo "<pre/>";
            if ($code == 200)
            {
                echo "Device Activity report sent Succesfully to Amazon DRS <br>";
            }           

curl_close($c);

}

function getimage(){
  require_once 'DB_Functions.php';
  $db = new DB_Functions();
  $db->image($_SESSION["email"]);
}
function setimage($img,$email){
  require_once 'DB_Functions.php';
  $db = new DB_Functions();
  $db->setsqlimage($img,$email);
}

?>