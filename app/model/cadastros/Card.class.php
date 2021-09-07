<?php
/**
 * Form Active Record
 * @author  <your-name-here>
 */
class Card extends TRecord
{
    const TABLENAME = 'card';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('description');
        parent::addAttribute('content');
    }
}
?>