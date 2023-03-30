<?php
class Cart
{

    //private
    private $conn;

    public $id;
    public $price;
    public $name;

    //db_table
    private $db_table = 'cart';
    
    //intialize
    public function __construct($db)
    {
        $this->conn = $db;
    }


    //GET ALL CART
    public function get_carts()
    {
        $sql_query = "SELECT id, name , price  FROM " . $this->db_table . "";
        $stmt = $this->conn->prepare($sql_query);
        $stmt->execute();
        return $stmt;
    }




    //GET SINGLE DATA
    public function get_cart()

    {
        $sql_query = "SELECT id, name , price  FROM " . $this->db_table . " WHERE id = ? LIMIT 0,1 ";
        $stmt = $this->conn->prepare($sql_query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        $data_row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->id = $data_row['id'];
        $this->name = $data_row['name'];
        $this->price = $data_row['price'];
    }

    //UPDATE CART OR EDIT
    public function update_CART()
    {

        $name = trim($this->name);
        $password = trim($this->price);


        // check if id is valid
        if (!$this->is_id_valid($this->id)) {
            throw new Exception('invalid id');
        }

    
        $sql_query = "UPDATE " . $this->db_table . " SET name = :name, price = :price WHERE id = :id";
        $stmt = $this->conn->prepare($sql_query);



        // bind data
        $value = array(':name' => $name, ':price' => $hash, ':id' => $this->id);

        try {
            $stmt->execute($value);
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }



    //DELETE CART
    function delete_CART()
    {

        //checks id
        if (!$this->is_id_valid($this->id)) {
            throw new Exception('invalid CART_ID');
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

}