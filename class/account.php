<?php
class Account
{

    //private
    private $conn;
    public $id_auth;
    public $authenticated;

    //public account data
    public $id;
    public $username;
    public $session_id;
    public $password;
    public $registration_time;
    public $account_active;

    //public session data
    public $session_id_session;
    public $account_id;
    public $login;


    //db_table
    private $db_table = 'account';
    private $db_table_session = 'session';
    //intialize

    public function __construct($db)
    {
        $this->conn = $db;
    }


    //GET ALL ACCOUNT
    public function get_accounts()
    {
        $sql_query = "SELECT id, username , session_id, password , registration_time , account_active FROM " . $this->db_table . "";
        $stmt = $this->conn->prepare($sql_query);
        $stmt->execute();
        return $stmt;
        // note - session_id and password should be removed in production
    }

    //CREATE ACCOUNT
    public function create_account()

    {

        $username = trim($this->username);
        $password = trim($this->password);

        //check user is valid

        if (!$this->is_name_valid($username)) {
            throw new Exception('invalid username');
        }

        // check password is valid
        if (!$this->is_pass_valid($password)) {
            throw new Exception('invalid password');
        }

        // check if user exists in database or same name
        if (!is_null($this->get_id_from_name($username))) {
            throw new Exception('user name is already in use');
        }

        //add an account
        $sql_query = "INSERT INTO " . $this->db_table . " SET  username = :username, password = :password, account_active = :account_active";
        $stmt = $this->conn->prepare($sql_query);

        // hash password
        $hash = password_hash($password, PASSWORD_DEFAULT);

        // intval $status is (1 = true)
        $int_status = 1 ;

        // bind data
        $value = array(':username' => $username, ':password' => $hash, ':account_active' => $int_status);
        // $stmt->bindParam(':username', $this->$username);
        // $stmt->bindParam(':password', $this->$hash);

        try {
            $stmt->execute($value);
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }

      
    }

    //santize username
    public function is_name_valid()
    {
        $valid = TRUE;

        //check length
        $length = mb_strlen($this->username);

        if (($length < 4 || $length > 16)) {
            $valid = FALSE;
        }

        return $valid;
    }

    //santize password
    public function is_pass_valid()
    {
        $valid = TRUE;

        //check length
        $length = mb_strlen($this->password);

        if (($length < 4 || $length > 16)) {
            $valid = FALSE;
        }

        return $valid;
    }


    //get id from name
    public function get_id_from_name()
    {

        if (!$this->is_name_valid($this->username)) {
            throw new Exception('invalid username');
        }

        $id = NULL;

        //Search id in db
        $sql_query = "SELECT id FROM " . $this->db_table . " WHERE username = :username";
        $stmt = $this->conn->prepare($sql_query);

        // bind data
        $value = array(':username' => $this->username);
        // $stmt->bindParam(':username', $this->username);

        try {
            $stmt->execute($value);
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }

        $data_set = $stmt->fetch(PDO::FETCH_ASSOC);

        //if result get id
        if (is_array($data_set)) {
            $id = intval($data_set['id'], 10);
        }

        return $id;
    }




    //GET SINGLE DATA
    public function get_account()

    {
        $sql_query = "SELECT id, username , password , registration_time , account_active FROM " . $this->db_table . " WHERE id = ? LIMIT 0,1 ";
        $stmt = $this->conn->prepare($sql_query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        $data_row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->id = $data_row['id'];
        $this->username = $data_row['username'];
        $this->password = $data_row['password'];
        $this->registration_time = $data_row['registration_time'];
        $this->account_active = $data_row['account_active'];
    }

    //UPDATE ACCOUNT OR EDIT
    public function update_account()
    {

        $username = trim($this->username);
        $password = trim($this->password);


        // check if id is valid
        if (!$this->is_id_valid($this->id)) {
            throw new Exception('invalid id');
        }

       
        // check password is valid
        if (!$this->is_pass_valid($this->password)) {
            throw new Exception('invalid password');
        }


        // check if user exists in database or same name apart from username
        $id_from_name = $this->get_id_from_name($username);

        if (!is_null($id_from_name) && ($id_from_name != $this->id)) {
            throw new Exception('user name is already in use');
        }

        $sql_query = "UPDATE " . $this->db_table . " SET username = :username, password = :password , account_active = :account_active WHERE id = :id";
        $stmt = $this->conn->prepare($sql_query);

        // hash password
        $hash = password_hash($this->password, PASSWORD_DEFAULT);

        // intval $status is (0 = failed, 1 = true)
        $int_status = $this->account_active ? 1 : 0;


        // bind data
        $value = array(':username' => $username, ':password' => $hash, ':account_active' => $int_status, ':id' => $this->id);
        // $stmt->bindParam(':account_active', $this->int_status);
        // $stmt->bindParam(':username', $this->$username);
        // $stmt->bindParam(':password', $this->$hash);
        // $stmt->bindParam(':id', $this->id);

        try {
            $stmt->execute($value);
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    //sanitize
    public function is_id_valid()
    {

        $valid = TRUE;

        //value of id must be between 1 $ 1000000

        if (($this->id < 1) || ($this->id > 1000000)) {
            $valid = FALSE;
        }

        return $valid;
    }



    //DELETE ACCOUNT
    function delete_account()
    {

        //checks id
        if (!$this->is_id_valid($this->id)) {
            throw new Exception('invalid account ID');
        }

        $sql_query = "DELETE FROM " . $this->db_table . " WHERE id = :id";
        $stmt = $this->conn->prepare($sql_query);


        // sanitize param
        $this->id = htmlspecialchars(strip_tags($this->id));

        // bind param
        $value = array(':id' => $this->id);
        // $stmt->bindParam(':id', $this->id);

        try {

            $stmt->execute($value);
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }

        //delete account session

        $sql_query = "DELETE FROM " . $this->db_table_session . " WHERE account_id = :id";
        $stmt = $this->conn->prepare($sql_query);

        // bind param
        $value = array(':id' => $this->id);
        // $stmt->bindParam(':id', $this->id);

        try {

            $stmt->execute($value);
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    //login

    public function login()
    {
        //trim
        $username = trim($this->username);
        $password = trim($this->password);

        //check username
        if (!$this->is_name_valid($username)) {
            return FALSE;
        }

        //check password
        if (!$this->is_pass_valid($password)) {
            return FALSE;
        }

        $sql_query = "SELECT * FROM " . $this->db_table . " WHERE username = :username  AND account_active = 1";
        $stmt = $this->conn->prepare($sql_query);

        // bind data
        $value = array(':username' => $username);
        // $stmt->bindParam(':username', $this->$username);

        try {

            $stmt->execute($value);
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }

        $data_set = $stmt->fetch(PDO::FETCH_ASSOC);

        //check if password matches
        if (is_array($data_set)) {
            if (password_verify($password, $data_set['password']) || password_verify($this->password, $data_set['password'])) {

                //if it matches, then set id and name
                $this->id_auth = intval($data_set['id'], 10);
                $this->authenticated = TRUE;

                //register session
                $this->register_login_session();

                return TRUE;
            }
        }
        //auth failed
        return FALSE;
    }

    //register login session 
    private function register_login_session()
    {

        if (session_start() == PHP_SESSION_ACTIVE) {

            // Use a REPLACE statement to insert a new row with the session id, if it doesn't exist or update.

            $sql_query = " UPDATE " . $this->db_table_session . " SET session_id = :session_id , account_id = :id , login = NOW()";
            $stmt = $this->conn->prepare($sql_query);

         
            // bind data
            $value = array(':session_id' => session_id(), ':id' => $this->id_auth);
            // $stmt->bindParam(':session_id', session_id());
            // $stmt->bindParam(':account_id', $this->id);

            try {   

                $stmt->execute($value);
            } catch (PDOException $e) {
                throw new Exception($e->getMessage( ));
            }
        }
    }

    // login using session

    public function session_login()
    {

        if (session_start() == PHP_SESSION_ACTIVE) {

            //query for a session id that is not older than 7 days

            $sql_query = "SELECT * FROM $this->db_table_session ,  $this->db_table WHERE $this->db_table_session.session_id = :sid" .
                "AND $this->db_table_session.login >= (NOW() - INTERVAL 7 DAYS) AND $this->db_table_session.account_id = $this->db_table.id" .
                "AND $this->db_table.account_active = 1";
            $stmt = $this->conn->prepare($sql_query);

            // bind data
            $value = array(':sid' => session_id());
            // $stmt->bindParam(':sid', session_id());


            try {

                $stmt->execute($value);
            } catch (PDOException $e) {
                throw new Exception($e->getMessage());
            }

            $data_set = $stmt->fetch(PDO::FETCH_ASSOC);

            if (is_array($data_set)) {
                //auth succesful set class property
                $this->id_auth = intval($data_set['id'], 10);
                $this->authenticated = TRUE;

                return TRUE;
            }
        }

        //auth failed
        return FALSE;
    }

    //logout
    public function logout()
    {

        //check if there's no user's
        if (is_null($this->id_auth)) {
            return;
        }

        //reset
        $this->id_auth = null;
        $this->authenticated = null;

        //remove open session from the account_sessions table

        if (session_status() == PHP_SESSION_ACTIVE)

            //delete session
        $sql_query = 'DELETE FROM ' . $this->db_table_session . " WHERE session_id = :sid";
        $stmt = $this->conn->prepare($sql_query);

        // bind data
        $value = array(':sid' => session_id());
        // $stmt->bindParam(':sid', session_id());

        try {

            $stmt->execute($value);
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    //check if a user is authenticated
    public function isAuthenticated()
    {
        return $this->authenticated;
    }

    //log out from other devices
    public function close_other_session()
    {

        //check if there's no user's
        if (is_null($this->id_auth)) {
            return;
        }

        //check session on
        if (session_status() == PHP_SESSION_ACTIVE) {

            //delete all session execpt the current one

            $sql_query = "DELETE FROM " . $this->db_table_session . "WHERE session_id != :sid AND account_id = :account_id ";
            $stmt = $this->conn->prepare($sql_query);

            // bind data
            $value = array(':sid' => session_id(), ':account_id' => $this->id_auth);
            // $stmt->bindParam(':sid', session_id(), ':account_id', $this->id_auth);

            try {

                $stmt->execute($value);
            } catch (PDOException $e) {
                throw new Exception($e->getMessage());
            }
        }
    }
}
