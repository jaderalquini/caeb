<?php
/**
 * Person Active Record
 * @author  <your-name-here>
 */
class Person extends TRecord
{
    const TABLENAME = 'person';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('name');
        parent::addAttribute('rg');
        parent::addAttribute('cpf');
        parent::addAttribute('birthdate');
        parent::addAttribute('maritalstatus');
        parent::addAttribute('address');
        parent::addAttribute('neighborhood');
        parent::addAttribute('zip');
        parent::addAttribute('city');
        parent::addAttribute('state_id');
        parent::addAttribute('phone');
        parent::addAttribute('celphone');
        parent::addAttribute('email');
        parent::addAttribute('assignmenter');
        parent::addAttribute('status');
        parent::addAttribute('registerdate');
    }

    public function addPersonTerapyWeekday(Terapy $terapy, Weekday $weekday)
    {
        $object = new PersonTerapyWeekday;
        $object->person_id = $this->id;
        $object->terapy_id = $terapy->id;
        $object->weekday_id = $weekday->id;
        $object->store();
    }
    
    public function addPersonCourseWeekday(Course $course, Weekday $weekday)
    {
        $object = new PersonCourseWeekday;
        $object->person_id = $this->id;
        $object->course_id = $course->id;
        $object->weekday_id = $weekday->id;
        $object->store();
    }
    
    public function getPersonTerapiesWeekdays()
    {
        $person_terapies_weekdays = array();
        
        $repository = new TRepository('PersonTerapyWeekday');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('person_id', '=', $this->id));
        $person_person_terapies_weekdays = $repository->load($criteria);
        if ($person_person_terapies_weekdays)
        {
            foreach ($person_person_terapies_weekdays as $person_person_terapy_weekday)
            {
                $person_terapies_weekdays[$person_person_terapy_weekday->terapy_id] = $person_person_terapy_weekday->weekday_id;
            }
        }
        
        return $person_terapies_weekdays;
    }
    
    public function getPersonCoursesWeekdays()
    {
        $person_courses_weekdays = array();
        
        $repository = new TRepository('PersonCourseWeekday');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('person_id', '=', $this->id));
        $person_person_courses_weekdays = $repository->load($criteria);
        if ($person_person_courses_weekdays)
        {
            foreach ($person_person_courses_weekdays as $person_person_course_weekday)
            {
                $person_courses_weekdays[$person_person_course_weekday->course_id] = $person_person_course_weekday->weekday_id;
            }
        }
        
        return $person_courses_weekdays;
    }
    
    public function getPersonSchedulings()
    {
    	$person_schedulings = array();
    		
    	$repository = new TRepository('CalendarEvent');
    	$criteria = new TCriteria;
    	$criteria->add(new TFilter('person_id', '=', $this->id));
    	$person_person_schedulings = $repository->load($criteria);
    	if ($person_person_schedulings)
    	{
    	    $i=0;
    	    foreach ($person_person_schedulings as $person_person_scheduling)
    	    {
    	        $i++;
    	        $person_schedulings[$i] = new CalendarEvent($person_person_scheduling->id);
    	    }
    	}
    	
    	return $person_schedulings;
    }
    
    public function clearParts()
    {
        // delete the related System_userSystem_user_group objects
        $criteria = new TCriteria;
        $criteria->add(new TFilter('person_id', '=', $this->id));
        
        $repository = new TRepository('PersonTerapyWeekday');                
        $repository->delete($criteria);
        
        $repository = new TRepository('PersonCourseWeekday');
        $repository->delete($criteria);   
    }
}