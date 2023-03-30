<?php
class Account
{

    //private
    private $conn;
    public $username_auth;

    //public account data
    public $id;
    public $email;
    public $username;
    public $password;
    public $registration_time;

    //db_table
    private $db_table = 'account';
    
    //intialize
    public function __construct($db)
    {
        $this->conn = $db;
    }


    //GET ALL ACCOUNT
    public function get_accounts()
    {
        $sql_query = "SELECT id, username , password , registration_time , email FROM " . $this->db_table . "";
        $stmt = $this->conn->prepare($sql_query);
        $stmt->execute();
        return $stmt;
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
            throw new Exception('user name is already in use dummy');
        }

        //add an account
        $sql_query = "INSERT INTO " . $this->db_table . " SET  username = :username, password = :password, email = :email";
        $stmt = $this->conn->prepare($sql_query);

        // hash password
        $hash = password_hash($password, PASSWORD_DEFAULT);

        // bind data
        $value = array(':username' => $username, ':password' => $hash, ':email' => $this->email);
        // $stmt->bindParam(':username', $this->$username);
        // $stmt->bindParam(':password', $this->$hash);
        // $stmt->bindParam(':password', $this->$email);

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
        $sql_query = "SELECT id, username , password , registration_time , email  FROM " . $this->db_table . " WHERE id = ? LIMIT 0,1 ";
        $stmt = $this->conn->prepare($sql_query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        $data_row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->id = $data_row['id'];
        $this->username = $data_row['username'];
        $this->password = $data_row['password'];
        $this->registration_time = $data_row['registration_time'];
        $this->email = $data_row['email'];
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

        $sql_query = "UPDATE " . $this->db_table . " SET username = :username, password = :password WHERE id = :id";
        $stmt = $this->conn->prepare($sql_query);

        // hash password
        $hash = password_hash($password, PASSWORD_DEFAULT);


        // bind data
        $value = array(':username' => $username, ':password' => $hash, ':id' => $this->id);
        // $stmt->bindParam(':email', $this->email);
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
            throw new Exception('invalid account_ID');
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

        $sql_query = "SELECT * FROM " . $this->db_table . " WHERE username = :username";
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
                $this->username_auth = $username;
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

    }

}

