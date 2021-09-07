<?php
/**
 * CardList Listing
 * @author  <your name here>
 */
class CardList extends TStandardList
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
        parent::setActiveRecord('Card');   // defines the active record
        parent::setDefaultOrder('id', 'asc');         // defines the default order
        // parent::setCriteria($criteria) // define a standard filter

        parent::addFilterField('id', 'like', 'id'); // filterField, operator, formField
        parent::addFilterField('description', 'like', 'description'); // filterField, operator, formField
        parent::addFilterField('content', 'like', 'content'); // filterField, operator, formField
        
        // creates the form
        $this->form = new TQuickForm('form_search_Card');
        $this->form->class = 'tform'; // change CSS class
        $this->form = new BootstrapFormWrapper($this->form);
        $this->form->style = 'display: table;width:100%'; // change style
        $this->form->setFormTitle('Card');        

        // create the form fields
        $id = new TEntry('id');
        $description = new TEntry('description');
        $content = new TEntry('content');

        // add the fields
       // $this->form->addQuickField('Código', $id,  100 );
        $this->form->addQuickField('Descrição', $description,  '100%' );
        //$this->form->addQuickField('Conteúdo', $content,  100 );
        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('Card_filter_data') );
        
        // add the search form actions
        $this->form->addQuickAction(_t('Find'), new TAction(array($this, 'onSearch')), 'fa:search');
        $this->form->addQuickAction(_t('Clear Filter'),  new TAction(array($this, 'onClear')), 'bs:ban-circle red');
        $this->form->addQuickAction(_t('New'),  new TAction(array('CardForm', 'onEdit')), 'bs:plus-sign green');
        
        // creates a DataGrid
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Código', 'right');
        $column_description = new TDataGridColumn('description', 'Descrição', 'left');
        $column_content = new TDataGridColumn('content', 'Conteúdo', 'left');

        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_description);
        $this->datagrid->addColumn($column_content);

        // creates the datagrid column actions
        $order_id = new TAction(array($this, 'onReload'));
        $order_id->setParameter('order', 'id');
        $column_id->setAction($order_id);
        
        $order_description = new TAction(array($this, 'onReload'));
        $order_description->setParameter('order', 'description');
        $column_description->setAction($order_description);
        
        $order_content = new TAction(array($this, 'onReload'));
        $order_content->setParameter('order', 'content');
        $column_content->setAction($order_content);        
        
        // create EDIT action
        $action_edit = new TDataGridAction(array('CardForm', 'onEdit'));
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
        $container->add(TPanelGroup::pack(_t('Cards'), $this->form));
        $container->add($this->datagrid);
        $container->add($this->pageNavigation);
        
        parent::add($container);
    }    
}
?>