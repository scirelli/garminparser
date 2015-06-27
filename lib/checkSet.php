<?PHP
//------------------------------------------------
// Helper func for initializing post/get variables
// @param: $var - mixed; the post/get variable
// @param: $default - mixed; a default value for
//         $var
// @return: the default value or what is in the
//  post/get variable
//------------------------------------------------
function checkSet( $var, $default = '', $toLower=true){
    if( isset($_REQUEST[$var]) && $_REQUEST[$var] != '' ){
        if( $toLower === true ){
            return strtolower($_REQUEST[$var]);
        }else{
            return $_REQUEST[$var];
        }
    }
    return $default;
}
?>
