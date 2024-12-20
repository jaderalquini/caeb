<?php
/**
 * SystemDocumentForm Form
 * @author  <your name here>
 */
class SystemDocumentForm extends TPage
{
    protected $form; // form
    
    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();
        parent::include_js('app/lib/include/application.js');
        
        // creates the form
        $this->form = new BootstrapFormWrapper( new TQuickForm('form_SystemDocument') );
        $this->form->setFormTitle(_t('Document'));
        
        // create the form fields
        $id = new THidden('id');
        $title = new TEntry('title');
        $description = new TText('description');
        $category_id = new TDBCombo('category_id', 'communication', 'SystemDocumentCategory', 'id', 'name');
        $submission_date = new TDate('submission_date');
        $archive_date = new TDate('archive_date');
        $user_ids = new TDBMultiSearch('user_ids', 'permission', 'SystemUser', 'id', 'name');
        $group_ids = new TDBCheckGroup('group_ids', 'permission', 'SystemGroup', 'id', 'name');
        $group_ids->setLayout('horizontal');
        $user_ids->setMinLength(1);
        
        // add the fields
        $row = $this->form->addQuickField( new TLabel(_t('Id')), $id );
        $row->style = 'display:none';
        $this->form->addQuickField( new TLabel(_t('Title')), $title );
        $this->form->addQuickField( new TLabel(_t('Description')), $description );
        $this->form->addQuickField( new TLabel(_t('Category')), $category_id );
        $this->form->addQuickField( new TLabel(_t('Submission date')), $submission_date );
        $this->form->addQuickField( new TLabel(_t('Archive date')), $archive_date );
        $title->placeholder = _t('Title');
        $description->placeholder = _t('Description');
        
        $title->setSize('100%');
        $description->setSize('100%');
        $category_id->setSize('100%');
        $submission_date->setSize('120');
        $archive_date->setSize('120');
        $title->addValidation( _t('Title'), new TRequiredValidator );
        $description->addValidation( _t('Description'), new TRequiredValidator );
        $category_id->addValidation( _t('Category'), new TRequiredValidator );
        $submission_date->addValidation( _t('Submission date'), new TRequiredValidator );
        
        $this->form->addContent( [TElement::tag('h3', _t('Permission'))] );
        
        $this->form->addFields( [_t('Users')],  [$user_ids] );
        $this->form->addFields( [_t('Groups')], [$group_ids] );

        $description->setSize('100%',70);
        $user_ids->setSize('100%', 70);
        
        // create the form actions
        $this->form->addQuickAction(_t('Save'), new TAction(array($this, 'onSave')), 'fa:floppy-o');
        $this->form->addQuickAction(_t('New'),  new TAction(array($this, 'onClear')), 'bs:plus-sign green');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', 'SystemDocumentUploadForm'));
        $container->add(TPanelGroup::pack(_t('Send document'), $this->form));
        
        parent::add($container);
    }

    /**
     * Save form data
     * @param $param Request
     */
    public function onSave( $param )
    {
        try
        {
            TTransaction::open('communication'); // open a transaction
            $this->form->validate(); // validate form data
            
            $object = new SystemDocument;  // create an empty object
            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data
            $object->system_user_id = TSession::getValue('userid');
            $object->filename = TSession::getValue('system_document_upload_file');
            $object->clearParts();
            $object->store(); // save the object
            
            if ($data->user_ids)
            {
                foreach ($data->user_ids as $user_id => $user_name)
                {
                    TTransaction::open('permission');
                    $system_user = SystemUser::find($user_id);
                    TTransaction::close();
                    $object->addSystemUser( $system_user );
                }
            }
            
            if ($data->group_ids)
            {
                foreach ($data->group_ids as $group_id)
                {
                    TTransaction::open('permission');
                    $system_group = SystemGroup::find($group_id);
                    TTransaction::close();
                    $object->addSystemGroup( $system_group );
                }
            }
            
            $source_file   = 'tmp/'.TSession::getValue('system_document_upload_file');
            $target_path   = 'files/documents/' . $object->id;
            $target_file   =  $target_path . '/'.$object->filename;
            
            if (file_exists($source_file))
            {
                if (!file_exists($target_path))
                {
                    if (!mkdir($target_path, 0777, true))
                    {
                        throw new Exception(_t('Permission denied'). ': '. $target_path);
                    }
                }
                else
                {
                    foreach (glob("$target_path/*") as $file)
                    {
                        unlink($file);
                    }
                }
                
                // if the user uploaded a source file
                if (file_exists($target_path))
                {
                    // move to the target directory
                    rename($source_file, $target_file);
                }
            }
            
            // get the generated id
            $data->id = $object->id;
            
            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction
            
            //$action = new TAction;
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback(); // undo all pending operations
        }
    }
    
    /**
     * Clear form data
     * @param $param Request
     */
    public function onClear( $param )
    {
        $this->form->clear();
    }
    
    /**
     * Load object to form data
     * @param $param Request
     */
    public function onEdit( $param )
    {
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];  // get the parameter $key
                TTransaction::open('communication'); // open a transaction
                $object = new SystemDocument($key); // instantiates the Active Record
                
                if ($object->system_user_id == TSession::getValue('userid') OR TSession::getValue('login') === 'admin')
                {
                    $object->user_ids = $object->getSystemUsersIds();
                    $object->group_ids = $object->getSystemGroupsIds();
                    $this->form->setData($object); // fill the form
                }
                else
                {
                    throw new Exception(_t('Permission denied'));
                }
                TTransaction::close(); // close the transaction
                
                if (empty($param['hasfile']))
                {
                    TSession::setValue('system_document_upload_file', $object->filename);
                }
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
}
