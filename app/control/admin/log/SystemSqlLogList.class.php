<?php
/**
 * SystemSqlLogList Listing
 * @author  <your name here>
 */
class SystemSqlLogList extends TStandardList
{
    protected $form;     // registration form
    protected $datagrid; // listing
    protected $pageNavigation;
    
    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct()
    {
        parent::__construct();
        parent::include_js('app/lib/include/application.js');
        
        parent::setDatabase('log');            // defines the database
        parent::setActiveRecord('SystemSqlLog');   // defines the active record
        parent::setDefaultOrder('id', 'asc');         // defines the default order
        parent::addFilterField('login', 'like'); // add a filter field
        parent::addFilterField('database_name', 'like'); // add a filter field
        parent::addFilterField('sql_command', 'like'); // add a filter field
        parent::setLimit(20);
        
        // creates the form, with a table inside
        $this->form = new BootstrapFormWrapper( new TQuickForm('form_search_SystemSqlLog') );
        $this->form->setFormTitle('SQL Log');
        $this->form->setFieldsByRow(2);
        
        // create the form fields
        $login       = new TEntry('login');
        $database    = new TEntry('database_name');
        $sql         = new TEntry('sql_command');


        // add the fields
        $this->form->addQuickField( new TLabel(_t('Login')), $login );
        $this->form->addQuickField( new TLabel(_t('Database')), $database );
        $this->form->addQuickField( new TLabel('SQL'), $sql );
        $login->placeholder = _t('Login');
        $database->placeholder = _t('Database');
        $sql->placeholder = 'SQL';

        $login->setSize('100%');
        $database->setSize('100%');
        $sql->setSize('100%');
        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('SystemSqlLog_filter_data') );
        
        // add the search form actions
        $this->form->addQuickAction(_t('Find'), new TAction(array($this, 'onSearch')), 'ico_find.png');
        
        // creates a DataGrid
        $this->datagrid = new BootstrapDatagridWrapper(new TQuickGrid);
        $this->datagrid->disableDefaultClick();
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        
        // creates the datagrid columns
        $id = $this->datagrid->addQuickColumn('ID', 'id', 'center', 50, new TAction(array($this, 'onReload')), array('order', 'id'));
        $logdate = $this->datagrid->addQuickColumn(_t('Date'), 'logdate', 'center', NULL, new TAction(array($this, 'onReload')), array('order', 'logdate'));
        $login = $this->datagrid->addQuickColumn(_t('Login'), 'login', 'center', NULL, new TAction(array($this, 'onReload')), array('order', 'login'));
        $database = $this->datagrid->addQuickColumn(_t('Database'), 'database_name', 'left', NULL, new TAction(array($this, 'onReload')), array('order', 'database'));
        $sql = $this->datagrid->addQuickColumn('SQL', 'sql_command', 'left', NULL);
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // create the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add(TPanelGroup::pack(_t('SQL Log'), $this->form));
        $container->add(TPanelGroup::pack('', $this->datagrid));
        $container->add($this->pageNavigation);
        
        parent::add($container);
    }
}
