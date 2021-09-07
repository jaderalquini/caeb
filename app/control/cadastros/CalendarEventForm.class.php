<?php
/**
 * CalendarEventForm
 */
class CalendarEventForm extends TWindow
{
    protected $form; // form
    private $servicetab_frontview;
    private $servicetab_backview;
    
    /**
     * Class constructor
     * Creates the page and the registration form
     */
    public function __construct($param)
    {
        parent::__construct();
        parent::include_js('app/lib/include/application.js');
        parent::setSize(640, null);
        parent::setTitle(_t('Scheduling'));
        
        // creates the form
        $this->form = new TForm('form_event');
        $this->form->class = 'tform'; // CSS class
        $this->form->style = 'width: 600px';
        
        // add a table inside form
        $table = new TTable;
        $table-> width = '100%';
        $this->form->add($table);
        
        // add a row for the form title
        /*$row = $table->addRow();
        $row->class = 'tformtitle'; // CSS class
        $row->addCell( new TLabel('Event') )->colspan = 2;*/
        
        $hours = array();
        $minutes = array();
        for ($n=0; $n<24; $n++)
        {
            $hours[$n] = str_pad($n, 2, '0', STR_PAD_LEFT);
        }
        
        for ($n=0; $n<=55; $n+=5)
        {
            $minutes[$n] = str_pad($n, 2, '0', STR_PAD_LEFT);
        }
        
        TTransaction::open(TSession::getValue('unitbank'));
        $conn = TTransaction::get();
        
        $query = 'select distinct terapy_id from terapy_weekday where weekday_id = ' . date('N');
        $results = $conn->query($query);
        
        if ($results)
        {
            $terapies = array();
            foreach ($results as $result)
            {
                $terapy = new Terapy($result['terapy_id']);
                $terapies[$terapy->id] = $terapy->name;
            }
        }
        
        TTransaction::close();
        
        // create the form fields
        $view               = new THidden('view');
        $id                 = new THidden('id');
        $color              = new TColor('color');
        $start_date         = new TDate('start_date');
        $start_hour         = new TCombo('start_hour');
        $start_minute       = new TCombo('start_minute');
        $end_date           = new THidden('end_date');
        $end_hour           = new THidden('end_hour');
        $end_minute         = new THidden('end_minute');
        $title              = new TEntry('title');
        $description        = new TText('description');
        $person_id          = new TDBSeekButton('person_id',TSession::getValue('unitbank'),'form_event','Person','name','person_id','person');
        $person             = new TEntry('person');
        $terapy_id          = new TCombo('terapy_id');
        $showedup           = new TRadioGroup('showedup');
        $servicetab_front   = new TFile('servicetab_front');
        $servicetab_back    = new TFile('servicetab_back');
        
        $color->setValue('#3a87ad');        
        $start_date->setMask('dd/mm/yyyy');
        $showedup->addItems(array('S' => 'Sim', 'N' => 'Não'));
        $showedup->setLayout('horizontal');
        
        $start_hour->addItems($hours);
        $start_minute->addItems($minutes);
        //$end_hour->addItems($hours);
        //$end_minute->addItems($minutes);
        $terapy_id->addItems($terapies);
        
        $id->setEditable(FALSE);
        
        // define the sizes
        $id->setSize(100);
        $color->setSize(100);
        $start_date->setSize(100);
        $end_date->setSize(100);
        $start_hour->setSize(50);
        $end_hour->setSize(50);
        $start_minute->setSize(50);
        $end_minute->setSize(50);
        $title->setSize('100%');
        $description->setSize('100%', 100);
        $person_id->setSize(60);
        $person->setSize(300);
        $terapy_id->setSize('100%');
        $servicetab_front->setSize('100%');
        $servicetab_back->setSize('100%');

        $start_hour->setChangeAction(new TAction(array($this, 'onChangeStartHour')));
        //$end_hour->setChangeAction(new TAction(array($this, 'onChangeEndHour')));
        $start_date->setExitAction(new TAction(array($this, 'onChangeStartDate')));
        //$end_date->setExitAction(new TAction(array($this, 'onChangeEndDate')));
        $servicetab_front->setCompleteAction(new TAction(array($this, 'onCompleteServicetab_front')));
        $servicetab_back->setCompleteAction(new TAction(array($this, 'onCompleteServicetab_back')));
        
        $person_id->addValidation(_t('Person'), new TRequiredValidator);
        $terapy_id->addValidation(_t('Terapy'), new TRequiredValidator);

        // add one row for each form field
        $table->addRowSet( $view );
        //$table->addRowSet( new TLabel(_t('Color') . ':'), $color );
        //$table->addRowSet( new TLabel(_t('Date') . ':'), $start_date );
        $table->addRowSet( $l1 = new TLabel(_T('Date') . ':'), array($start_date, $start_hour, $start_minute) );
        //$table->addRowSet( new TLabel('End time:'), array($end_date, $end_hour, $end_minute));
        //$table->addRowSet( new TLabel(_t('Title') . ':'), $title );
        $table->addRowSet( $l2 = new TLabel(_t('Person') . ':'), array($person_id, $person) );
        $table->addRowSet( $l3 = new TLabel(_t('Terapy') . ':'), $terapy_id );
        $table->addRowSet( $l4 = new TLabel(_t('Observation') . ':'), $description );
        //$table->addRowSet( new TLabel('ID:'), $id );
        
        $l1->setFontStyle('bold');
        $l2->setFontStyle('bold');
        $l3->setFontStyle('bold');
        $l4->setFontStyle('bold');
        
        // create an action button (save)
        $save_button=new TButton('save');
        $save_button->setAction(new TAction(array($this, 'onSave')), _t('Save'));
        $save_button->setImage('fa:save green');

 
        // create an new button (edit with no parameters)
        $new_button=new TButton('new');
        $new_button->setAction(new TAction(array($this, 'onEdit')), _t('Clear'));
        $new_button->setImage('fa:eraser orange');

        // create an del button (edit with no parameters)
        $del_button=new TButton('del');
        $del_button->setAction(new TAction(array($this, 'onDelete')), _t('Delete'));
        $del_button->setImage('fa:trash-o red');
        
        $this->form->setFields(array($id, $view, $color, $title, $description, $start_date, $start_hour, $start_minute, 
                                $end_date, $end_hour, $end_minute, $person_id, $person, $terapy_id, $save_button, $new_button,$del_button));
                                
        if (isset($param['key']))
        {
            $this->servicetab_frontview = new TElement('div');
            $this->servicetab_frontview->id = 'servicetab_frontview';
            $this->servicetab_frontview->style = 'width:100%;height:auto;min-height:200px;border:1px solid gray;padding:4px;';
            
            $this->servicetab_backview = new TElement('div');
            $this->servicetab_backview->id = 'servicetab_backview';
            $this->servicetab_backview->style = 'width:100%;height:auto;min-height:200px;border:1px solid gray;padding:4px;';
            
            $table->addRowSet( $l5 = new TLabel( _t('Showedup') . ':'), $showedup);
            /*$table->addRowSet( $l6 = new TLabel( _t('Servicetab (Front)') . ':'), $servicetab_front);
            $table->addRowSet( new TLabel(''), $this->servicetab_frontview);
            $table->addRowSet( $l7 = new TLabel( _t('Servicetab (Back)') . ':'), $servicetab_back);
            $table->addRowSet( new TLabel(''), $this->servicetab_backview);*/
            
            $l5->setFontStyle('bold');
            /*$l6->setFontStyle('bold');
            $l7->setFontStyle('bold');*/
            
            $this->form->addField($showedup);
            /*$this->form->addField($servicetab_front);
            $this->form->addField($servicetab_back);*/
        }
        
        $table->addRowSet( new TLabel(''), array($id, $end_date, $end_hour, $end_minute) );
        
        $buttons_box = new THBox;
        $buttons_box->add($save_button);
        //$buttons_box->add($new_button);
        $buttons_box->add($del_button);
        
        // add a row for the form action
        $row = $table->addRow();
        $row->class = 'tformaction'; // CSS class
        $row->addCell($buttons_box)->colspan = 2;
        
        parent::add($this->form);
    }

    /**
     * Executed when user leaves start hour field
     */
    public static function onChangeStartHour($param=NULL)
    {
        $obj = new stdClass;
        if (empty($param['start_minute']))
        {
            $obj->start_minute = '0';
            TForm::sendData('form_event', $obj);
        }
        
        if (empty($param['end_hour']) AND empty($param['end_minute']))
        {
            $obj->end_hour = $param['start_hour'] +1;
            $obj->end_minute = '0';
            TForm::sendData('form_event', $obj);
        }
    }
    
    /**
     * Executed when user leaves end hour field
     */
    public static function onChangeEndHour($param=NULL)
    {
        if (empty($param['end_minute']))
        {
            $obj = new stdClass;
            $obj->end_minute = '0';
            TForm::sendData('form_event', $obj);
        }
    }
    
    /**
     * Executed when user leaves start date field
     */
    public static function onChangeStartDate($param=NULL)
    {
        $date = TDate::date2us($param['start_date']);
        $weekday = date('N', strtotime($date));
        
        if (empty($param['end_date']) AND !empty($param['start_date']))
        {
            $obj = new stdClass;
            $obj->end_date = $param['start_date'];
            TForm::sendData('form_event', $obj);
        }
        
        if (!empty($param['start_date']))
        {
            TTransaction::open(TSession::getValue('unitbank'));
            $conn = TTransaction::get();
            
            $query = 'select distinct terapy_id from terapy_weekday where weekday_id = ' . $weekday;
            $results = $conn->query($query);
            
            if ($results)
            {
                $terapies = array();
                $terapies[] = '';
                foreach ($results as $result)
                {
                    $terapy = new Terapy($result['terapy_id']);
                    $terapies[$terapy->id] = $terapy->name;
                }
            }
            
            TTransaction::close();
            
            TCombo::reload('form_event', 'terapy_id', $terapies);
        }
    }
    
    /**
     * Executed when user leaves end date field
     */
    public static function onChangeEndDate($param=NULL)
    {
        if (empty($param['end_hour']) AND empty($param['end_minute']) AND !empty($param['start_hour']))
        {
            $obj = new stdClass;
            $obj->end_hour = min($param['start_hour'],22) +1;
            $obj->end_minute = '0';
            TForm::sendData('form_event', $obj);
        }
    }
    
    /**
     * method onSave()
     * Executed whenever the user clicks at the save button
     */
    public function onSave($param)
    {  
    	try
        {
            // open a transaction with database TSession::getValue('unitbank')
            TTransaction::open(TSession::getValue('unitbank'));
            
            $this->form->validate(); // form validation
            
            // get the form data into an active record Entry
            $data = $this->form->getData();
            
            if ($param['id']=='')
            {
            	    self::Validate($data);
            }
            
            $object = new CalendarEvent;
            $terapy = new Terapy($data->terapy_id);
            $object->color = $terapy->color;
            $object->id = $data->id;
            $object->title = $data->person . ' <br>('.$terapy->name.')';
            $object->description = $data->description;
            $object->start_time = TDate::date2us($data->start_date) . ' ' . str_pad($data->start_hour, 2, '0', STR_PAD_LEFT) . ':' . str_pad($data->start_minute, 2, '0', STR_PAD_LEFT) . ':00';
            $object->end_time = $data->end_date . ' ' . str_pad($data->end_hour, 2, '0', STR_PAD_LEFT) . ':' . str_pad($data->end_minute, 2, '0', STR_PAD_LEFT) . ':00';
            $object->person_id = $data->person_id;
            $object->terapy_id = $data->terapy_id;
            
            $object->store(); // stores the object
            
            $data->id = $object->id;
            $this->form->setData($data); // keep form data
            
            TTransaction::close(); // close the transaction
            $posAction = new TAction(array('FullCalendarDatabaseView', 'onReload'));
            $posAction->setParameter('view', $data->view);
            $posAction->setParameter('date', TDate::date2us($data->start_date));
            
            // shows the success message
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'), $posAction);
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', $e->getMessage());
            
            $this->form->setData( $this->form->getData() ); // keep form data
            
            // undo all pending operations
            TTransaction::rollback();
        }
    }
    
    /**
     * method onEdit()
     * Executed whenever the user clicks at the edit button da datagrid
     */
    public function onEdit($param)
    {
        try
        {
            if (isset($param['key']))
            {
                // get the parameter $key
                $key=$param['key'];
                
                // open a transaction with database TSession::getValue('unitbank')
                TTransaction::open(TSession::getValue('unitbank'));
                
                // instantiates object CalendarEvent
                $object = new CalendarEvent($key);
                
                $person = new Person($object->person_id);
                
                $data = new stdClass;
                $data->id = $object->id;
                $data->color = $object->color;
                $data->title = $object->title;
                $data->description = $object->description;
                $data->person_id = $object->person_id;
                $data->person = $person->name;
                $data->terapy_id = $object->terapy_id;
                $data->start_date = TDate::date2br(substr($object->start_time,0,10));
                $data->start_hour = substr($object->start_time,11,2);
                $data->start_minute = substr($object->start_time,14,2);
                $data->end_date = substr($object->end_time,0,10);
                $data->end_hour = substr($object->end_time,11,2);
                $data->end_minute = substr($object->end_time,14,2);
                $data->view = $param['view'];
                
                // fill the form with the active record data
                $this->form->setData($data);
                
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
    
    /**
     * Delete event
     */
    public static function onDelete($param)
    {
        // define the delete action
        $action = new TAction(array('CalendarEventForm', 'Delete'));
        $action->setParameters($param); // pass the key parameter ahead
        
        // shows a dialog to the user
        new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
    }
    
    /**
     * Delete a record
     */
    public static function Delete($param)
    {
        try
        {
            // get the parameter $key
            $key = $param['id'];
            // open a transaction with database
            TTransaction::open(TSession::getValue('unitbank'));
            
            // instantiates object
            $object = new CalendarEvent($key, FALSE);
            
            // deletes the object from the database
            $object->delete();
            
            // close the transaction
            TTransaction::close();
            
            $posAction = new TAction(array('FullCalendarDatabaseView', 'onReload'));
            
            // shows the success message
            new TMessage('info', AdiantiCoreTranslator::translate('Record deleted'), $posAction);
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', $e->getMessage());
            // undo all pending operations
            TTransaction::rollback();
        }
    }
    
    /**
     * Fill form from the user selected time
     */
    public function onStartEdit($param)
    {
        $this->form->clear();
        $data = new stdClass;
        $data->view = $param['view']; // calendar view
        $data->color = '#3a87ad';
        
        if ($param['date'])
        {
            if (strlen($param['date']) == 10)
            {
                $data->start_date = TDate::date2br($param['date']);
                $data->end_date = TDate::date2br($param['date']);
            }
            if (strlen($param['date']) == 19)
            {
                $data->start_date   = TDate::date2br(substr($param['date'],0,10));
                $data->start_hour   = substr($param['date'],11,2);
                $data->start_minute = substr($param['date'],14,2);
                
                $data->end_date   = substr($param['date'],0,10);
                $data->end_hour   = substr($param['date'],11,2) +1;
                $data->end_minute = substr($param['date'],14,2);
            }
            $this->form->setData( $data );
        }
    }
    
    /**
     * Update event. Result of the drag and drop or resize.
     */
    public static function onUpdateEvent($param)
    {
        try
        {
            if (isset($param['id']))
            {
                // get the parameter $key
                $key=$param['id'];
                
                // open a transaction with database TSession::getValue('unitbank')
                TTransaction::open(TSession::getValue('unitbank'));
                
                // instantiates object CalendarEvent
                $object = new CalendarEvent($key);
                $object->start_time = str_replace('T', ' ', $param['start_time']);
                $object->end_time   = str_replace('T', ' ', $param['end_time']);
                $object->store();
                                
                // close the transaction
                TTransaction::close();
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', '<b>Error</b> ' . $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    public static function onCompleteServicetab_front($param)
    {
        TScript::create("$('#servicetab_frontview').html('')");
        TScript::create("$('#servicetab_frontview').append(\"<img style='width:100%' src='tmp/{$param['servicetab_front']}'>\");");
    }
    
    public static function onCompleteServicetab_back($param)
    {
        TScript::create("$('#servicetab_backview').html('')");
        TScript::create("$('#servicetab_backview').append(\"<img style='width:100%' src='tmp/{$param['servicetab_back']}'>\");");
    }
    
    public function Validate($param)
    {
        $conn = TTransaction::get();
        
        $terapy = new Terapy($param->terapy_id);
        
        $query="select count(*) as TOTAL from calendar_event where date(start_time)='".TDate::date2us($param->start_date)."' and terapy_id=".$param->terapy_id;
        $results = $conn->query($query);
        if ($results)
        {
            foreach ($results as $result)
            {
                if ($result['TOTAL'] >= $terapy->vacancies)
                {
                    throw new Exception('Atingido limite de vagas detsa terapia neste dia.');
                }
            }
        }
        
        $query="select person_id from calendar_event where date(start_time)='".TDate::date2us($param->start_date)."' and person_id=".$param->person_id;
        $results = $conn->query($query);
        if ($results)
        {
            foreach ($results as $result)
            {                
                throw new Exception('Ja existe um agendamento para este paciente neste dia.');
            }
        }   			
    }
}