<?php


//This coding class is in charge of the communication with the server


class access {
    
    // variables for construction of the class and access to the server
    var $host = null;
    var $user = null;
    var $pass = null;
    var $name = null;
    
    var $conn = null;
    var $result = null;
    
    
    // build the full class
    function __construct($host, $user, $pass, $name) {
        
        // caching the server access data in order to use them later
        $this->host = $host;
        $this->user = $user;
        $this->pass = $pass;
        $this->name = $name;
        
        // establish connection
        $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->name);
        
        // error occured while constructing the class
        if (mysqli_connect_errno()) {
            echo 'Could not construct';
            return;
        }
        
        $this->conn->set_charset('utf8');
        
    }
    
    
    // establish connection with the server
    public function connect() {
        
        // establishing connection
        $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->name);
        
        // error occured while connecting
        if (mysqli_connect_errno()) {
            echo 'Could not connect';
            return;
        }
        
        $this->conn->set_charset('utf8');
        
    }
    
    
    // disconnect from the server once we finished using the server connection
    public function disconnect() {
        
        // if the connection is not null (established), close it
        if ($this->conn != null) {
            $this->conn->close();
        }
        
    }
    
    
    // Will try to select any value in database based on received Email
    public function selectUser($email) {
        
        // array to store full user related information with the logic: Key=>Value (Name=>John)
        $returnArray = array();
        
        // SQL Language / Commande to be sent to the server
        // SELECT * FROM users WHERE email='john@yahoo.com'
        $sql = "SELECT * FROM users WHERE email='" . $email . "'";
        
        // execuring query via already established connection with the server
        $result = $this->conn->query($sql);
        
        // result isn't zero and it has at least 1 row / value / result
        if ($result != null && (mysqli_num_rows($result)) >= 1) {
            
            // converting to be a JSON type
            $row = $result->fetch_array(MYSQLI_ASSOC);
            
            // assign fetched row to ReturnArray
            if (!empty($row)) {
                $returnArray = $row;
            }
            
        }
        
        // throw back returnArray
        return $returnArray;
        
    }
    
    
    // Inserting Data in the server receiving from the user (e.g. register.php)
    public function insertUser($email, $firstName, $lastName, $encryptedPassword, $salt, $birthday, $gender) {
        
        // SQL Language - command to inser data
        $sql = "INSERT INTO users SET email=?, firstName=?, lastName=?, password=?, salt=?, birthday=?, gender=?";
        
        // preparing SQL for execution by checking the validity
        $statement = $this->conn->prepare($sql);
        
        // if error
        if (!$statement) {
            throw new Exception($statement->error);
        }
        
        // assigning a variables instead of '?', after checking the preparation and validty of the SQL command
        $statement->bind_param('sssssss', $email, $firstName, $lastName, $encryptedPassword, $salt, $birthday, $gender);
        
        // $result will store the status / result of the execution of SQL command
        $result = $statement->execute();
        
        return $result;
        
    }
      
    
    // updating the path of the image (stored in the server) in the database
    function updateImageURL($type, $path, $id) {
        
        // UPDATE users SET ava=? WHERE id=?
        $sql = 'UPDATE users SET ' . $type . '=? WHERE id=?';
        
        // prepare command to be executed
        $statement = $this->conn->prepare($sql);
        
        // if error occured while execution
        if (!$statement) {
            throw new Exception($statement->error);
        }
        
        // assigning parameters to the prepared command execution
        $statement->bind_param('si', $path, $id);
        
        // $result will store the result of executed statement
        $result = $statement->execute();
                
        return $result;
        
    }
    
    
      // Will try to select any value in database based on received Email
    public function selectUserID($id) {
        
        // array to store full user related information with the logic: Key=>Value (Name=>John)
        $returnArray = array();
        
        // SQL Language / Commande to be sent to the server
        // SELECT * FROM users WHERE id='777'
        $sql = "SELECT * FROM users WHERE id='" . $id . "'";
        
        // execuring query via already established connection with the server
        $result = $this->conn->query($sql);
        
        // result isn't zero and it has at least 1 row / value / result
        if ($result != null && (mysqli_num_rows($result)) >= 1) {
            
            // converting to be a JSON type
            $row = $result->fetch_array(MYSQLI_ASSOC);
            
            // assign fetched row to ReturnArray
            if (!empty($row)) {
                $returnArray = $row;
            }
            
        }
        
        // throw back returnArray
        return $returnArray;
        
    }



    // updates bio in the server
    function updateBio($id, $bio){

        // declaring sql command
        $sql = 'UPDATE users SET bio=? WHERE id=?';

        // prepare SQL command for execute
        $statement = $this->conn->prepare($sql);

        // if error occured while preparing the statement to be exec
        if(!$statement){
            throw new Exception($statement->error);
        }

        // assign params to the prepapred SQL Command
        $statement->bind_param('si', $bio, $id);

        // access result of exec
        $result = $statement->execute();

        // returning result of exec
        return $result;

    }
    
    
}