<?php


class TestPlugin1 extends SC_Plugin_Ex {
  
    
    function enable(String $classname){
        
        return preg_match('/shopping|payment|products/',$classname)?
          preg_match($pattern, $subject)
        :
        ;
    }


}