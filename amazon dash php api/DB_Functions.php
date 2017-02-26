<?php


class DB_Functions {

    private $db;
    private $con;
    public $access_token;
    //put your code here

    // constructor
    function __construct() {
        //echo " hi constructor called";
        

        require_once 'DB_Connect.php';
        // connecting to database
        $this->db = new DB_Connect();
        $this->con = $this->db->connect();
     
}

    // destructor
    function __destruct() {
        
    }
 
    /**
     * Storing new user
     * returns user details
     */
    public function storeUser($name, $email, $access,$refresh,$time) {
        
        
        $this->access_token=$access;
        if($name!=''){
           $result = mysqli_query($this->con,"INSERT INTO users(id, name, email, ACCESS_TOKEN, REFRESH_TOKEN,TIME) VALUES(NUll, '$name', '$email', '$access', '$refresh','$time')");
        // check for successful store
        if ($result) {
            return true;
        } else {
            return false;
        } 
        }
        
    }

    public function updateuser($email, $access,$refresh,$time){
      //$date=$time->format('Y-m-d H:i:s');
        $this->access_token=$access;
    $query ="UPDATE `users` SET `ACCESS_TOKEN`='$access',`REFRESH_TOKEN`='$refresh',`TIME`='$time' WHERE email ='$email'";

        if ($result=mysqli_query($this->con, $query)) {  
             echo    "<br> User Data updated ";      
    } else {  echo "It failed";
           }

    }

    /**
     * Get user by email and password
     */
    public function getUserByEmail($email) {
        $result = mysqli_query($this->con,"SELECT * FROM users WHERE email ='$email'") ;
        // check for result 
        $no_of_rows = mysqli_num_rows($result);
        if ($no_of_rows > 0) {
            $result = mysqli_fetch_array($result);
            
                return $result;
            
        } else {
            // user not found
            return false;
        }
    }

    /**
     * Check user is existed or not
     */
    public function isUserExisted($email) {
        $result = mysqli_query($this->con,"SELECT email from users WHERE email = '$email'");
        $no_of_rows = mysqli_num_rows($result);
        if ($no_of_rows > 0) {
            // user existed 
            return true;
        } else {
            // user not existed
            return false;
        }
    }
 public function getData() {
        $result = mysqli_query($con,"SELECT value FROM data ") or die(mysqli_error());

            $result = mysqli_fetch_array($result);
                Print_r($result) ;

    }
  
  public function slot1($val,$email) {
        $result = mysqli_query($this->con,"UPDATE `users` SET `SLOT1`=$val WHERE `EMAIL` ='$email'") or die(mysqli_error());
if (!$result) { 
   echo "error"; 
}else{ 
echo "Slot 1 (tangerine) val is now =".$val ."<br>"; 

    }  
    }
      public function slot2($val,$email) {
        $result = mysqli_query($this->con,"UPDATE `users` SET `SLOT2`=$val WHERE `EMAIL` ='$email'") or die(mysqli_error());
if (!$result) { 
   echo "error"; 
}else{ 
echo "Slot 2 (cantaloupe) val is now =".$val ."<br>"; 

    }  
    }
      public function slot3($val,$email) {
        $result = mysqli_query($this->con,"UPDATE `users` SET `SLOT3`=$val WHERE `EMAIL` ='$email'") or die(mysqli_error());
if (!$result) { 
   echo "error"; 
}else{ 
echo "Slot 3 (yogurt) val is now =".$val ."<br>"; 

    }  
    }

public function slots_threshold($val1,$val2,$val3,$email) {
        $result = mysqli_query($this->con,"UPDATE `users` SET `pref_slot1`=$val1 , `pref_slot2`=$val2 , `pref_slot3`=$val3 WHERE `EMAIL` ='$email'") or die(mysqli_error());
if (!$result) { 
   echo "error"; 
}else{ 

echo "All Slots threshold value is now changed slot1 = ".$val1 ." slot2 =".$val2."slot3 = ".$val3." <br>"; 

    }  
    }


public function image($email){
    $result = mysqli_query($this->con,"SELECT `image` FROM users WHERE email ='$email'") ;
        $no_of_rows = mysqli_num_rows($result);
        if ($no_of_rows > 0) {
            $result = mysqli_fetch_array($result);
                echo '<img src="data:image/jpeg;base64,'.$result[0].'"/>';
                echo "<br>";
            
        } else {
            echo "faield";
        }
}
public function setsqlimage($img,$email){

        $result = mysqli_query($this->con,"UPDATE `users` SET `image`='$img' WHERE `EMAIL` ='$email'") or die(mysqli_error($this->con));

if (!$result) { 
  $_SESSION['error']='yes';
   echo "error"; 
}else{ 
    $_SESSION['error']='no';

echo "Image received and stored successfully from the device .<br>"; 

       }  
    }

}
?>

