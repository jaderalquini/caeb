<?php
/**
 * CalendarEvent Active Record
 * @author  <your-name-here>
 */
class CalendarEvent extends TRecord
{
    const TABLENAME = 'calendar_event';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('start_time');
        parent::addAttribute('end_time');
        parent::addAttribute('color');
        parent::addAttribute('title');
        parent::addAttribute('description');
        parent::addAttribute('person_id');
        parent::addAttribute('person');
        parent::addAttribute('terapy_id');
        parent::addAttribute('terapy');
        parent::addAttribute('showedup');
        parent::addAttribute('servicetab_front');
        parent::addAttribute('servicetab_back');
    }
    
    public function get_person()
    {
        try
        {
            $obj = new Person($this->person_id);
            return $obj->name;   
        }  
        catch (Exception $e) // in case of exception
        {
            return NULL;    
        }        
    }
    
    public function get_terapy()
    {
        try
        {
            $obj = new Terapy($this->terapy_id);
            return $obj->name;   
        }  
        catch (Exception $e) // in case of exception
        {
            return NULL;    
        }        
    }
}