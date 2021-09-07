<?php
/**
 * TerapyWeekday Active Record
 * @author  <your-name-here>
 */
class TerapyWeekday extends TRecord
{
    const TABLENAME = 'terapy_weekday';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('terapy_id');
        parent::addAttribute('weekday_id');
        parent::addAttribute('terapy');
        parent::addAttribute('weekday');
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