<?php
class Asset{
  
    // database connection and table name
    private $conn;
    private $table_name = "assetlist";
  
    // object properties
    public $id;
    public $Asset_no;
    public $Asset_desc;
    public $Category;
    public $Location;
    public $CalibDate_start;
    public $CalibDate_end;
    public $Company_name;
  
    public function __construct($db){
        $this->conn = $db;
    }
  
    // used by select drop-down list
    public function readAll(){
        //select all data
        $query = "SELECT
                    *
                FROM
                    " . $this->table_name . "
                ORDER BY
                    Asset_no";
  
        $stmt = $this->conn->prepare( $query );
        $stmt->execute();
  
        return $stmt;
    }

    // used by select drop-down list
public function read(){
  
        //select all data
        $query = "SELECT *
        FROM
             " . $this->table_name . "
            ORDER BY
                Asset_no";
  
        $stmt = $this->conn->prepare( $query );
        $stmt->execute();
    
        return $stmt;
}

// create product
function create(){
  
    
    // query to insert record
    $query = "INSERT INTO
                " . $this->table_name . "
            SET
                Asset_no=:Asset_no, Asset_desc=:Asset_desc, Category=:Category, Location=:Location, First_calib=:First_calib";
  
    // prepare query
    $stmt = $this->conn->prepare($query);
    
    // sanitize
    $this->Asset_no=htmlspecialchars(strip_tags($this->Asset_no));
    $this->Asset_desc=htmlspecialchars(strip_tags($this->Asset_desc));
    $this->Category=htmlspecialchars(strip_tags($this->Category));
    $this->Location=htmlspecialchars(strip_tags($this->Location));
    $this->First_calib=htmlspecialchars(strip_tags($this->First_calib));
  
    // bind values
    $stmt->bindParam(":Asset_no", $this->Asset_no);
    $stmt->bindParam(":Asset_desc", $this->Asset_desc);
    $stmt->bindParam(":Category", $this->Category);
    $stmt->bindParam(":Location", $this->Location);
    $stmt->bindParam(":First_calib", $this->First_calib);
 

    // execute query
    if($stmt->execute()){

        return true;
    }
  
        return false;
}

// used when filling up the update product form
function readOne(){
  
    // query to read single record
    $query = "SELECT * FROM
                " . $this->table_name . " 
            WHERE
                Asset_no = ?
            LIMIT
                0,1";
    
    // prepare query statement
    $stmt = $this->conn->prepare( $query );
  
    // bind Asset_no of product to be updated
    $stmt->bindParam(1, $this->Asset_no);
  
    // execute query
    $stmt->execute();
  
    // get retrieved row 
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        //extract $row to access its values while true 
        extract($row);

        //explode the values in First_calib array into separate values
        $message = $row['First_calib'];
        $arr = explode(",", $message);
        $CalibDate_start = $arr[0];
        $CalibDate_end = $arr[1];
        $Company_name = $arr[2];

        $row['Asset_no'] ??= '0' ;
   
            //if there is Asset_no , return true
            if($row['Asset_no'] !='0'){

            // set values to object properties
            $this->id = $row['id'];
             $this->Asset_no = $row['Asset_no'];
            $this->Asset_no = $row['Asset_no'];
            $this->Asset_desc = $row['Asset_desc'];
            $this->Category = $row['Category'];
            $this->Location = $row['Location'];
            $this->CalibDate_start = $CalibDate_start;
            $this->CalibDate_end = $CalibDate_end;
            $this->Company_name = $Company_name;

        }
    

        else{
            return false;
        }
    }
}
// delete the product
function delete(){
  
    // delete query
    $query = "DELETE FROM " . $this->table_name . " WHERE Asset_no = ?";
  
    // prepare query
    $stmt = $this->conn->prepare($query);
  
    // sanitize
    $this->Asset_no=htmlspecialchars(strip_tags($this->Asset_no));
  
    // bind Asset_no of record to delete
    $stmt->bindParam(1, $this->Asset_no);
  
    // execute query
    $stmt->execute();
    $count = $stmt->rowCount();
    //count the number of row afected by the query


    //check is there any query successfully executed
    if($count != '0'){
        return true;
    }
    else{
        return false;
    }
}

// search products
function search($keywords){
  
    // select all query
    $query = "SELECT *
            FROM
                " . $this->table_name . " 
            WHERE
                id LIKE ? OR Asset_no LIKE ? OR Asset_desc LIKE ?
            ORDER BY
                id ASC";
  
    // prepare query statement
    $stmt = $this->conn->prepare($query);
  
    // sanitize
    $keywords=htmlspecialchars(strip_tags($keywords));
    $keywords = "%{$keywords}%";
  
    // bind
    $stmt->bindParam(1, $keywords);
    $stmt->bindParam(2, $keywords);
    $stmt->bindParam(3, $keywords);
  
    // execute query
    $stmt->execute();
  
    return $stmt;
}

}
?>