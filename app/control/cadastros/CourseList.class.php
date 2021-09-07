<?php
/**
 * CoursesList Listing
 * @author  <your name here>
 */
class CourseList extends TStandardList
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
        
        parent::setDatabase(TSession::getValue('unitbank'));            // defines the database
        parent::setActiveRecord('Course');   // defines the active record
        parent::setDefaultOrder('name', 'asc');         // defines the default order
        // parent::setCriteria($criteria) // define a standard filter

        parent::addFilterField('id', 'like', 'id'); // filterField, operator, formField
        parent::addFilterField('name', 'like', 'name'); // filterField, operator, formField
        parent::addFilterField('description', 'like', 'description'); // filterField, operator, formField
        
        // creates the form
        $this->form = new TQuickForm('form_search_Course');
        $this->form->class = 'tform'; // change CSS class
        $this->form = new BootstrapFormWrapper($this->form);
        $this->form->style = 'display: table;width:100%'; // change style
        $this->form->setFormTitle('Course');        

        // create the form fields
        $id = new TEntry('id');
        $name = new TEntry('name');
        $description = new TEntry('description');
        $output_type  = new THidden('output_type');
        
        $output_type->setValue('pdf');

        // add the fields
        //$this->form->addQuickField(_t('Id'), $id,  100 );
        $this->form->addQuickField(_t('Name'), $name,  '100%' );
        //$this->form->addQuickField(_t('Description'), $description,  '100%' );
        $this->form->addQuickField('', $output_type);
        
        $name->placeholder = _t('Name');
        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('Courses_filter_data') );
        
        // add the search form actions
        $this->form->addQuickAction(_t('Find'), new TAction(array($this, 'onSearch')), 'fa:search');
        $this->form->addQuickAction(_t('Clear Filter'),  new TAction(array($this, 'onClear')), 'bs:ban-circle red');
        $this->form->addQuickAction(_t('New'),  new TAction(array('CourseForm', 'onEdit')), 'bs:plus-sign green');
        $this->form->addQuickAction(_t('Report'), new TAction(array($this, 'onGenerateReport')), 'fa:print');
        
        // creates a DataGrid
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', _t('Id'), 'left');
        $column_name = new TDataGridColumn('name', _t('Name'), 'left');
        $column_description = new TDataGridColumn('description', _t('Description'), 'left');
        $column_weekdays = new TDataGridColumn('weekdays', _t('Weekdays'), 'left');

        // add the columns to the DataGrid
        //$this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_name);
        $this->datagrid->addColumn($column_description);
        $this->datagrid->addColumn($column_weekdays);
        
        $column_weekdays->setTransformer( function($value, $object, $row) {
            $weekdaynames = array();
            $repository = new TRepository('CourseWeekday');
            $criteria = new TCriteria;
            $criteria->add(new TFilter('course_id','=',$object->id));
            $objects = $repository->load($criteria);
            if ($objects)
            {
                foreach ($objects as $object)
                {
                    $weekday = new Weekday( $object->weekday_id );
                    $weekdaynames[] = $weekday->name;
                }
            }
            return implode(',', $weekdaynames);
        });
        
        // creates the datagrid column actions
        $order_id = new TAction(array($this, 'onReload'));
        $order_id->setParameter('order', 'id');
        $column_id->setAction($order_id);
        
        $order_name = new TAction(array($this, 'onReload'));
        $order_name->setParameter('order', 'name');
        $column_name->setAction($order_name);
        
        // create EDIT action
        $action_edit = new TDataGridAction(array('CourseForm', 'onEdit'));
        //$action_edit->setUseButton(TRUE);
        $action_edit->setButtonClass('btn btn-default');
        $action_edit->setLabel(_t('Edit'));
        $action_edit->setImage('fa:pencil-square-o blue fa-lg');
        $action_edit->setField('id');
        $this->datagrid->addAction($action_edit);
        
        // create DELETE action
        $action_del = new TDataGridAction(array($this, 'onDelete'));
        //$action_del->setUseButton(TRUE);
        $action_del->setButtonClass('btn btn-default');
        $action_del->setLabel(_t('Delete'));
        $action_del->setImage('fa:trash-o red fa-lg');
        $action_del->setField('id');
        $this->datagrid->addAction($action_del);
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // create the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());        

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add(TPanelGroup::pack(_t('Courses'), $this->form));
        $container->add($this->datagrid);
        $container->add($this->pageNavigation);
        
        parent::add($container);
    }   
    
    public function onGenerateReport($param)
    {        
        $data = $this->form->getData();
        
        try
        {
            // open a transaction with database 'samples'
            TTransaction::open(TSession::getValue('unitbank'));
            
            $formdata = $this->form->getData();
            
            $repository = new TRepository('Course');
            $criteria = new TCriteria;
            
            $filters=array();
            if ($formdata->name)
            {
                $criteria->add(new TFilter('name', 'like', '%'.$formdata->name.'%'));
                $filters[]=_t('Name').": ".$formdata->name;
            }
            
            $param['order'] = 'name';
            $param['direction'] = 'asc';
            $criteria->setProperties($param);
            
            $objects = $repository->load($criteria, FALSE);
            $format  = $formdata->output_type;
            
            if ($objects)
            {
                $designer = new TReport();
                $designer->setTitle('Relatório de Terapias');
                $columns = array();
                $columns[0]['size'] = 250;
                $columns[0]['text'] = _t('Name');
                $columns[0]['align'] = 'L';
                $columns[1]['size'] = 290;
                $columns[1]['text'] = _t('Weekday');
                $columns[1]['align'] = 'L';
                $designer->setFilters($filters);
                $designer->setColumns($columns);
                $designer->AddPage('Portrait','A4');
                $designer->SetMargins(30,30,30);
                $designer->SetAutoPageBreak(true, 30);
                $designer->SetFont('Arial','',10);
                $designer->SetX(30);
                
                $designer->SetFillColor(220,220,220);
                $fill = FALSE;
                $designer->SetFont('Arial','',8);
                $i=0;
                foreach ($objects as $object)
                {
                    $i++;
                    $designer->SetTextColor(0,0,0);
                    $designer->Cell(250,15,utf8_decode($object->name),0,0,'L',$fill);
                    $weekdaynames = array();
                    $repository = new TRepository('CourseWeekday');
                    $criteria = new TCriteria;
                    $criteria->add(new TFilter('course_id','=',$object->id));
                    $courseweekdays = $repository->load($criteria);
                    if ($courseweekdays)
                    {
                        foreach ($courseweekdays as $courseweekday)
                        {
                            $weekday = new Weekday( $courseweekday->weekday_id );
                            $weekdaynames[] = $weekday->name;
                        }
                    }
                    $designer->Cell(290,15,utf8_decode(implode(',',$weekdaynames)),0,0,'L',$fill);
                    $designer->Ln();
                    $fill = !$fill;
                }
                
                $designer->SetTextColor(0,126,196);
                $designer->Cell(250,15,'Total de Cursos',0,0,'R',$fill);
                $designer->Cell(290,15,$i,0,0,'L',$fill);
                    
                if (!file_exists("app/output/TerapyReport.{$format}") OR is_writable("app/output/TerapyReport.{$format}"))
                {
                    $designer->save("app/output/TerapyReport.{$format}");
                }
                else
                {
                    throw new Exception(_t('Permission denied') . ': ' . "app/output/TerapyReport.{$format}");
                }
                    
                parent::openFile("app/output/TerapyReport.{$format}"); 
            }
            else
            {
                new TMessage('error', _t('No records found'));
            }
            
            // close the transaction
            TTransaction::close();
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', '<b>Error</b> ' . $e->getMessage());
            
            // undo all pending operations
            TTransaction::rollback();
        }
        
        $this->form->setData($data);
    } 
}