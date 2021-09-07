<?php
class SystemProfileForm extends TPage
{
    private $form;
    
    public function __construct()
    {
        parent::__construct();
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_System_group');
        $this->form->setFormTitle( _t('Profile') );
        
        $name  = new TEntry('name');
        $login = new TEntry('login');
        $email = new TEntry('email');
        $password1 = new TPassword('password1');
        $password2 = new TPassword('password2');
        $login->setEditable(FALSE);
        
        $name->setSize('100%');
        $login->setSize('100%');
        $email->setSize('100%');
        $password1->setSize('100%');
        $password2->setSize('100%');
        
        $this->form->addFields( [new TLabel(_t('Name'))], [$name] );
        $this->form->addFields( [new TLabel(_t('Login'))], [$login] );
        $this->form->addFields( [new TLabel(_t('Email'))], [$email] );
        
        $label = new TLabel(_t('Change password') . ' ('. _t('Leave empty to keep old password') . ')');
        $label->style = 'text-align: left; background: #FFFBCB; width: 100%;';
        
        $this->form->addFields( [$label] );
        $this->form->addFields( [new TLabel(_t('Password'))], [$password1] );
        $this->form->addFields( [new TLabel(_t('Password confirmation'))], [$password2] );
        
        $this->form->addAction(_t('Save'), new TAction(array($this, 'onSave')), 'fa:save');
        
        $bc = new TBreadCrumb();
        $bc->addHome();
        $bc->addItem(_t('Profile'));
        
        $container = new TVBox;
        $container->style = 'width:100%';
        $container->add($bc);
        $container->add($this->form);
        
        // add the form to the page
        parent::add($container);
        
        /*$this->form = new TQuickForm('form_Profile');
        $this->form->class = 'tform';
        $this->form = new BootstrapFormWrapper($this->form);
        $this->form->style = 'display: table;width:100%'; // change style
        $this->form->setFormTitle(_t('Profile'));
        
        $name  = new TEntry('name');
        $login = new TEntry('login');
        $email = new TEntry('email');
        $password1 = new TPassword('password1');
        $password2 = new TPassword('password2');
        $login->setEditable(FALSE);
        
        $this->form->addQuickField( _t('Name'), $name, '80%', new TRequiredValidator );
        $this->form->addQuickField( _t('Login'), $login, '80%', new TRequiredValidator );
        $this->form->addQuickField( _t('Email'), $email, '80%', new TRequiredValidator );
        
        $table = $this->form->getContainer();
        $row = $table->addRow();
        $row->style = 'background: #FFFBCB;';
        $cell = $row->addCell( new TLabel(_t('Change password') . ' ('. _t('Leave empty to keep old password') . ')') );
        $cell->colspan = 2;
        
        $this->form->addQuickField( _t('Password'), $password1, '80%' );
        $this->form->addQuickField( _t('Password confirmation'), $password2, '80%' );
        
        $this->form->addQuickAction(_t('Save'), new TAction(array($this, 'onSave')), 'fa:save');
        
        $bc = new TBreadCrumb();
        $bc->addHome();
        $bc->addItem(_t('Profile'));
        
        $container = TVBox::pack($bc, $this->form);
        $container->style = 'width:100%';
        
        parent::add($container);*/
    }
    
    public function onEdit($param)
    {
        try
        {
            TTransaction::open('permission');
            $login = SystemUser::newFromLogin( TSession::getValue('login') );
            $this->form->setData($login);
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
    
    public function onSave($param)
    {
        try
        {
            $this->form->validate();
            
            $object = $this->form->getData();
            
            TTransaction::open('permission');
            $user = SystemUser::newFromLogin( TSession::getValue('login') );
            $user->name = $object->name;
            $user->email = $object->email;
            
            if( $object->password1 )
            {
                if( $object->password1 != $object->password2 )
                {
                    throw new Exception(_t('The passwords do not match'));
                }
                
                $user->password = md5($object->password1);
            }
            else
            {
                unset($user->password);
            }
            
            $user->store();
            
            $this->form->setData($object);
            
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));
            
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
}