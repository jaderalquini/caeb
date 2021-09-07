<?php
/**
 * Courses Active Record
 * @author  <your-name-here>
 */
class Course extends TRecord
{
    const TABLENAME = 'course';
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
    }
    
    public function addWeekdays(Weekday $weekday)
    {
        $object = new CourseWeekday;
        $object->course_id = $this->id;
        $object->weekday_id = $weekday->id;
        $object->store();
    }
    
    public function getCourseWeekdays()
    {
        $course_weekdays = array();
        
        // load the related System_user_group objects
        $repository = new TRepository('CourseWeekday');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('course_id', '=', $this->id));
        $course_course_weekdays = $repository->load($criteria);
        if ($course_course_weekdays)
        {
            foreach ($course_course_weekdays as $course_course_weekday)
            {
                $course_weekdays[] = new Weekday( $course_course_weekday->weekday_id );
            }
        }
        return $course_weekdays;
    }
    
    public function getCourseWeekdaysNames()
    {
        $weekdaynames = array();
        $weekdays = $this->getCourseWeekdays();
        if ($weekdays)
        {
            foreach ($weekdays as $weekday)
            {
                $weekdaysnames[] = $weekday->name;
            }
        }
        
        return implode(',', $weekdaynames);
    }
    
    public function delete($id = NULL)
    {
        $id = isset($id) ? $id : $this->id;
        $repository = new TRepository('CourseWeekday');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('course_id', '=', $id));
        $repository->delete($criteria);
        
        parent::delete($id);
    }
}