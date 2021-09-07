<?php
/**
 * SystemUnitForm Registration
 * @author  <your name here>
 */
class SystemUnitForm extends TPage
{
    protected $form; // form
    private $logoview;
    
    /**
     * Class constructor
     * Creates the page and the registration form
     */
    function __construct()
    {
        parent::__construct();
        parent::include_js('app/lib/include/application.js');
        
        // creates the form
        $this->form = new BootstrapFormBuilder;
        $this->form->setName('form_SystemUnit');
        $this->form->setFormTitle(_t('Units'));
        
        // create the form fields
        $id = new TEntry('id');
        $name = new TEntry('name');
        $cnpj = new TEntry('cnpj');
        $zip = new TEntry('zip');
        $uf_id = new TDBCombo('uf_id','zip', 'State', 'id', 'name');
        $city = new TDBEntry('city','zip','City','name');
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
        
        $cnpj->setMask('99.999.999/9999-99');
        $cnpj->addValidation('CNPJ', new TCNPJValidator);
        $zip->setMask('99999-999');
        $phone->setMask('(99)9999-9999');
        $fax->setMask('(99)9999-9999');
        $email->addValidation('Email', new TEmailValidator);
        
        // Adiciona validação aos campos
        $name->addValidation('Nome', new TRequiredValidator);
        $cnpj->addValidation('CNPJ', new TRequiredValidator);
        $uf_id->addValidation('UF', new TRequiredValidator);
        $city->addValidation('Município', new TRequiredValidator);
        $logo->addValidation('Logo', new TRequiredValidator);
        
        $this->logoview = new TElement('div');
        $this->logoview->id = 'logoview';
        $this->logoview->style = 'width:100%;height:auto;min-height:200px;border:1px solid gray;padding:4px;';
        
        $zip->setExitAction(new TAction(array($this, 'onExitZip')));
        $logo->setCompleteAction(new TAction(array($this, 'onCompleteLogo')));
        
        // add the fields
        $this->form->addFields( [$l1 = new TLabel(_t('Id'))], [$id], [$l2 = new TLabel(_t('Name'))], [$name] );
        $this->form->addFields( [$l3 = new TLabel('CNPJ')], [$cnpj], [$l4 = new TLabel('ZIP')], [$zip] );
        $this->form->addFields( [$l5 = new TLabel(_t('UF'))], [$uf_id], [$l6 = new TLabel('City')], [$city] );
        $this->form->addFields( [$l7 = new TLabel(_t('Neighborhood'))], [$neighborhood], [$l8 = new TLabel(_t('Address'))], [$address] );
        $this->form->addFields( [$l9 = new TLabel(_t('Phone'))], [$phone], [$l10 = new TLabel(_t('Site'))], [$site] );
        $this->form->addFields( [$l11 = new TLabel(_t('Email'))], [$email], [$l12 = new TLabel('Logo')], [$logo] );
        $this->form->addFields( [$l13 = new TLabel(_t('Bank'))], [$bank], [], [$this->logoview] );
        
        $l1->setFontStyle('bold');
        $l2->setFontStyle('bold');
        $l3->setFontStyle('bold');
        $l4->setFontStyle('bold');
        $l5->setFontStyle('bold');
        $l6->setFontStyle('bold');
        $l7->setFontStyle('bold');
        $l8->setFontStyle('bold');
        $l9->setFontStyle('bold');
        $l10->setFontStyle('bold');
        $l11->setFontStyle('bold');
        $l12->setFontStyle('bold');
        $l13->setFontStyle('bold');
        
        $id->setSize(100);  
        $name->setSize('100%');  
        $zip->setSize(100);  
        $city->setSize('100%');
        $neighborhood->setSize('100%');
        $address->setSize('100%'); 
        $site->setSize('100%');
        $responsible_name->setSize('100%');
        $logo->setSize('100%');
        
        $id->setEditable(FALSE);
        $id->placeholder = _t('Id');
        $name->placeholder = _t('Name');
        $cnpj->placeholder = 'CNPJ';
        $zip->placeholder = _t('ZIP');
        $city->placeholder = _t('City');
        $neighborhood->placeholder = _t('Neighborhood');
        $address->placeholder = _t('Address');
        $phone->placeholder = _t('Phone');
        $fax->placeholder = 'Fax';
        $site->placeholder = _t('Site');
        $email->placeholder = _t('Email');
        $responsible_name->placeholder = _t('Responsible Name');
        $responsible_cpf->placeholder = _t('Responsible CPF');
        $bank->placeholder = _t('Bank');
        
        // create the form actions
        $this->form->addAction(_t('Save'), new TAction(array($this, 'onSave')), 'fa:floppy-o');
        $this->form->addAction(_t('New'),  new TAction(array($this, 'onEdit')), 'bs:plus-sign green');
        $this->form->addAction(_t('Back to the listing'),new TAction(array('SystemUnitList','onReload')),'fa:table blue');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', 'SystemUnitList'));
        $container->add($this->form);
        
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
            TTransaction::open('permission'); // open a transaction
            
            /**
            // Enable Debug logger for SQL operations inside the transaction
            TTransaction::setLogger(new TLoggerSTD); // standard output
            TTransaction::setLogger(new TLoggerTXT('log.txt')); // file
            **/
            
            $this->form->validate(); // validate form data
            
            $object = new SystemUnit;  // create an empty object
            $data = $this->form->getData(); // get form data as array
            
            // have attachments
            if ($data->logo)
            {
                $target_folder = 'uploads/' . TSession::getValue('unitid');
                $target_file   = $target_folder . '/' .$data->logo;
                @mkdir($target_folder);
                rename('tmp/'.$data->logo, $target_file);
                $data->logo = $target_file;
            }
            
            $object->fromArray( (array) $data); // load the object with data
            $object->store(); // save the object
            
            // get the generated id
            $data->id = $object->id;
            
            $this->form->setData($data); // fill form data
            $this->onCompleteLogo($param);
            TTransaction::close(); // close the transaction
            
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
     * Fire form events
     * @param $param Request
     */
    public function fireEvents( $object )
    {        
        if ($object->logo !== '' && $object->logo !== null)
        {
            $image = new TImage($object->logo);
            $image->style = 'width: 100%';
            $this->logoview->add( $image );
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
                TTransaction::open('permission'); // open a transaction
                $object = new SystemUnit($key); // instantiates the Active Record
                $this->form->setData($object); // fill the form
                TTransaction::close(); // close the transaction
                $this->fireEvents( $object );
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
    
    public static function onExitZip($param)
    {
        TScript::create('cep = $("input[name=zip]").val();
                        if($.trim(cep) != ""){
		                $.getScript("http://cep.republicavirtual.com.br/web_cep.php?formato=javascript&cep="+cep, function(){
			                if(resultadoCEP["resultado"] == 1){
			                    var endereco = unescape(resultadoCEP["tipo_logradouro"])+" "+unescape(resultadoCEP["logradouro"]);
			                    var bairro = unescape(resultadoCEP["bairro"]);
			                    var cidade = unescape(resultadoCEP["cidade"]);
			                    var uf = unescape(resultadoCEP["uf"]);
			                    
			                    $("input[name=address]").val(endereco.toUpperCase());
			                    $("input[name=neighborhood]").val(bairro.toUpperCase());
			                    $("input[name=city]").val(cidade.toUpperCase());
			                    $("select[name=state_id]").val(uf.toUpperCase());
			                }
		                });
	                }');
    }
    
    public static function onCompleteLogo($param)
    {
        TScript::create("$('#logoview').html('')");
        TScript::create("$('#logoview').append(\"<img style='width:100%' src='tmp/{$param['logo']}'>\");");
    }
}