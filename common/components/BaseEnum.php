<?php



/**
 * Description of BaseEnum
 * Used to allow const values to be used to access the const name
 * 
 * How to use 
 * $error = xxxErrorCodes::toString($code);
 * @author helenk
 */
namespace common\components;
use ReflectionClass;
abstract class BaseEnum{
    private final function __construct(){ }

    public static function toString($val){
        $tmp = new ReflectionClass(get_called_class());
        $a = $tmp->getConstants();
        $b = array_flip($a);

        //return ucfirst(strtolower($b[$val]));
        return $b[$val];
    }
}



?>
