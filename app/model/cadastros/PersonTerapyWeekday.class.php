<?php
/**
 * PersonTerapyWeekday Active Record
 * @author  <your-name-here>
 */
class PersonTerapyWeekday extends TRecord
{
    const TABLENAME = 'person_terapy_weekday';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('person_id');
        parent::addAttribute('terapy_id');
        parent::addAttribute('weekday_id');
        parent::addAttribute('person');
        parent::addAttribute('terapy');
        parent::addAttribute('weekday');
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
    
    public function get_weekday()
    {
        try
        {
            $obj = new Weekday($this->weekday_id);
            return $obj->name;   
        }  
        catch (Exception $e) // in case of exception
        {
            return NULL;    
        }        
    }
}