<?php
/**
 * SystemDocumentList Listing
 * @author  <your name here>
 */
class SystemSharedDocumentList extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $formgrid;
    private $loaded;
    private $deleteButton;
    
    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct()
    {
        parent::__construct();
        
        // creates the form
        $this->form = new BootstrapFormWrapper( new TQuickForm('form_search_SystemDocument') );
        $this->form->setFormTitle(_t('Shared with me'));
        $this->form->setFieldsByRow(2);
        
        // create the form fields
        $title       = new TEntry('title');
        $category_id = new TDBCombo('category_id', 'communication', 'SystemDocumentCategory', 'id', 'name');
        $filename    = new TEntry('filename');

        $this->form->addQuickField( new TLabel(_t('Title')), $title );
        $this->form->addQuickField( new TLabel(_t('Category')), $category_id );
        $this->form->addQuickField( new TLabel(_t('File')), $filename );
        $title->placeholder = _t('Title');
        $filename->placeholder = _t('File');
        
        $title->setSize('100%');
        $category_id->setSize('100%');
        $filename->setSize('100%');
        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('SystemDocument_filter_data') );
        
        // add the search form actions
        $this->form->addQuickAction(_t('Find'), new TAction(array($this, 'onSearch')), 'fa:search');
        $this->form->addQuickAction(_t('Clear Filter'),  new TAction(array($this, 'onClear')), 'bs:ban-circle red');
        $this->form->addQuickAction(_t('New'),  new TAction(array('SystemDocumentUploadForm', 'onNew')), 'bs:plus-sign green');
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->datatable='true';
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'center', 50);
        $column_title = new TDataGridColumn('title', _t('Title'), 'left');
        $column_category_id = new TDataGridColumn('category->name', _t('Category'), 'center');
        $column_submission_date = new TDataGridColumn('submission_date', _t('Date'), 'center');
        
        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_title);
        $this->datagrid->addColumn($column_category_id);
        $this->datagrid->addColumn($column_submission_date);
        
        if (TSession::getValue('login') == 'admin')
        {
            $column_user = new TDataGridColumn('system_user->name', _t('User'), 'left');
            $this->datagrid->addColumn($column_user);
        }
        
        // creates the datagrid column actions
        $order_id = new TAction(array($this, 'onReload'));
        $order_id->setParameter('order', 'id');
        $column_id->setAction($order_id);
        
        $order_title = new TAction(array($this, 'onReload'));
        $order_title->setParameter('order', 'title');
        $column_title->setAction($order_title);
        
        $order_category_id = new TAction(array($this, 'onReload'));
        $order_category_id->setParameter('order', 'category_id');
        $column_category_id->setAction($order_category_id);
        
        // create DOWNLOAD action
        $action_download = new TDataGridAction(array($this, 'onDownload'));
        //$action_edit->setUseButton(TRUE);
        $action_download->setButtonClass('btn btn-default');
        $action_download->setLabel(_t('Download'));
        $action_download->setImage('fa:cloud-download green fa-lg');
        $action_download->setField('id');
        $this->datagrid->addAction($action_download);
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        


        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add(TPanelGroup::pack(_t('Shared with me'), $this->form));
        $container->add(TPanelGroup::pack('', $this->datagrid));
        $container->add($this->pageNavigation);
        
        parent::add($container);
    }
    
    /**
     * Download file
     */
    public function onDownload($param)
    {
        try
        {
            if (isset($param['id']))
            {
                $id = $param['id'];  // get the parameter $key
                TTransaction::open('communication'); // open a transaction
                $object = new SystemDocument($id); // instantiates the Active Record
                
                //system_user_id
                if ($object->hasUserAccess( TSession::getValue('userid') ) OR $object->hasGroupAccess( TSession::getValue('usergroupids') ))
                {
                    if (strtolower(substr($object->filename, -4)) == 'html')
                    {
                        $win = TWindow::create( $object->filename, 0.8, 0.8 );
                        $win->add( file_get_contents( "files/documents/{$id}/".$object->filename ) );
                        $win->show();
                    }
                    else
                    {
                        TPage::openFile("files/documents/{$id}/".$object->filename);
                    }
                }
                else
                {
                    new TMessage('error', _t('Permission denied'));
                }
                TTransaction::close(); // close the transaction
            }
            else
            {
                $this->form->clear();
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }
    
    /**
     * Register the filter in the session
     */
    public function onSearch()
    {
        // get the search form data
        $data = $this->form->getData();
        
        // clear session filters
        TSession::setValue('SystemDocumentList_filter_title',   NULL);
        TSession::setValue('SystemDocumentList_filter_category_id',   NULL);
        TSession::setValue('SystemDocumentList_filter_filename',   NULL);

        if (isset($data->title) AND ($data->title)) {
            $filter = new TFilter('title', 'like', "%{$data->title}%"); // create the filter
            TSession::setValue('SystemDocumentList_filter_title',   $filter); // stores the filter in the session
        }


        if (isset($data->category_id) AND ($data->category_id)) {
            $filter = new TFilter('category_id', '=', "$data->category_id"); // create the filter
            TSession::setValue('SystemDocumentList_filter_category_id',   $filter); // stores the filter in the session
        }

        if (isset($data->filename) AND ($data->filename)) {
            $filter = new TFilter('filename', 'like', "%{$data->filename}%"); // create the filter
            TSession::setValue('SystemDocumentList_filter_filename',   $filter); // stores the filter in the session
        }

        
        // fill the form with data again
        $this->form->setData($data);
        
        // keep the search data in the session
        TSession::setValue('SystemDocument_filter_data', $data);
        
        $param=array();
        $param['offset']    =0;
        $param['first_page']=1;
        $this->onReload($param);
    }
    
    public function onClear( $param )
    {
        $this->form->clear();
        
        TSession::setValue('SystemDocumentList_filter_title',   NULL);
        TSession::setValue('SystemDocumentList_filter_category_id',   NULL);
        TSession::setValue('SystemDocumentList_filter_filename',   NULL);
        
        TSession::setValue('SystemDocument_filter_data', NULL);
        
        $param=array();
        $param['offset']    =0;
        $param['first_page']=1;
        $this->onReload($param);
    }
    
    /**
     * Load the datagrid with data
     */
    public function onReload($param = NULL)
    {
        try
        {
            // open a transaction with database 'communication'
            TTransaction::open('communication');
            
            // creates a repository for SystemDocument
            $repository = new TRepository('SystemDocument');
            $limit = 10;
            // creates a criteria
            $criteria = new TCriteria;
            
            // default order
            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'asc';
            }
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);
            
            $criteria->add(new TFilter('archive_date', 'is', null));
            
            // shared sub-criteria
            $userid = TSession::getValue('userid');
            $usergroups = TSession::getValue('usergroupids');
            $shared_criteria = new TCriteria;
            $shared_criteria->add(new TFilter('id', 'IN', "(SELECT document_id FROM system_document_user WHERE system_user_id='$userid')"), TExpression::OR_OPERATOR);
            $shared_criteria->add(new TFilter('id', 'IN', "(SELECT document_id FROM system_document_group WHERE system_group_id IN ($usergroups))"), TExpression::OR_OPERATOR);
            $criteria->add($shared_criteria);
            
            
            if (TSession::getValue('SystemDocumentList_filter_title')) {
                $criteria->add(TSession::getValue('SystemDocumentList_filter_title')); // add the session filter
            }


            if (TSession::getValue('SystemDocumentList_filter_category_id')) {
                $criteria->add(TSession::getValue('SystemDocumentList_filter_category_id')); // add the session filter
            }
            
            if (TSession::getValue('SystemDocumentList_filter_filename')) {
                $criteria->add(TSession::getValue('SystemDocumentList_filter_filename')); // add the session filter
            }

            
            // load the objects according to criteria
            $objects = $repository->load($criteria, FALSE);
            
            if (is_callable($this->transformCallback))
            {
                call_user_func($this->transformCallback, $objects, $param);
            }
            
            $this->datagrid->clear();
            if ($objects)
            {
                // iterate the collection of active records
                foreach ($objects as $object)
                {
                    // add the object inside the datagrid
                    $this->datagrid->addItem($object);
                }
            }
            
            // reset the criteria for record count
            $criteria->resetProperties();
            $count= $repository->count($criteria);
            
            $this->pageNavigation->setCount($count); // count of records
            $this->pageNavigation->setProperties($param); // order, page
            $this->pageNavigation->setLimit($limit); // limit
            
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
    
    
    /**
     * method show()
     * Shows the page
     */
    public function show()
    {
        // check if the datagrid is already loaded
        if (!$this->loaded AND (!isset($_GET['method']) OR !(in_array($_GET['method'],  array('onReload', 'onSearch')))) )
        {
            if (func_num_args() > 0)
            {
                $this->onReload( func_get_arg(0) );
            }
            else
            {
                $this->onReload();
            }
        }
        parent::show();
    }
}
