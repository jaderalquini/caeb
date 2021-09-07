<?php
/**
 * FormForm Registration
 * @author  <your name here>
 */
class CardForm extends TPage
{
    protected $form; // form
    
    use Adianti\Base\AdiantiStandardFormTrait; // Standard form methods
    
    /**
     * Class constructor
     * Creates the page and the registration form
     */
    function __construct()
    {
        parent::__construct();
        
        $this->setDatabase('caeb');              // defines the database
        $this->setActiveRecord('Card');     // defines the active record
        
        // creates the form
        $this->form = new TQuickForm('form_Form');
        $this->form->class = 'tform'; // change CSS class
        $this->form = new BootstrapFormWrapper($this->form);
        $this->form->style = 'display: table;width:100%'; // change style
        
        // define the form title
        $this->form->setFormTitle('Form');       

        // create the form fields
        $id = new TEntry('id');
        $description = new TEntry('description');
        $content = new THtmlEditor('content');
        
        $id->setEditable(FALSE);

        // add the fields
        $this->form->addQuickField('Código', $id,  100 );
        $this->form->addQuickField('Descrição', $description,  '100%' );
        $this->form->addQuickField('Conteúdo', $content );
        
        $content->setSize('100%', 500);
         
        // create the form actions
        $this->form->addQuickAction(_t('Save'), new TAction(array($this, 'onSave')), 'fa:floppy-o');
        $this->form->addQuickAction(_t('New'),  new TAction(array($this, 'onEdit')), 'bs:plus-sign green');
        $this->form->addQuickAction( _t('Back to the listing'), new TAction(array('CardList','onReload')),  'fa:table blue' );
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', 'CardList'));
        $container->add(TPanelGroup::pack(_t('Cards'), $this->form));
        
        parent::add($container);
    }
}
?>