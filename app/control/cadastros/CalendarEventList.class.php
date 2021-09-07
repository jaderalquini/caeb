<?php
/**
 * CalendarEventList Listing
 * @author  <your name here>
 */
class CalendarEventList extends TStandardList
{
    protected $form;     // registration form
    protected $datagrid; // listing
    protected $pageNavigation;
    protected $formgrid;
    protected $deleteButton;
    protected $transformCallback;
    
    /**
     * Page constructor
     */
    public function __construct()
    {
        parent::__construct();
        
        parent::setDatabase('caeb');            // defines the database
        parent::setActiveRecord('CalendarEvent');   // defines the active record
        parent::setDefaultOrder('id', 'asc');         // defines the default order
        // parent::setCriteria($criteria) // define a standard filter

        parent::addFilterField('id', 'like', 'id'); // filterField, operator, formField
        parent::addFilterField('start_time', 'like', 'start_time'); // filterField, operator, formField
        parent::addFilterField('end_time', 'like', 'end_time'); // filterField, operator, formField
        parent::addFilterField('title', 'like', 'title'); // filterField, operator, formField
        parent::addFilterField('description', 'like', 'description'); // filterField, operator, formField
        parent::addFilterField('color', 'like', 'color'); // filterField, operator, formField
        parent::addFilterField('person_id', 'like', 'person_id'); // filterField, operator, formField
        parent::addFilterField('terapy_id', 'like', 'terapy_id'); // filterField, operator, formField
        parent::addFilterField('showedup', 'like', 'showedup'); // filterField, operator, formField
        parent::addFilterField('servicetab_front', 'like', 'servicetab_front'); // filterField, operator, formField
        parent::addFilterField('servicetab_back', 'like', 'servicetab_back'); // filterField, operator, formField
        
        // creates the form
        $this->form = new TQuickForm('form_search_CalendarEvent');
        $this->form->class = 'tform'; // change CSS class
        $this->form = new BootstrapFormWrapper($this->form);
        $this->form->style = 'display: table;width:100%'; // change style
        $this->form->setFormTitle('CalendarEvent');        

        // create the form fields
        /*$id = new TEntry('id');
        $start_time = new TEntry('start_time');
        $end_time = new TEntry('end_time');
        $title = new TEntry('title');
        $description = new TEntry('description');
        $color = new TEntry('color');
        $person_id = new TEntry('person_id');
        $terapy_id = new TEntry('terapy_id');
        $showedup = new TEntry('showedup');
        $servicetab_front = new TEntry('servicetab_front');
        $servicetab_back = new TEntry('servicetab_back');

        // add the fields
        $this->form->addQuickField('Id', $id,  200 );
        $this->form->addQuickField('Start Time', $start_time,  200 );
        $this->form->addQuickField('End Time', $end_time,  200 );
        $this->form->addQuickField('Title', $title,  200 );
        $this->form->addQuickField('Description', $description,  200 );
        $this->form->addQuickField('Color', $color,  200 );
        $this->form->addQuickField('Person Id', $person_id,  200 );
        $this->form->addQuickField('Terapy Id', $terapy_id,  200 );
        $this->form->addQuickField('Showedup', $showedup,  200 );
        $this->form->addQuickField('Servicetab Front', $servicetab_front,  200 );
        $this->form->addQuickField('Servicetab Back', $servicetab_back,  200 );*/
        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('CalendarEvent_filter_data') );
        
        // add the search form actions
        /*$this->form->addQuickAction(_t('Find'), new TAction(array($this, 'onSearch')), 'fa:search');
        $this->form->addQuickAction(_t('New'),  new TAction(array('CalendarEventForm', 'onEdit')), 'bs:plus-sign green');*/
        
        // creates a DataGrid
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'left');
        $column_start_time = new TDataGridColumn('start_time', _t('Date'), 'left');
        $column_end_time = new TDataGridColumn('end_time', 'End Time', 'left');
        $column_title = new TDataGridColumn('title', 'Title', 'left');
        $column_description = new TDataGridColumn('description', _t('Observation'), 'left');
        $column_color = new TDataGridColumn('color', 'Color', 'left');
        $column_person_id = new TDataGridColumn('person', _t('Person'), 'left');
        $column_terapy_id = new TDataGridColumn('terapy', _t('Terapy'), 'left');
        $column_showedup = new TDataGridColumn('showedup', _t('Showedup'), 'center');
        $column_servicetab_front = new TDataGridColumn('servicetab_front', 'Servicetab Front', 'left');
        $column_servicetab_back = new TDataGridColumn('servicetab_back', 'Servicetab Back', 'left');

        // add the columns to the DataGrid
        //$this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_start_time);
        /*$this->datagrid->addColumn($column_end_time);
        $this->datagrid->addColumn($column_title);
        $this->datagrid->addColumn($column_description);        
        $this->datagrid->addColumn($column_color);*/
        $this->datagrid->addColumn($column_person_id);
        $this->datagrid->addColumn($column_terapy_id);
        $this->datagrid->addColumn($column_showedup);
        /*$this->datagrid->addColumn($column_servicetab_front);
        $this->datagrid->addColumn($column_servicetab_back);*/
        
        $column_start_time->setTransformer(function($value, $object, $row) {
            return TDate::date2br(substr($value, 0, 10));
        });
        
        $column_showedup->setTransformer( function($value, $object, $row) {
            if ($value=='N')
            {
                $class = 'danger';
                $label = _t('No');
            } else if ($value=='S') {
                $class = 'success';
                $label = _t('Yes');
            }
            $div = new TElement('span');
            $div->class="label label-{$class}";
            $div->style="text-shadow:none; font-size:12px; font-weight:lighter";
            $div->add($label);
            return $div;
        });
        
        // create EDIT action
        $action_edit = new TDataGridAction(array('CalendarEventForm', 'onEdit'));
        //$action_edit->setUseButton(TRUE);
        $action_edit->setButtonClass('btn btn-default');
        $action_edit->setLabel(_t('Edit'));
        $action_edit->setImage('fa:pencil-square-o blue fa-lg');
        $action_edit->setField('id');
        $this->datagrid->addAction($action_edit);
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // create the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());        

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', 'FullCalendarDatabaseView'));
        //$container->add(TPanelGroup::pack(_t('Schedulings'), $this->form));
        $container->add($this->datagrid);
        //$container->add($this->pageNavigation);
        
        parent::add($container);
    }
    
    public function onReload($param = NULL)
    {
        try
        {
            TTransaction::open(TSession::getValue('unitbank'));
            $conn = TTransaction::get();
            
            $query="select c.id id, start_time, p.name person , t.name terapy, showedup from calendar_event c 
                     left join person p on p.id = person_id 
                     left join terapy t on t.id = terapy_id";
                     
            $where=array();
            if ($param['start_date'] != '')
            {
                $where[]='date(start_time)>='.TDate::date2us($param['start_date']);
            }
            
            if ($param['end_date'] != '')
            {
                $where[]='date(end_time)<='.TDate::date2us($param['end_date']);
            }
            
            if ($param['person_id'] != '')
            {
                $where[]='person_id='.$param['person_id'];
            }
            
            if ($param['terapy_id'] != '')
            {
                $where[]='terapy_id='.$param['terapy_id'];
            }
            
            if (sizeof($where))
            {
                $query.=' where '.implode(' and ', $where);
            }
                                  
            $results = $conn->query($query);
            
            $this->datagrid->clear();
            if ($results)
            {
                foreach ($results as $result)
                {
                    $item = new StdClass;
                    $item->id = $result['id'];
                    $item->start_time = $result['start_time'];
                    $item->person = $result['person'];
                    $item->terapy = $result['terapy'];
                    $item->showedup = $result['showedup'];
                    $this->datagrid->addItem($item);
                }
            }
            
            // close the transaction
            TTransaction::close();
            $this->loaded = true;
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
