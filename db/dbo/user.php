<?php
/**
 * Table Definition for AnonymousUsers
 */
class DataObjects_User{
    public $__table = 'Users'; // table name
    //These column names must exactly match variable names below. Case and all.
    public $id      = 0;
    public $user_id = '';
    public $joinDate;

    private $n;        // number of rows
    private $result;   //Result from find.

    /**
     * Constructor: If an email is passed in a new user is created but not inserted into the database. 
     *              call DataObjects_Anonymous_Users::insert() to insert the user.
     * @param string $email an email address of a new user
     * @throw User already exists
     */
    public function __construct($user_id=null){
        if( $user_id == null ){
            $this->id       = 0;
            $this->joinDate = null;   
            $this->user_id  = null;      
            $this->n = 0; //row count
            $this->result = null;
            return;
        }else{
            if( $this->create( $user_id ) === false ) throw new Exception('User already exists.');//TODO: Possibly change this so it doesn't throw and exception
        }
    }

    //function __clone(){
    //}

    /**
     * Creates a new anonymous user and fills the current obj with the info
     * Desc: Tries to create an anonymous user. 
     *       If the user already exists it will fill this obj with the users info.
     *       If the email is not a valid email it will throw an Invalid Email error
     * @param: string $email the users email.
     * @return: a pointer this object or false if the user already existed.
     * @throw: Excption if invalid email.
     * @acces: public
     **/ 
    private function create( $user_id ){
        $this->user_id = $user_id;

        if( !$this->find(true) ){//The user id doesn't exists.
            $this->joinDate = date("Y-m-d");
            if( $this->insert() )
                $this->fetch();
            else
                throw new Exception('Could not create user ' . $this->user_id);
        }
        return $this;
    }

    /**
     * Inserts this object or the one passed in.
     * @param DataObjects_Anonymous_Users $do_anonymousUser an object to insert 
     * @return: insert result
     * @access public
     */
    public function insert( $do_anonymousUser=null ){
        $sql = "INSERT INTO " . $this->__table . "(user_id, joinDate) VALUES ('%s', '%s')";
        $rtn = false;

        if( $do_anonymousUser != null && ($do_anonymousUser instanceof DataObjects_User) ){
            $sql = sprintf($sql, $do_anonymousUser->user_id, $do_anonymousUser->joinDate); 
        }else{
            $sql = sprintf($sql, $this->user_id, $this->joinDate); 
        }
        $rtn = mysql_query( $sql );
        $this->id =  $this->getLastInsertId();
        return $rtn;
    }

    private function getLastInsertId(){
        $result = mysql_query( 'SELECT last_insert_id() as id' );
        $result = mysql_fetch_assoc( $result );
        return $result['id'];
    }

    /**
     * find results, either normal or crosstable
     *
     * for example
     *
     * $object = new mytable();
     * $object->ID = 1;
     * $object->find();
     *
     *
     * will set $object->N to number of rows, and expects next command to fetch rows
     * will return $object->N
     *
     * @param   boolean $n Fetch first result
     * @access  public
     * @return  mixed (number of rows returned, or true if numRows fetching is not supported)
     * @throws Exception if email or anonuser_id is not set
     */
    private function find( $n=false ){
        //$vars = get_object_vars($this); //Use this function to build a query based on fields that are filled in.
        if( $this->id != 0 )
            $rtn = $this->get( $this->id );
        else if($this->user_id != null){
            $rtn = $this->get( 'user_id', $this->user_id );
        }else{
            throw new Exception('id or user_id is not set.');
        }
        if( $rtn && $n === true ){
            $this->fetch();
        }

        return $rtn; 
    }
    
    /**
     * fetches next row into this objects var's
     *
     * returns 1 on success 0 on failure
     **/
    private function fetch(){
        $row = mysql_fetch_assoc( $this->result );
        if( $row === false ) return false;

        foreach($row as $var => $value){
            if( property_exists($this, $var) ){
                $this->$var = $row[$var];
            }
        }
        return true;
    }
    
    /**
     * Gets a key $k with a value $v. 
     * If $v is null then search is done by anonuser_id = $k
     * return: number of rows if successful
     * TODO: This function will have to be updated to accomodate differnt data types
     **/
    private function get($k,$v=NULL) {
        if(  $this->result )
            mysql_free_result( $this->result );
        $sql = "SELECT * FROM " . $this->__table . " WHERE %s = '%s'";
        if($v == NULL){
            $v = $k;
            $k = 'id';
        }else{
            $k = strtolower( $k );
        }

        $sql = sprintf($sql, $k, $v);
        $this->result = mysql_query($sql);
        if( $this->result !== false )
            return $this->n = mysql_num_rows( $this->result );
        return false;
    }
}
/*
 * Counting rows
$stmtMain = $mysqli->prepare("SELECT SQL_CALC_FOUND_ROWS jobid,title FROM tbljobs 
    LIMIT ?, ? UNION SELECT FOUND_ROWS(),'!!IgnoreCount!!';")

Then iterate through the results with something like:
 */
?>
