<?php
class CYBS_REQ {
  var $classname = "CYBS_REQ";
  var $requests = array();
  var $currentItem = 1;
  
  ##
  ## Return the position and number of units 
  ## of an article in the cart(or false and 0, 
  ## if it is not in there)
  ##
  function check($key) {

    if (!is_array($this->requests))
      return array(false, 0);

    reset($this->requests);
    while(list($field_key, $field_val) = each($this->requests)) {
    
      if (isset($field_key) 
       && ($field_key == $key)) {
           return array($field_key, $field_val);
      }
    }
    
    return array(false, 0);
  }

  function reset() {

    reset($this->requests);
    while(list($field_key, $field_val) = each($this->requests)) {
      unset($this->requests[$field_key]);
    }
    return true;
  }

  function add_request($key, $val) {
  
    ## Check to see if we already have some of these
    list($field_key, $field_val) = $this->check($key);
    
    ## We already have them
    if ($field_key) {
      return $field_key;
    }
    
    ## New field 
    $this->requests[$key] = $val;

    return $field_key;
  }
  
  function remove_request($key) {
  
    ## Check to see if we have some of these
    list($field_key, $field_val) = $this->check($key);
    
    ## Can't take them out
    if (!$field_key) {
      return false;
    }
    
    unset($this->requests[$field_key]);
    return $field_key;
  }

  # Get requests
  function get_requests() {
    if (!is_array($this->requests))
      return false;

    return $this->requests;
  }

  #
  # For debug use. 
  #
  function show_all() {
    if (!is_array($this->requests) or ($this->requests) == 0) {
      return false;
    }

    reset($this->requests);
    while(list($field_key, $field_val) = each($this->requests)) {
      $this->show_field($field_key, $field_val);
    }
  }

  function show_field($key, $val) {
    printf("%s=%s<br>\n", $key, $val);
  }

}
?>
