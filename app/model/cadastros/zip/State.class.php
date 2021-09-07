<?php
/**
 * State Active Record
 * @author  <your-name-here>
 */
class State extends TRecord
{
    const TABLENAME = 'state';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('name');
        parent::addAttribute('country_id');
    }
    
    /**
     * Method get_country
     * Sample of usage: $state->country->attribute;
     * @returns Country instance
     */
    public function get_country()
    {
        $obj = new Country($this->country_id);
        return $obj->name;
    }
}