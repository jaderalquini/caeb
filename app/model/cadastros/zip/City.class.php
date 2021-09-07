<?php
/**
 * City Active Record
 * @author  <your-name-here>
 */
class City extends TRecord
{
    const TABLENAME = 'city';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('name');
        parent::addAttribute('state_id');
    }

    /**
     * Method get_state
     * Sample of usage: $municipio->state->attribute;
     * @returns State instance
     */
    public function get_state()
    {
        $obj = new State($this->state_id);
        return $obj->name;
    }
}
