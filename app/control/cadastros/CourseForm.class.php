<?php
/**
 * CoursesForm Registration
 * @author  <your name here>
 */
class CourseForm extends TPage
{
    protected $form; // form
    
    use Adianti\Base\AdiantiStandardFormTrait; // Standard form methods
    
    /**
     * Class constructor
     * Creates the page and the registration form
     */
    function __construct()
    {
        parent::__construct();
        
        $this->setDatabase(TSession::getValue('unitbank'));              // defines the database
        $this->setActiveRecord('Course');     // defines the active record
        
        // creates the form
        $this->form = new TQuickForm('form_Course');
        $this->form->class = 'tform'; // change CSS class
        $this->form = new BootstrapFormWrapper($this->form);
        $this->form->style = 'display: table;width:100%'; // change style
        
        // define the form title
        $this->form->setFormTitle('Course');
        
        // create the form fields
        $id = new THidden('id');
        $name = new TEntry('name');
        $description = new TText('description');
        $weekdays = new TDBCheckGroup('weekdays',TSession::getValue('unitbank'),'Weekday','id','name');
        
        $weekdays->setLayout('horizontal');
        foreach ($weekdays->getLabels() as $label)
        {
            $label->setSize(75);
        }
        
        $name->addValidation(_t('Name'), new TRequiredValidator);

        // add the fields
        $this->form->addQuickField(_t('Name'), $name,  '100%' );
        $this->form->addQuickField(_t('Description'), $description );
        $this->form->addQuickField(_t('Weekdays'), $weekdays, '100%' );
        $this->form->addQuickField('', $id );
        
        $description->setSize('100%', 100);
        
        $id->placeholder = _t('Id');
        $name->placeholder = _t('Name');
        $description->placeholder = _t('Description');
        
        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
         
        // create the form actions
        $this->form->addQuickAction(_t('Save'), new TAction(array($this, 'onSave')), 'fa:floppy-o');
        $this->form->addQuickAction(_t('New'),  new TAction(array($this, 'onEdit')), 'bs:plus-sign green');
        $this->form->addQuickAction( _t('Back to the listing'), new TAction(array('CourseList','onReload')),  'fa:table blue' );
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', 'CourseList'));
        $container->add(TPanelGroup::pack(_t('Courses'), $this->form));
        
        parent::add($container);
    }
    
    public static function onSave($param)
    {
        try
        {
            // open a transaction with database TSession::getValue('unitbank')
            TTransaction::open(TSession::getValue('unitbank'));
            
            $object = new Course;
            $object->fromArray( $param );
            
            $object->store();
            
            $repository = new TRepository('CourseWeekday');
            $criteria = new TCriteria;
            $criteria->add(new TFilter('course_id', '=', $object->id));
            $repository->delete($criteria);
            
            if( !empty($param['weekdays']) )
            {
                foreach( $param['weekdays'] as $weekday_id )
                {
                    $object->addWeekdays(new Weekday($weekday_id));
                }
            }
            
            TForm::sendData('form_Course', $data);
            
            // close the transaction
            TTransaction::close();
            
            // shows the success message
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', $e->getMessage());
            
            // undo all pending operations
            TTransaction::rollback();
        }
    }
    
    function onEdit($param)
    {
        try
        {
            if (isset($param['key']))
            {
                // get the parameter $key
                $key=$param['key'];
                
                // open a transaction with database 'permission'
                TTransaction::open(TSession::getValue('unitbank'));
                
                // instantiates object System_user
                $object = new Course($key);
                
                $weekdays = array();
                
                if ($weekdays_db = $object->getCourseWeekdays() )
                {
                    foreach ($weekdays_db as $wkd)
                    {
                        $weekdays[] = $wkd->id;
                    }
                }
                
                $data = array();
                
                $object->weekdays = $weekdays;
                
                // fill the form with the active record data
                $this->form->setData($object);
                
                // close the transaction
                TTransaction::close();
            }
            else
            {
                $this->form->clear();
            }
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', $e->getMessage());
            
            // undo all pending operations
            TTransaction::rollback();
        }
    }
}