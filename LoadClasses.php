<?php
//namespace Scandiweb;

class LoadClasses{

    public function __construct(){

        //$this->baseDIR = $baseDIR;
    }

    public static function loadClass($className, $path = ""){

        $classFile = $className.'.php';
        $filePath = $path.''.$classFile;
        
        if(file_exists($filePath) AND $className != self::class){
         
            spl_autoload_register(function($filePath){
               
                 require_once $filePath;
            });
          
           spl_autoload_call($filePath);
         
            $allLoaded = get_declared_classes();
        
             foreach($allLoaded as $key => $value){

                if($value === $className){
                    return $className;
                }
            }
        }
        return null;
    }

   public function initLoad($baseDIR){

    $files = scandir($baseDIR);
    foreach($files as $file){

      preg_match("/[\w]+.php/", $file, $matches);

      if(empty($matches) === false){
      
         $className = str_replace(".php", "", $matches[0]);
         self::loadClass($className);
      }
    }
   }
}
?>