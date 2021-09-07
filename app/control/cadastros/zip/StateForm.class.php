<?php
/**
 * StateForm Registration
 * @author  <your name here>
 */
class StateForm extends TPage
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
        
        $this->setDatabase('zip');              // defines the database
        $this->setActiveRecord('State');     // defines the active record
        
        // creates the form
        $this->form = new TQuickForm('form_State');
        $this->form->class = 'tform'; // change CSS class
        $this->form = new BootstrapFormWrapper($this->form);
        $this->form->style = 'display: table;width:100%'; // change style
        $this->form->setFieldsByRow(2);
        
        // define the form title
        $this->form->setFormTitle('State');        

        // create the form fields
        $id = new TEntry('id');
        $name = new TEntry('name');
        $country_id = new TDBCombo('country_id','zip','Country','id','name');

        // add the fields
        $this->form->addQuickField('Sigla', $id,  100 );
        $this->form->addQuickField(_t('Name'), $name,  '100%' );
        $this->form->addQuickField(_t('Country'), $country_id,  '100%' );
        
        $id->placeholder = _t('Id');
        $name->placeholder = _t('Name');
        
        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
         
        // create the form actions
        $this->form->addQuickAction(_t('Save'), new TAction(array($this, 'onSave')), 'fa:floppy-o');
        $this->form->addQuickAction(_t('New'),  new TAction(array($this, 'onEdit')), 'bs:plus-sign green');
        $this->form->addQuickAction( _t('Back to the listing'), new TAction(array('StateList','onReload')),  'fa:table blue' );
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', 'StateList'));
        $container->add(TPanelGroup::pack(_t('States'), $this->form));
        
        parent::add($container);
    }
}