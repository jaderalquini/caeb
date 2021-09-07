<?php
/**
 * CityForm Registration
 * @author  <your name here>
 */
class CityForm extends TPage
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
        $this->setActiveRecord('City');     // defines the active record
        
        // creates the form
        $this->form = new TQuickForm('form_City');
        $this->form->class = 'tform'; // change CSS class
        $this->form = new BootstrapFormWrapper($this->form);
        $this->form->style = 'display: table;width:100%'; // change style
        $this->form->setFieldsByRow(2);
        
        // define the form title
        $this->form->setFormTitle('City');        

        // create the form fields
        $id = new TEntry('id');
        $name = new TEntry('name');
        $state_id = new TDBCombo('state_id','zip','State','id','name');
        
        $id->placeholder = _t('Id');
        $name->placeholder = _t('Name');

        // add the fields
        $this->form->addQuickField(_t('Id'), $id,  100 );
        $this->form->addQuickField(_t('Name'), $name,  '100%' );
        $this->form->addQuickField(_t('State'), $state_id,  200 );
        
        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
         
        // create the form actions
        $this->form->addQuickAction(_t('Save'), new TAction(array($this, 'onSave')), 'fa:floppy-o');
        $this->form->addQuickAction(_t('New'),  new TAction(array($this, 'onEdit')), 'bs:plus-sign green');
        $this->form->addQuickAction( _t('Back to the listing'), new TAction(array('CityList','onReload')),  'fa:table blue' );
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', 'CityList'));
        $container->add(TPanelGroup::pack(_t('Cities'), $this->form));
        
        parent::add($container);
    }
}
