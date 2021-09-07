<?php
/**
 * SystemUnitList Listing
 * @author  <your name here>
 */
class SystemUnitList extends TStandardList
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
        parent::include_js('app/lib/include/application.js');
        
        parent::setDatabase('permission');            // defines the database
        parent::setActiveRecord('SystemUnit');   // defines the active record
        parent::setDefaultOrder('id', 'asc');         // defines the default order
        parent::addFilterField('id', '=', 'id'); // filterField, operator, formField
        parent::addFilterField('name', 'like', 'name'); // filterField, operator, formField
        
        // creates the form
        $this->form = new BootstrapFormWrapper( new TQuickForm('form_search_SystemUnit') );
        $this->form->setFormTitle(_t('Units'));
        $this->form->setFieldsByRow(2);
        
        // create the form fields
        $id = new TEntry('id');
        $name = new TEntry('name');
        $cnpj = new TEntry('cnpj');
        $ie = new TEntry('ie');
        $nrjucesc = new TEntry('nrjucesc');
        $creci = new TEntry('creci');
        $zip = new TEntry('zip');
        $uf_id = new TDBCombo('uf_id','zip', 'State', 'id', 'nome');
        $citty = new TEntry('citty');
        $neighborhood = new TEntry('neighborhood');
        $address = new TEntry('address');
        $phone = new TEntry('phone');
        $fax = new TEntry('fax');
        $site = new TEntry('site');
        $email = new TEntry('email');
        $responsible_name = new TEntry('responsible_name');
        $responsible_cpf = new TEntry('responsible_cpf');
        $logo = new TFile('logo');
        $bank = new TEntry('bank');
        
        // add the fields
        $this->form->addQuickField(_t('Id'), $id, 100 );
        $this->form->addQuickField(_t('Name'), $name, '100%' );
        $this->form->addQuickField('CNPJ', $cnpj );
        /*$this->form->addQuickField('IE', $ie );
        $this->form->addQuickField('Jucesc', $nrjucesc );
        $this->form->addQuickField('Creci', $creci );
        $this->form->addQuickField(_t('ZIP'), $zip, 100 );
        $this->form->addQuickField('UF', $uf_id );
        $this->form->addQuickField(_t('Citty'), $citty, '100%' );*/
        $this->form->addQuickField(_t('Address'), $address, '100%' );
        $this->form->addQuickField(_t('Neighborhood'), $neighborhood, '100%' );
        /*$this->form->addQuickField(_t('Phone'), $phone );
        $this->form->addQuickField('Fax', $fax );
        $this->form->addQuickField(_t('Site'), $site, '100%' );
        $this->form->addQuickField(_t('Email'), $email, '100%' );
        $this->form->addQuickField(_t('Responsible Name'), $responsible_name, '100%' );
        $this->form->addQuickField(_t('Responsible CPF'), $responsible_cpf );
        $this->form->addQuickField(_t('Logo'), $logo, '100%' );
        $this->form->addQuickField(_t('Bank'), $bank );*/
        $id->placeholder = _t('Id');
        $name->placeholder = _t('Name');
        $cnpj->placeholder = 'CNPJ';
        $ie->placeholder = 'IE';
        $zip->placeholder = _t('ZIP');
        $creci->placeholder = 'Creci';
        $nrjucesc->placeholder = 'Jucesc';
        $citty->placeholder = _t('Citty');
        $neighborhood->placeholder = _t('Neighborhood');
        $address->placeholder = _t('Address');
        $phone->placeholder = _t('Phone');
        $fax->placeholder = 'Fax';
        $site->placeholder = _t('Site');
        $email->placeholder = _t('Email');
        $responsible_name->placeholder = _t('Responsible Name');
        $responsible_cpf->placeholder = _t('Responsible CPF');
        $bank->placeholder = _t('Bank');
        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('SystemUnit_filter_data') );
        
        // add the search form actions
        $this->form->addQuickAction(_t('Find'), new TAction(array($this, 'onSearch')), 'fa:search');
        $this->form->addQuickAction(_t('Clear Filter'),  new TAction(array($this, 'onClear')), 'bs:ban-circle red');
        $this->form->addQuickAction(_t('New'),  new TAction(array('SystemUnitForm', 'onEdit')), 'bs:plus-sign green');
        
        // creates a DataGrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);
        
        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', _t('Id'), 'center', 50);
        $column_name = new TDataGridColumn('name', _t('Name'), 'left');
        $column_cnpj = new TDataGridColumn('cnpj', 'Cnpj', 'left');
        $column_ie = new TDataGridColumn('ie', 'Ie', 'left');
        $column_nrjucesc = new TDataGridColumn('nrjucesc', 'Nrjucesc', 'left');
        $column_creci = new TDataGridColumn('creci', 'Creci', 'left');
        $column_zip = new TDataGridColumn('zip', 'Zip', 'left');
        $column_uf_id = new TDataGridColumn('uf_id', 'Uf Id', 'left');
        $column_citty = new TDataGridColumn('citty', 'Citty', 'left');
        $column_neighborhood = new TDataGridColumn('neighborhood', 'Neighborhood', 'left');
        $column_address = new TDataGridColumn('address', 'Address', 'left');
        $column_phone = new TDataGridColumn('phone', 'Phone', 'left');
        $column_fax = new TDataGridColumn('fax', 'Fax', 'left');
        $column_site = new TDataGridColumn('site', 'Site', 'left');
        $column_email = new TDataGridColumn('email', 'Email', 'left');
        $column_responsible_name = new TDataGridColumn('responsible_name', 'Responsible Name', 'left');
        $column_responsible_cpf = new TDataGridColumn('responsible_cpf', 'Responsible Cpf', 'left');
        $column_logo = new TDataGridColumn('logo', 'Logo', 'left');
        $column_bank = new TDataGridColumn('bank', 'Bank', 'left');

        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_name);
        $this->datagrid->addColumn($column_cnpj);
        /*$this->datagrid->addColumn($column_ie);
        $this->datagrid->addColumn($column_nrjucesc);
        $this->datagrid->addColumn($column_creci);
        $this->datagrid->addColumn($column_zip);
        $this->datagrid->addColumn($column_uf_id);
        $this->datagrid->addColumn($column_citty);*/
        $this->datagrid->addColumn($column_address);
        $this->datagrid->addColumn($column_neighborhood);
        $this->datagrid->addColumn($column_phone);
        /*$this->datagrid->addColumn($column_fax);
        $this->datagrid->addColumn($column_site);
        $this->datagrid->addColumn($column_email);
        $this->datagrid->addColumn($column_responsible_name);
        $this->datagrid->addColumn($column_responsible_cpf);
        $this->datagrid->addColumn($column_logo);
        $this->datagrid->addColumn($column_bank);*/
        
        // define the transformer method over image
        $column_logo->setTransformer( function($value, $object, $row) {
            if (file_exists($value)) {
                return new TImage($value);
            }
        });

        // creates the datagrid column actions
        $order_id = new TAction(array($this, 'onReload'));
        $order_id->setParameter('order', 'id');
        $column_id->setAction($order_id);
        
        $order_name = new TAction(array($this, 'onReload'));
        $order_name->setParameter('order', 'name');
        $column_name->setAction($order_name);
        
        // create EDIT action
        $action_edit = new TDataGridAction(array('SystemUnitForm', 'onEdit'));
        $action_edit->setButtonClass('btn btn-default');
        $action_edit->setLabel(_t('Edit'));
        $action_edit->setImage('fa:pencil-square-o blue fa-lg');
        $action_edit->setField('id');
        $this->datagrid->addAction($action_edit);
        
        // create DELETE action
        $action_del = new TDataGridAction(array($this, 'onDelete'));
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
        //$container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add(TPanelGroup::pack(_t('Units'), $this->form));
        $container->add(TPanelGroup::pack('', $this->datagrid));
        $container->add($this->pageNavigation);
        
        parent::add($container);
    }
}
