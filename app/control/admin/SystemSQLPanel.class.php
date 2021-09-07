<?php
class SystemSQLPanel extends TPage
{
    private $form;
    private $container;
    
    public function __construct()
    {
        parent::__construct();
        parent::include_js('app/lib/include/application.js');
        
        $this->form = new BootstrapFormWrapper( new TQuickForm('sqlpanel') );
        $this->form->setFormTitle('SQL Panel');
        
        $list = scandir('app/config');
        $options = array();
        foreach ($list as $entry)
        {
            if (substr($entry, -4) == '.ini')
            {
                $options[ substr($entry,0,-4) ] = $entry;
            }
        }
        
        $database = new TCombo('database');
        $table = new TCombo('table');
        $select = new TText('select');
        
        $this->form->addQuickField( new TLabel(_t('Database')), $database );
        $this->form->addQuickField( new TLabel(_t('Table')), $table );
        $this->form->addQuickField( new TLabel('SELECT'), $select );
        
        $this->form->addQuickAction( _t('Generate'), new TAction(array($this, 'onGenerate')), 'fa:check-circle green');
        
        $database->addItems($options);
        $database->setChangeAction(new TAction(array($this, 'onDatabaseChange')));
        $table->setChangeAction(new TAction(array($this, 'onTableChange')));
        $select->addValidation( 'SELECT', new TRequiredValidator );
        $database->addValidation(_t('Database'), new TRequiredValidator);
        $table->addValidation(_t('Table'), new TRequiredValidator);
        $database->setSize('100%');
        $table->setSize('100%');
        $select->setSize('100%', 100);
        
        $this->container = new TVBox;
        $this->container->style = 'width: 100%';
        $this->container->add(new TXMLBreadCrumb('menu.xml','SystemProgramList'));
        $this->container->add(TPanelGroup::pack(_t('SQL Panel'), $this->form));
        
        parent::add($this->container);
    }
    
    /**
     * onDatabaseChange
     */
    public static function onDatabaseChange($param)
    {
        try
        {
            $tables = SystemDatabaseInformationService::getDatabaseTables( $param['database'] );
            if ($tables)
            {
                TCombo::reload('sqlpanel', 'table', $tables, true);
            }
            else
            {
                TCombo::clearField('sqlpanel', 'table');
            }
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
    
    /**
     * onTableChange
     */
    public static function onTableChange($param)
    {
        if (!empty($param['table']))
        {
            $table = $param['table'];
            $obj = new stdClass;
            $obj->select = "SELECT * FROM {$table} LIMIT 100";
            TForm::sendData('sqlpanel', $obj);
        }
    }
    
    /**
     * onGenerate
     */
    public function onGenerate($param)
    {
        try
        {
            self::onDatabaseChange($param);
            $obj = new stdClass;
            
            // keep table filled via javascript
            if (isset($param['table']))
            {
                $obj->table = $param['table'];
                TForm::sendData('sqlpanel', $obj);
            }
            
            $this->form->validate();
            $data = $this->form->getData();
            
            if (strtoupper(substr( $data->select, 0, 6)) !== 'SELECT')
            {
                throw new Exception(_t('Invalid command'));
            }
            // creates a DataGrid
            $datagrid = new BootstrapDatagridWrapper(new TDataGrid);
            $datagrid->datatable = 'true';
            
            $panel = new TPanelGroup( _t('Results') );
            $panel->add($datagrid);
            
            TTransaction::open( $data->database );
            $conn = TTransaction::get();
            $result = $conn->query( $data->select );
            $row = $result->fetch();
            
            $i = 0;
            if ($row)
            {
                foreach ($row as $key => $value)
                {
                    if (is_string($key))
                    {
                        $col = new TDataGridColumn($key, $key, 'left');
                        $datagrid->addColumn($col);
                    }
                }
                
                // create the datagrid model
                $datagrid->createModel();
                
                $datagrid->addItem( (object) $row );
                
                $i = 1;
                while ($row = $result->fetch() AND $i<= 1000)
                {
                    $datagrid->addItem( (object) $row );
                    $i ++;
                }
            }
            $panel->addFooter( _t('^1 records shown', "<b>{$i}</b>"));
            $this->container->add($panel);
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
}