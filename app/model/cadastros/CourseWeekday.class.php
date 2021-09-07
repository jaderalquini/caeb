<?php
/**
 * CourseWeekday Active Record
 * @author  <your-name-here>
 */
class CourseWeekday extends TRecord
{
    const TABLENAME = 'course_weekday';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('course_id');
        parent::addAttribute('weekday_id');
    }
}