<?php
/**
 * Table Definition for AnonymousUsers
 */
class DataObjects_Converted_Files{
    public $__table = 'Converted_Files'; // table name
    //These column names must exactly match variable names below. Case and all.
    public $id        = 0;
    public $file_name = null;
    public $file_md5  = null;
    public $user_id   = 0;

    private $n;        // number of rows
    private $result;   //Result from find.

    /**
     * Constructor: If an email is passed in a new user is created but not inserted into the database. 
     *              call DataObjects_Anonymous_Users::insert() to insert the user.
     * @param string $email an email address of a new user
     * @throw User already exists
     */
    public function __construct($file_name='', $user=null){
        if( !$user ){
            $this->id        = 0;
            $this->user_id   = 0;      
            $this->file_name = null;
            $file_md5        = null;

            $this->n = 0; //row count
            $this->result = null;
            return;
        }else{
            if( $this->create( $file_name, $user->id ) === false ) throw new Exception('User already exists.');//TODO: Possibly change this so it doesn't throw and exception
        }
    }

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
    private function create( $file_name, $file_md5, $user_id ){
        $this->user_id   = $user_id;
        $this->file_name = $file_name;
        $this->file_md5  = $file_md5;

        if( !$this->find(true) ){//The user id doesn't exists.
            if( $this->insert() )
                $this->fetch();
            else
                throw new Exception('Could not create file' . $this->file_name);
        }
        return $this;
    }

    /**
     * Inserts this object or the one passed in.
     * @param DataObjects_Anonymous_Users $do_convertedFiles an object to insert 
     * @return: insert result
     * @access public
     */
    public function insert( $do_convertedFiles=null ){
        $sql = "INSERT INTO " . $this->__table . "(user_id, file_name, file_md5) VALUES ('%s', '%s', '%s')";
        $rtn = false;

        if( $do_convertedFiles != null && ($do_convertedFiles instanceof DataObjects_User) ){
            $sql = sprintf($sql, $do_convertedFiles->user_id, $do_convertedFiles->file_name, $do_convertedFiles->file_md5); 
        }else{
            $sql = sprintf($sql, $this->user_id, $this->file_name, $this->file_md5); 
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
    public function find( $n=false ){
        //$vars = get_object_vars($this); //Use this function to build a query based on fields that are filled in.
        if( $this->id != 0 )
            $rtn = $this->get( $this->id );
        else if( $this->file_md5 != null && $this->user_id ){
            if(  $this->result )
                mysql_free_result( $this->result );
            $sql = "SELECT * FROM " . $this->__table . " WHERE %s = '%s' AND %s = '%s'";
            $sql = sprintf($sql, 'user_id', $this->user_id, 'file_md5', $this->file_md5);
            $this->result = mysql_query($sql);
            if( $this->result !== false )
                $rtn = $this->n = mysql_num_rows( $this->result );
            else
                $rtn = false;
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

