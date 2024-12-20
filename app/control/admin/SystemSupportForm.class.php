<?php
/**
 * SystemMessageForm Registration
 * @author  <your name here>
 */
class SystemSupportForm extends TWindow
{
    protected $form; // form
    
    /**
     * Class constructor
     * Creates the page and the registration form
     */
    function __construct()
    {
        parent::__construct();
        parent::include_js('app/lib/include/application.js');
        parent::setSize(0.8, 370);
        parent::setTitle( _t('Open ticket') );
        
        // creates the form
        $this->form = new BootstrapFormWrapper(new TQuickForm('form_SystemMessage'));
        //$this->form->class = 'tform'; // change CSS class
        $this->form->style = 'display: table;width:100%'; // change style
        
        // define the form title
        $this->form->setFormTitle(_t('Ticket'));
        
        // create the form fields
        $subject = new TEntry('subject');
        $message = new TText('message');

        // add the fields
        $this->form->addQuickField(_t('Subject'), $subject,  '100%' );
        $this->form->addQuickField(_t('Message'), $message );
        $message->setSize('100%', '100');
        
        $subject->addValidation(_t('Subject'), new TRequiredValidator );
        $message->addValidation(_t('Message'), new TRequiredValidator );
        
        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        
        // create the form actions
        $this->form->addQuickAction(_t('Send'), new TAction(array($this, 'onSend')), 'fa:envelope-o');
        $this->form->addQuickAction(_t('Clear form'),  new TAction(array($this, 'onClear')), 'fa:eraser red');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(TPanelGroup::pack(_t('Ticket'), $this->form));
        
        parent::add($container);
    }
    
    public function onClear($param)
    {
    
    }
    
    public function onSend($param)
    {
        try
        {
            // get the form data
            $data = $this->form->getData();
            // validate data
            $this->form->validate();
            
            // open a transaction with database
            TTransaction::open('permission');
            
            $preferences = SystemPreference::getAllPreferences();
            
            $mail = new TMail;
            $mail->setFrom( trim($preferences['mail_from']) , TSession::getValue('username'));
            $mail->addAddress( trim($preferences['mail_support']) );
            $mail->setSubject( $data->subject );
            
            if ($preferences['smtp_auth'])
            {
                $mail->SetUseSmtp();
                $mail->SetSmtpHost($preferences['smtp_host'], $preferences['smtp_port']);
                $mail->SetSmtpUser($preferences['smtp_user'], $preferences['smtp_pass']);
            }
            
            $mail->setTextBody($data->message);
            
            $mail->send();
            
            // close the transaction
            TTransaction::close();
            
            // shows the success message
            new TMessage('info', _t('Message sent successfully'));
        }
        catch (Exception $e) // in case of exception
        {
            // get the form data
            $object = $this->form->getData();
            
            // fill the form with the active record data
            $this->form->setData($object);
            
            // shows the exception error message
            new TMessage('error', $e->getMessage());
            
            // undo all pending operations
            TTransaction::rollback();
        }
    }
}
