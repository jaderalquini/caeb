<?php
/**
 * Terapia Active Record
 * @author  <your-name-here>
 */
class Terapy extends TRecord
{
    const TABLENAME = 'terapy';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('name');
        parent::addAttribute('description');
        parent::addAttribute('color');
        parent::addAttribute('vacancies');
    }
    
    public function addWeekdays(Weekday $weekday)
    {
        $object = new TerapyWeekday;
        $object->terapy_id = $this->id;
        $object->weekday_id = $weekday->id;
        $object->store();
    }
    
    public function getTerapyWeekdays()
    {
        $terapy_weekdays = array();
        
        // load the related System_user_group objects
        $repository = new TRepository('TerapyWeekday');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('terapy_id', '=', $this->id));
        $terapy_terapy_weekdays = $repository->load($criteria);
        if ($terapy_terapy_weekdays)
        {
            foreach ($terapy_terapy_weekdays as $terapy_terapy_weekday)
            {
                $terapy_weekdays[] = new Weekday( $terapy_terapy_weekday->weekday_id );
            }
        }
        return $terapy_weekdays;
    }
    
    public function getTerapyWeekdaysNames()
    {
        $weekdaynames = array();
        $weekdays = $this->getTerapyWeekdays();
        if ($weekdays)
        {
            foreach ($weekdays as $weekday)
            {
                $weekdaysnames[] = $weekday->name;
            }
        }
        
        return implode(',', $weekdaynames);
    }
}