<?php
/**
 * CityList Listing
 * @author  <your name here>
 */
class CityList extends TStandardList
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
        
        parent::setDatabase('zip');            // defines the database
        parent::setActiveRecord('City');   // defines the active record
        parent::setDefaultOrder('id', 'asc');         // defines the default order
        // parent::setCriteria($criteria) // define a standard filter

        parent::addFilterField('id', 'like', 'id'); // filterField, operator, formField
        parent::addFilterField('name', 'like', 'name'); // filterField, operator, formField
        parent::addFilterField('state_id', 'like', 'state_id'); // filterField, operator, formField
        
        // creates the form
        $this->form = new TQuickForm('form_search_City');
        $this->form->class = 'tform'; // change CSS class
        $this->form = new BootstrapFormWrapper($this->form);
        $this->form->style = 'display: table;width:100%'; // change style
        $this->form->setFormTitle('City'); 
        $this->form->setFieldsByRow(2);       

        // create the form fields
        $id = new TEntry('id');
        $name = new TEntry('name');
        $state_id = new TDBCombo('state_id','zip','State','id','name');

        // add the fields
        //$this->form->addQuickField(_t('Id'), $id,  100 );
        $this->form->addQuickField(_t('Name'), $name,  '100%' );
        $this->form->addQuickField(_t('State'), $state_id,  200 );
        
        $id->placeholder = _t('Id');
        $name->placeholder = _t('Name');
        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('City_filter_data') );
        
        // add the search form actions
        $this->form->addQuickAction(_t('Find'), new TAction(array($this, 'onSearch')), 'fa:search');
        $this->form->addQuickAction(_t('Clear Filter'),  new TAction(array($this, 'onClear')), 'bs:ban-circle red');
        $this->form->addQuickAction(_t('New'),  new TAction(array('CityForm', 'onEdit')), 'bs:plus-sign green');
        
        // creates a DataGrid
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', _t('Id'), 'left');
        $column_name = new TDataGridColumn('name', _t('Name'), 'left');
        $column_state_id = new TDataGridColumn('state_id', _t('State'), 'left');

        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_name);
        $this->datagrid->addColumn($column_state_id);
        
        // creates the datagrid column actions
        $order_id = new TAction(array($this, 'onReload'));
        $order_id->setParameter('order', 'id');
        $column_id->setAction($order_id);
        
        $order_name = new TAction(array($this, 'onReload'));
        $order_name->setParameter('order', 'name');
        $column_name->setAction($order_name);
        
        $order_state_id = new TAction(array($this, 'onReload'));
        $order_state_id->setParameter('order','state_id');
        $column_state_id->setAction($order_state_id);
        
        // create EDIT action
        $action_edit = new TDataGridAction(array('CityForm', 'onEdit'));
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
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add(TPanelGroup::pack(_t('Cities'), $this->form));
        $container->add($this->datagrid);
        $container->add($this->pageNavigation);
        
        parent::add($container);
    }    
}