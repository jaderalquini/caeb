<?php
/**
 * Weekday Active Record
 * @author  <your-name-here>
 */
class Weekday extends TRecord
{
    const TABLENAME = 'weekday';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('name');
    }
}
