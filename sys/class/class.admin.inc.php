<?php

declare(strict_types=1);

class Admin extends DB_Connect {
    private $_saltLength = 7;
    
    public function __construct($db = NULL, $saltLength = NULL) {
        parent::__construct($db);
        if (is_int($saltLength))
            $this->_saltLength = $saltLength;
    }
    public function processLoginForm() {
        if ($_POST['action'] != 'user_login') 
            return "Invalid action supplied for ProcessForm.";
        $uname = htmlentities($_POST['uname'], ENT_QUOTES);
        $pword = htmlentities($_POST['pword'], ENT_QUOTES);
        
        $query = "SELECT * FROM users WHERE user_name = '$uname' LIMIT 1";
        
        try {
            $result = mysqli_query($this->db, $query);
            $user = mysqli_fetch_assoc($result);
            mysqli_close($this->db);
        } catch(Exception $e) { 
            die($e->getMessage());
        }
        if (!isset($user))
            return "Your username or password is invalid.";
        
        $hash = $this->_getSaltedHash($pword, $user['user_pass']);
        if ($user['user_pass'] == $hash) {
            $_SESSION['user'] = array(
                'id' => $user['user_id'],
                'name' => $user['user_name'],
                'email' => $user['user_email']
            );
            return TRUE;
        } else {
            return "Your username or password failed miserably.";
        }   
    }
    private function _getSaltedHash($string, $salt=NULL) {
        if ($salt == NULL)
            $salt = substr(md5((string)time()), 0, $this->_saltLength);
        else
            $salt = substr($salt, 0, $this->_saltLength);
        return $salt . sha1($salt . $string);
    }
    public function processLogout() {
        if ($_POST['action'] != 'user_logout')
            return "Invalid action supplied for logout.";
        session_destroy();
        return TRUE;
    }
}
?>
