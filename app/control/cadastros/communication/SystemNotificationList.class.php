<?php
/**
 * SystemNotificationList Listing
 * @author  <your name here>
 */
class SystemNotificationList extends TStandardList
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
        
        parent::setDatabase('communication');            // defines the database
        parent::setActiveRecord('SystemNotification');   // defines the active record
        parent::setDefaultOrder('id', 'desc');         // defines the default order
        
        $criteria = new TCriteria;
        $criteria->add(new TFilter('system_user_to_id', '=', TSession::getValue('userid') ) );
        parent::setCriteria($criteria); // define a standard filter

        parent::addFilterField('checked', 'like', 'checked'); // filterField, operator, formField
        parent::addFilterField('subject', 'like', 'subject'); // filterField, operator, formField
        parent::addFilterField('message', 'like', 'message'); // filterField, operator, formField
        
        // creates the form
        $this->form = new BootstrapFormWrapper(new TQuickForm('form_search_SystemNotification'));
        $this->form->setFormTitle(_t('Notifications'));
        $this->form->setFieldsByRow(2);
        
        // create the form fields
        $subject = new TEntry('subject');
        $message = new TEntry('message');

        // add the fields
        $this->form->addQuickField( new TLabel(_t('Subject')), $subject );
        $this->form->addQuickField( new TLabel(_t('Message')), $message );

        $subject->setSize('100%');
        $message->setSize('100%');
        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('SystemNotification_filter_data') );
        
        // add the search form actions
        $this->form->addQuickAction(_t('Find'), new TAction(array($this, 'onSearch')), 'fa:search');
        
        // creates a DataGrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);

        // creates the datagrid columns
        $column_checked = new TDataGridColumn('action', _t('Action'), 'center', 200);
        $column_message = new TDataGridColumn('message', _t('Message'), 'left');
        
        $column_message->setTransformer( function($value, $object, $row) {
            try
            {
                TTransaction::open('permission');
                $user = SystemUser::find($object->system_user_id);
                $name = $user->name;
                TTransaction::close();
            }
            catch (Exception $e)
            {
                new TMessage('error', $e->getMessage());
            }
            
            if ($object->checked == 'Y')
            {
                $row->style = "color:gray";
            }
            $wrapper = new TElement('div');
            $wrapper->style = 'padding: 10px';
            $wrapper->add( '<b>'.$name . '</b>' .
                           '<div style="float:right"><i class="fa fa-calendar red"/> '.substr($object->dt_message, 0, 10) . '</div><br>' .
                           '<b>'.$object->subject . '</b> <br>' .
                           $object->message );
            return $wrapper;
        });
        
        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_checked);
        $this->datagrid->addColumn($column_message);

        $order = new TAction(array($this, 'onReload'));
        $order->setParameter('order', 'dt_message');
        $column_message->setAction($order);
        
        parent::setTransformer( array($this, 'onBeforeLoad') );
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // create the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        


        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(TPanelGroup::pack(_t('Notifications'), $this->form));
        $container->add(TPanelGroup::pack('', $this->datagrid));
        $container->add($this->pageNavigation);
        
        parent::add($container);
    }
    
    /**
     * Iterate all objects before rendering
     * Create the check/uncheck buttons
     */
    public function onBeforeLoad($objects, $param)
    {
        foreach ($objects as $object)
        {
            $button = new TElement('a');
            $button->generator = 'adianti';
            $button->class = 'btn btn-default';
            $button->style="width:160px";
            
            if ($object->checked == 'Y')
            {
                $button->href = 'index.php?class=SystemNotificationList&method=onUnCheck&id='.$object->id;
                $button->add( new TImage('fa:archive gray') );
                $button->add( TElement::tag('span', _t('Check as unread'), array('style' =>'color:gray' ) ) );
            }
            else
            {
                $button->href = 'index.php?class=SystemNotificationFormView&method=onExecuteAction&id='.$object->id;
                $button->add( new TImage( 'fa:' . substr($object->icon,6) ) );
                $button->add( TElement::tag('span', $object->action_label ) );
            }
            
            $object->action = $button;
        }
    }
    
    /**
     * Check message as read
     */
    public function onCheck($param)
    {
        try
        {
            TTransaction::open('communication');
            
            $message = SystemNotification::find($param['id']);
            if ($message)
            {
                if ($message->system_user_to_id == TSession::getValue('userid'))
                {
                    $message->checked = 'Y';
                    $message->store();
                    TScript::create('update_notifications_menu()');
                }
                else
                {
                    throw new Exception(_t('Permission denied'));
                }
            }
            TTransaction::close();
            
            parent::onReload($param);
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
    
    /**
     * Check message as unread
     */
    public function onUncheck($param)
    {
        try
        {
            TTransaction::open('communication');
            
            $message = SystemNotification::find($param['id']);
            if ($message)
            {
                if ($message->system_user_to_id == TSession::getValue('userid'))
                {
                    $message->checked = 'N';
                    $message->store();
                    TScript::create('update_notifications_menu()');
                }
                else
                {
                    throw new Exception(_t('Permission denied'));
                }
            }
            TTransaction::close();
            
            parent::onReload($param);
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
}
