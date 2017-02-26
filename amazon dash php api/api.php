<?php

include("api_functions.php");

    $debug=true;

if(isset($_POST['setimage'])){
  setimage($_POST['setimage'],$_POST['email']);
}

if(isset($_GET['getimage'])){
  getimage();
}

if(isset($_GET['slot1'])){

slots_threshold_update($_GET['slot1'],$_GET['slot2'],$_GET['slot3']);
  
}
if (isset($_GET["email"]))
{
echo "email =".($_GET["email"])."\r\n<br/>";
login_registered_user($_GET["email"]);

}
if (isset($_GET["tangerine"]))
{
echo "tangerine =".($_GET["tangerine"])."\r\n<br/>";
updateslot1($_GET["tangerine"]);
echo "\r\n";
}
if (isset($_GET["cantaloupe"]))
{
echo "cantaloupe =".($_GET["cantaloupe"])."\r\n<br/>";
updateslot2($_GET["cantaloupe"]);
}

if (isset($_GET["yogurt"]))
{
echo "yogurt =".($_GET["yogurt"])."\r\n<br/>";
updateslot3($_GET["yogurt"]);
}

if (isset($_GET['refresh'])) {
  refresh();
}
if (isset($_GET['scope'])) {
	login();

}
if (isset($_GET['replenish'])) {
  $cantaloupe='393b494e-7c13-4e37-986c-36743000c687';
  $tangerine='a85b05c4-49b7-497b-84c2-882cda942666';
  $yogurt='09e0bfaf-60ff-41dc-863c-05f770213191';
  if($_GET['replenish']=='cantaloupe'){
    replenish($cantaloupe);
  }
   if($_GET['replenish']=='tangerine'){
    replenish($tangerine);
  }
  if($_GET['replenish']=='yogurt'){
    replenish($yogurt);
  }
}
if (isset($_GET['status'])) {
  refresh();
  checktoreplensish();
  status();
}
if (isset($_GET['slot_status'])) {
    $cantaloupe='393b494e-7c13-4e37-986c-36743000c687';
  $tangerine='a85b05c4-49b7-497b-84c2-882cda942666';
  $yogurt='09e0bfaf-60ff-41dc-863c-05f770213191';
  refresh();
  slotstaus($cantaloupe,"cantaloupe");
  slotstaus($tangerine,"tangerine");
  slotstaus($yogurt,"yogurt");
}

if(isset($_GET['deregister'])){
    deregister();
}
if(isset($_GET['report_Device_activity'])){
    report_Device_activity();
} ?>