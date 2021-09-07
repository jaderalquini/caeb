<?php
/**
 * PersonList Listing
 * @author  <your name here>
 */
class PersonList extends TStandardList
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
        
        parent::setDatabase(TSession::getValue('unitbank'));            // defines the database
        parent::setActiveRecord('Person');   // defines the active record
        parent::setDefaultOrder('name', 'asc');         // defines the default order
        // parent::setCriteria($criteria) // define a standard filter

        parent::addFilterField('id', 'like', 'id'); // filterField, operator, formField
        parent::addFilterField('name', 'like', 'name'); // filterField, operator, formField
        parent::addFilterField('cpf', 'like', 'cpf'); // filterField, operator, formField
        parent::addFilterField('birthdate', 'like', 'birthdate'); // filterField, operator, formField
        parent::addFilterField('address', 'like', 'address'); // filterField, operator, formField
        parent::addFilterField('neighborhood', 'like', 'neighborhood'); // filterField, operator, formField
        parent::addFilterField('zip', 'like', 'zip'); // filterField, operator, formField
        parent::addFilterField('city', 'like', 'city'); // filterField, operator, formField
        parent::addFilterField('state_id', 'like', 'state_id'); // filterField, operator, formField
        parent::addFilterField('phone', 'like', 'phone'); // filterField, operator, formField
        parent::addFilterField('celphone', 'like', 'celphone'); // filterField, operator, formField
        parent::addFilterField('email', 'like', 'email'); // filterField, operator, formField
        parent::addFilterField('assignmenter', 'like', 'assignmenter'); // filterField, operator, formField
        parent::addFilterField('registerdate', 'like', 'registerdate'); // filterField, operator, formField
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_search_Person');
        $this->form->setFormTitle(_t('Peopple'));

        // create the form fields
        $id = new TEntry('id');
        $name = new TEntry('name');
        $cpf = new TEntry('cpf');
        $birthdate = new TEntry('birthdate');
        $address = new TEntry('address');
        $neighborhood = new TEntry('neighborhood');
        $zip = new TEntry('zip');
        $city = new TEntry('city');
        $state_id = new TEntry('state_id');
        $phone = new TEntry('phone');
        $celphone = new TEntry('celphone');
        $email = new TEntry('email');
        $assignmenter = new TRadioGroup('assignmenter');
        $status = new TRadioGroup('status');
        $registerdate = new TEntry('registerdate');
        $output_type  = new THidden('output_type');
        
        $output_type->setValue('pdf');
        
        $name->setSize('100%'); 

        // add the fields        
        $this->form->addFields( [$l1 = new TLabel(_t('Name'))], [$name]);
        $this->form->addFields( [$l2 = new TLabel(_t('Assignmenter'))], [$assignmenter] );
        $this->form->addFields( [$l3 = new TLabel('Status')], [$status] );
        $this->form->addFields( [new TLabel('')], [$output_type] );
        
        $l1->setFontStyle('bold');
        $l2->setFontStyle('bold');
        $l3->setFontStyle('bold');
        
        
        $name->placeholder=_t('Name');
        $assignmenter->addItems(array('S' => 'Sim', 'N' => 'Não'));
        $assignmenter->setLayout('horizontal');
        
        $status->addItems(array('A' => 'Ativo', 'I' => 'Inativo'));
        $status->setLayout('horizontal');
        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('Person_filter_data') );
        
        // add the search form actions
        $this->form->addAction(_t('Find'), new TAction(array($this, 'onSearch')), 'fa:search');
        $this->form->addAction(_t('Clear Filter'),  new TAction(array($this, 'onClear')), 'bs:ban-circle red');        
        $this->form->addAction(_t('New'),  new TAction(array('PersonForm', 'onEdit')), 'bs:plus-sign green');
        $this->form->addAction(_t('Report'), new TAction(array($this, 'onGenerateReport')), 'fa:print');
        
        // creates a DataGrid
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', _t('Id'), 'left');
        $column_name = new TDataGridColumn('name', _t('Name'), 'left');
        $column_cpf = new TDataGridColumn('cpf', 'Cpf', 'left');
        $column_birthdate = new TDataGridColumn('birthdate', 'Birthdate', 'left');
        $column_address = new TDataGridColumn('address', 'Address', 'left');
        $column_neighborhood = new TDataGridColumn('neighborhood', 'Neighborhood', 'left');
        $column_zip = new TDataGridColumn('zip', 'Zip', 'left');
        $column_city = new TDataGridColumn('city', 'City', 'left');
        $column_state_id = new TDataGridColumn('state_id', 'State Id', 'left');
        $column_phone = new TDataGridColumn('phone', _t('Phone'), 'left');
        $column_celphone = new TDataGridColumn('celphone', _t('Cel Phone'), 'left');
        $column_email = new TDataGridColumn('email', 'Email', 'left');
        $column_assignmenter = new TDataGridColumn('assignmenter', _t('Assignmenter'), 'center');
        $column_status = new TDataGridColumn('status', 'Status', 'center');
        $column_registerdate = new TDataGridColumn('registerdate', 'Registerdate', 'left');

        // add the columns to the DataGrid
        //$this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_name);
        /*$this->datagrid->addColumn($column_cpf);
        $this->datagrid->addColumn($column_birthdate);
        $this->datagrid->addColumn($column_address);
        $this->datagrid->addColumn($column_neighborhood);
        $this->datagrid->addColumn($column_zip);
        $this->datagrid->addColumn($column_city);
        $this->datagrid->addColumn($column_state_id);*/
        $this->datagrid->addColumn($column_phone);
        $this->datagrid->addColumn($column_celphone);
        $this->datagrid->addColumn($column_email);
        $this->datagrid->addColumn($column_assignmenter);
        $this->datagrid->addColumn($column_status);
        //$this->datagrid->addColumn($column_registerdate);
        
        $column_assignmenter->setTransformer( function($value, $object, $row) {
            $class = ($value=='N') ? 'info' : 'primary';
            $label = ($value=='N') ? _t('No') : _t('Yes');
            $div = new TElement('span');
            $div->class="label label-{$class}";
            $div->style="text-shadow:none; font-size:12px; font-weight:lighter";
            $div->add($label);
            return $div;
        });
        
        $column_status->setTransformer( function($value, $object, $row) {
            $class = ($value=='I') ? 'danger' : 'success';
            $label = ($value=='I') ? _t('Inactive') : _t('Active');
            $div = new TElement('span');
            $div->class="label label-{$class}";
            $div->style="text-shadow:none; font-size:12px; font-weight:lighter";
            $div->add($label);
            return $div;
        });
        
        // creates the datagrid column actions
        $order_id = new TAction(array($this, 'onReload'));
        $order_id->setParameter('order', 'id');
        $column_id->setAction($order_id);
        
        $order_name = new TAction(array($this, 'onReload'));
        $order_name->setParameter('order', 'name');
        $column_name->setAction($order_name);
        
        // create EDIT action
        $action_edit = new TDataGridAction(array('PersonForm', 'onEdit'));
        //$action_edit->setUseButton(TRUE);
        $action_edit->setButtonClass('btn btn-default');
        $action_edit->setLabel(_t('Edit'));
        $action_edit->setImage('fa:pencil-square-o blue fa-lg');
        $action_edit->setField('id');
        $this->datagrid->addAction($action_edit);
        
        // create DELETE action
        $action_del = new TDataGridAction(array($this, 'onDelete'));
        //$action_del->setUseButton(TRUE);
        $action_del->setButtonClass('btn btn-default');
        $action_del->setLabel(_t('Delete'));
        $action_del->setImage('fa:trash-o red fa-lg');
        $action_del->setField('id');
        $this->datagrid->addAction($action_del);
        
        $action_ficha = new TDataGridAction(array($this, 'onPrintFichaCadastral'));
        //$action_del->setUseButton(TRUE);
        $action_ficha->setButtonClass('btn btn-default');
        $action_ficha->setLabel('Imprimir Ficha de Cadastro');
        $action_ficha->setImage('fa:print blue fa-lg');
        $action_ficha->setField('id');
        $this->datagrid->addAction($action_ficha);
        
        $action_agenda = new TDataGridAction(array($this, 'onInputDialog'));
        //$action_del->setUseButton(TRUE);
        $action_agenda->setButtonClass('btn btn-default');
        $action_agenda->setLabel('Imprimir Ficha de Agendamentos');
        $action_agenda->setImage('fa:calendar red fa-lg');
        $action_agenda->setField('id');
        $this->datagrid->addAction($action_agenda);
        
        // create ONOFF action
        $action_onoff = new TDataGridAction(array($this, 'onTurnOnOff'));
        $action_onoff->setButtonClass('btn btn-default');
        $action_onoff->setLabel(_t('Activate/Deactivate'));
        $action_onoff->setImage('fa:power-off fa-lg orange');
        $action_onoff->setField('id');
        $this->datagrid->addAction($action_onoff);
        
        $this->datagrid->createModel();
        
        // create the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());        

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add($this->datagrid);
        $container->add($this->pageNavigation);
        
        parent::add($container);
    }
    
    public function onGenerateReport($param)
    {        
        $data = $this->form->getData();
        
        try
        {
            // open a transaction with database 'samples'
            TTransaction::open(TSession::getValue('unitbank'));
            
            $formdata = $this->form->getData();
            
            $repository = new TRepository('Person');
            $criteria = new TCriteria;
            
            $filters=array();
            if ($formdata->name)
            {
                $criteria->add(new TFilter('name', 'like', '%'.$formdata->name.'%'));
                $filters[]=_t('Name').': '.$formdata->name;
            }
            
            if ($formdata->assignmenter)
            {
                $criteria->add(new TFilter('assignmenter', '=', $formdata->assignmenter));
                if ($formdata->assignmenter == 'N')
                {
                    $filters[]=_t('Assignmenter').': '.utf8_decode(_t('No'));
                } 
                else 
                {
                    $filters[]=_t('Assignmenter').': '.utf8_decode(_t('Yes'));
                }                
            }
            
            if ($formdata->status)
            {
                $criteria->add(new TFilter('status', '=', $formdata->status));
                if ($formdata->status == 'I')
                {
                    $filters[]='Status: '.utf8_decode(_t('Inactive'));
                } 
                else 
                {
                    $filters[]='Status: '.utf8_decode(_t('Active'));
                }                
            }
            
            $param['order'] = 'name';
            $param['direction'] = 'asc';
            $criteria->setProperties($param);
            
            $objects = $repository->load($criteria, FALSE);
            $format  = $formdata->output_type;
            
            if ($objects)
            {
                $designer = new TReport();
                $designer->setTitle('Relatório de Pessoas');
                $columns = array();
                $columns[0]['size'] = 225;
                $columns[0]['text'] = _t('Name');
                $columns[0]['align'] = 'L';
                $columns[1]['size'] = 80;
                $columns[1]['text'] = _t('Phone');
                $columns[1]['align'] = 'L';
                $columns[2]['size'] = 80;
                $columns[2]['text'] = _t('Cel Phone');
                $columns[2]['align'] = 'L';
                $columns[3]['size'] = 155;
                $columns[3]['text'] = 'E-mail';
                $columns[3]['align'] = 'L';
                $columns[4]['size'] = 50;
                $designer->setFilters($filters);
                $designer->setColumns($columns);
                $designer->AddPage('Portrait','A4');
                $designer->SetMargins(30,30,30);
                $designer->SetAutoPageBreak(true, 30);
                $designer->SetFont('Arial','',10);
                $designer->SetX(30);
                
                $designer->SetFillColor(220,220,220);
                $fill = FALSE;                
                $designer->SetFont('Arial','',8);
                $i=0;
                foreach ($objects as $object)
                {
                    $i++;
                    $designer->SetTextColor(0,0,0);
                    $designer->Cell(225,15,utf8_decode($object->name),0,0,'L',$fill);
                    $designer->Cell(80,15,utf8_decode($object->phone),0,0,'L',$fill);
                    $designer->Cell(80,15,utf8_decode($object->celphone),0,0,'L',$fill);
                    $designer->Cell(155,15,utf8_decode($object->email),0,0,'L',$fill);
                    $designer->Ln();
                    $fill = !$fill;
                }
                
                $designer->SetTextColor(0,126,196);
                $designer->Cell(225,15,'',0,0,'R',$fill);
                $designer->Cell(160,15,'Total de Pessoas',0,0,'R',$fill);
                $designer->Cell(155,15,$i,0,0,'L',$fill);
                    
                if (!file_exists("app/output/PersonReport.{$format}") OR is_writable("app/output/PersonReport.{$format}"))
                {
                    $designer->save("app/output/PersonReport.{$format}");
                }
                else
                {
                    throw new Exception(_t('Permission denied') . ': ' . "app/output/PersonReport.{$format}");
                }
                    
                parent::openFile("app/output/PersonReport.{$format}"); 
            }
            else
            {
                new TMessage('error', _t('No records found'));
            }
            
            // close the transaction
            TTransaction::close();
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', '<b>Error</b> ' . $e->getMessage());
            
            // undo all pending operations
            TTransaction::rollback();
        }
        
        $this->form->setData($data);
    }  
    
    public function onTurnOnOff($param)
    {
        try
        {
            TTransaction::open('caeb');
            $person = Person::find($param['id']);
            if ($person instanceof Person)
            {
                $person->status = $person->status == 'A' ? 'I' : 'A';
                $person->store();
            }
            
            TTransaction::close();
            
            $this->onReload($param);
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    public function onPrintFichaCadastral($param)
    {   
        $key=$param['key'];
        
        try
        {                
            $designer = new TPDFDesigner;
            $designer->generate();
            $designer->setMargins(30,15); 
            
            $image_file = TSession::getValue('unitlogo');
            $designer->Image($image_file, 30, 30, 100, 50, 'PNG', '', 'T', FALSE, 300, '', FALSE, FALSE, 0, FALSE, FALSE, FALSE);
            $designer->setY(45);
            $designer->SetFont('Arial','B',12); 
            $linha = 'ATENDIMENTO FRATERNO';   
            $designer->Cell(565,15,utf8_decode($linha),0,0,'C');
            $designer->Ln();
            
            TTransaction::open('caeb');
            $person = new Person($key);
            TTransaction::close();
            
            if ($person->assignmenter == 'S')
            {
                $linha = 'Ficha do TAREFEIRO';
                $designer->Cell(565,15,utf8_decode($linha),0,0,'C');
                $designer->Ln();
                $designer->Ln();
                $designer->SetFont('Arial','',10);
                $linha = 'NOME DO TAREFEIRO : '.$person->name;
                $designer->Cell(565,15,utf8_decode($linha),0,0,'L');
                $designer->Ln();
                $linha = 'Data Nas:';
                $designer->Cell(80,15,utf8_decode($linha),0,0,'L');
                if ($person->birthdate)
                {
                    $birthdate=explode('-',$person->birthdate);
                    $date=date('d/m/Y');
                    $date=explode('/',$date);
                    
                    $anos=$date[2]-$birthdate[0];
                    
                    if($birthdate[1] > $date[1])
                    {
                        $anos = $anos - 1;
                    }
                    
                    if($birthdate[1] == $date[1])
                    {
                        if($birthdate[2] <= $date[0])
                        {
                            
                        }
                        else
                        {
                            $anos = $anos - 1;
                        }
                    }
                    
                    if ($birthdate[1] < $date[1])
                    {
                        
                    }
                    
                    $birthdate = ' '.$birthdate[2].' / '.$birthdate[1].' / '.$birthdate[0].' ';
                    $age = $anos . ' anos';
                }
                else
                {
                    $birthdate = '....../....../........';
                    $age = '...........';
                }
                $designer->SetX(85);
                $designer->Cell(70,15,$birthdate,0,0,'L');
                $designer->SetX(160);
                $linha = 'Idade:';
                $designer->Cell(30,15,utf8_decode($linha),0,0,'L');
                $designer->SetX(195);
                $designer->Cell(50,15,$age,0,0,'L');
                $designer->SetX(260);
                $linha = 'Telefone: '.$person->phone;
                $designer->Cell(200,15,utf8_decode($linha),0,0,'L');
                $designer->Ln();
                $linha = '1. Descreva o que está lhe afligindo: ( ) Emocional ( ) Espiritual ( ) Físico';
                $designer->Cell(565,15,utf8_decode($linha),0,0,'L');
                $designer->Ln();
                $designer->Ln();
                $linha = '.................................................................................................................................................................................................';
                $designer->Cell(565,15,utf8_decode($linha),0,0,'L');
                $designer->Ln();
                $designer->Ln();
                $linha = '.................................................................................................................................................................................................';
                $designer->Cell(565,15,utf8_decode($linha),0,0,'L');
                $designer->Ln();
                $designer->Ln();
                $linha = '.................................................................................................................................................................................................';
                $designer->Cell(565,15,utf8_decode($linha),0,0,'L');
                $designer->Ln();
                $linha = '2. Você já fez tratamento no CAEB para esta mesma situação?';
                $designer->Cell(480,15,utf8_decode($linha),0,0,'L');
                $designer->SetX(485);
                $linha = '( )SIM';
                $designer->Cell(50,15,utf8_decode($linha),0,0,'L');
                $designer->SetX(535);
                $linha = '( )NÃO';
                $designer->Cell(50,15,utf8_decode($linha),0,0,'L');
                $designer->Ln();
                $linha = '3. Você está fazendo acompanhamento médico?';
                $designer->Cell(480,15,utf8_decode($linha),0,0,'L');
                $designer->SetX(485);
                $linha = '( )SIM';
                $designer->Cell(50,15,utf8_decode($linha),0,0,'L');
                $designer->SetX(535);
                $linha = '( )NÃO';
                $designer->Cell(50,15,utf8_decode($linha),0,0,'L');
                $designer->Ln();
                $linha = '4. Você está fazendo acompanhamento com psicólogo ou psicoterapeuta?';
                $designer->Cell(480,15,utf8_decode($linha),0,0,'L');
                $designer->SetX(485);
                $linha = '( )SIM';
                $designer->Cell(50,15,utf8_decode($linha),0,0,'L');
                $designer->SetX(535);
                $linha = '( )NÃO';
                $designer->Cell(50,15,utf8_decode($linha),0,0,'L');
                $designer->Ln();
                $linha = '5. Está usando medicamento controlado?';
                $designer->Cell(480,15,utf8_decode($linha),0,0,'L');
                $designer->SetX(485);
                $linha = '( )SIM';
                $designer->Cell(50,15,utf8_decode($linha),0,0,'L');
                $designer->SetX(535);
                $linha = '( )NÃO';
                $designer->Cell(50,15,utf8_decode($linha),0,0,'L');
                $designer->Ln();
                $designer->SetX(50);
                $linha = 'Se sim qual?......................................................................................................................................................................';
                $designer->Cell(565,15,utf8_decode($linha),0,0,'L');
                $designer->Ln();
                $linha = '6 . Alguma vez pensou em desistir de viver?';
                $designer->Cell(400,15,utf8_decode($linha),0,0,'L');
                $designer->SetX(485);
                $linha = '( )SIM';
                $designer->Cell(50,15,utf8_decode($linha),0,0,'L');
                $designer->SetX(535);
                $linha = '( )NÃO';
                $designer->Cell(50,15,utf8_decode($linha),0,0,'L');
                $designer->Ln();
                $designer->SetX(50);
                $linha = 'Se sim por que?.................................................................................................................................................................';
                $designer->Cell(565,15,utf8_decode($linha),0,0,'L');
                $designer->Ln();
                $linha = '7. Você tem ( ) Fibromialgia ( ) Artrite ( ) Artrose ( ) Reumatismo';
                $designer->Cell(565,15,utf8_decode($linha),0,0,'L');
                $linha = '8. Nível de estudo no CAEB......................................................................................................................................................';
                $designer->Ln();
                $designer->Cell(565,15,utf8_decode($linha),0,0,'L');
                $data = date('d/m/Y');
                $linha = 'Declaro estar ciente destas informações: Assinatura: _________________________________________Data '.$data;
                $designer->Ln();
                $designer->Cell(565,15,utf8_decode($linha),0,0,'L');
                $designer->Ln();
                $linha = 'Nossa Missão: ATRAVÉS DO AMOR OBTER A CURA.';
                $designer->Ln();
                $designer->SetFont('Arial','',12); 
                $designer->Cell(565,15,utf8_decode($linha),0,0,'C');
                $designer->Ln();
                $linha = '==============================================================================';
                $designer->Ln();
                $designer->Cell(565,15,utf8_decode($linha),0,0,'L');
                $designer->Ln();
                $Y = $designer->GetY();
                $designer->Image($image_file, 30, $Y, 100, 50, 'PNG', '', 'T', FALSE, 300, '', FALSE, FALSE, 0, FALSE, FALSE, FALSE);
                $linha = 'ATENDIMENTO FRATERNO';
                $designer->Ln();   
                $designer->Cell(565,15,utf8_decode($linha),0,0,'C');
                $linha = 'Ficha do TAREFEIRO';
                $designer->Ln();
                $designer->Cell(565,15,utf8_decode($linha),0,0,'C');
                $designer->Ln();
                $designer->Ln();
                $designer->SetFont('Arial','',10);
                $linha = 'NOME DO TAREFEIRO : '.$person->name;
                $designer->Cell(565,15,utf8_decode($linha),0,0,'L');
                $designer->Ln();
                $linha = 'Data Nas:';
                $designer->Cell(80,15,utf8_decode($linha),0,0,'L');
                $designer->SetX(85);
                $designer->Cell(70,15,$birthdate,0,0,'L');
                $designer->SetX(160);
                $linha = 'Idade:';
                $designer->Cell(30,15,utf8_decode($linha),0,0,'L');
                $designer->SetX(195);
                $designer->Cell(50,15,$age,0,0,'L');
                $designer->SetX(260);
                $linha = 'Telefone: '.$person->phone;
                $designer->Cell(200,15,utf8_decode($linha),0,0,'L');
                $designer->Ln();
                $linha = '1. Descreva o que está lhe afligindo: ( ) Emocional ( ) Espiritual ( ) Físico';
                $designer->Cell(565,15,utf8_decode($linha),0,0,'L');
                $designer->Ln();
                $designer->Ln();
                $linha = '.................................................................................................................................................................................................';
                $designer->Cell(565,15,utf8_decode($linha),0,0,'L');
                $designer->Ln();
                $designer->Ln();
                $linha = '.................................................................................................................................................................................................';
                $designer->Cell(565,15,utf8_decode($linha),0,0,'L');
                $designer->Ln();
                $designer->Ln();
                $linha = '.................................................................................................................................................................................................';
                $designer->Cell(565,15,utf8_decode($linha),0,0,'L');
                $designer->Ln();
                $linha = '2. Você já fez tratamento no CAEB para esta mesma situação?';
                $designer->Cell(480,15,utf8_decode($linha),0,0,'L');
                $designer->SetX(485);
                $linha = '( )SIM';
                $designer->Cell(50,15,utf8_decode($linha),0,0,'L');
                $designer->SetX(535);
                $linha = '( )NÃO';
                $designer->Cell(50,15,utf8_decode($linha),0,0,'L');
                $designer->Ln();
                $linha = '3. Você está fazendo acompanhamento médico?';
                $designer->Cell(480,15,utf8_decode($linha),0,0,'L');
                $designer->SetX(485);
                $linha = '( )SIM';
                $designer->Cell(50,15,utf8_decode($linha),0,0,'L');
                $designer->SetX(535);
                $linha = '( )NÃO';
                $designer->Cell(50,15,utf8_decode($linha),0,0,'L');
                $designer->Ln();
                $linha = '4. Você está fazendo acompanhamento com psicólogo ou psicoterapeuta?';
                $designer->Cell(480,15,utf8_decode($linha),0,0,'L');
                $designer->SetX(485);
                $linha = '( )SIM';
                $designer->Cell(50,15,utf8_decode($linha),0,0,'L');
                $designer->SetX(535);
                $linha = '( )NÃO';
                $designer->Cell(50,15,utf8_decode($linha),0,0,'L');
                $designer->Ln();
                $linha = '5. Está usando medicamento controlado?';
                $designer->Cell(480,15,utf8_decode($linha),0,0,'L');
                $designer->SetX(485);
                $linha = '( )SIM';
                $designer->Cell(50,15,utf8_decode($linha),0,0,'L');
                $designer->SetX(535);
                $linha = '( )NÃO';
                $designer->Cell(50,15,utf8_decode($linha),0,0,'L');
                $designer->Ln();
                $designer->SetX(50);
                $linha = 'Se sim qual?......................................................................................................................................................................';
                $designer->Cell(565,15,utf8_decode($linha),0,0,'L');
                $designer->Ln();
                $linha = '6 . Alguma vez pensou em desistir de viver?';
                $designer->Cell(400,15,utf8_decode($linha),0,0,'L');
                $designer->SetX(485);
                $linha = '( )SIM';
                $designer->Cell(50,15,utf8_decode($linha),0,0,'L');
                $designer->SetX(535);
                $linha = '( )NÃO';
                $designer->Cell(50,15,utf8_decode($linha),0,0,'L');
                $designer->Ln();
                $designer->SetX(50);
                $linha = 'Se sim por que?.................................................................................................................................................................';
                $designer->Cell(565,15,utf8_decode($linha),0,0,'L');
                $designer->Ln();
                $linha = '7. Você tem ( ) Fibromialgia ( ) Artrite ( ) Artrose ( ) Reumatismo';
                $designer->Cell(565,15,utf8_decode($linha),0,0,'L');
                $linha = '8. Nível de estudo no CAEB......................................................................................................................................................';
                $designer->Ln();
                $designer->Cell(565,15,utf8_decode($linha),0,0,'L');
                $data = date('d/m/Y');
                $linha = 'Declaro estar ciente destas informações: Assinatura: _________________________________________Data '.$data;
                $designer->Ln();
                $designer->Cell(565,15,utf8_decode($linha),0,0,'L');
                $designer->Ln();
                $linha = 'Nossa Missão: ATRAVÉS DO AMOR OBTER A CURA.';
                $designer->Ln();
                $designer->SetFont('Arial','B',12); 
                $designer->Cell(565,15,utf8_decode($linha),0,0,'C');
                
                $designer->AddPage();
                $linha = 'Parte de trás da ficha - deverá ser preenchido pelo Atendente Fraterno';
                $designer->Ln(); 
                $designer->Cell(565,15,utf8_decode($linha),0,0,'L');
                $designer->Ln(); 
                $designer->SetFont('Arial','',12);
                $linha = 'OBSERVAÇÕES';
                $designer->Ln(); 
                $designer->Cell(565,15,utf8_decode($linha),0,0,'L'); 
                $linha = '.................................................................................................................................................................';
                $designer->Ln();
                $designer->Ln(); 
                $designer->Cell(565,15,utf8_decode($linha),0,0,'L');
                $linha = '.................................................................................................................................................................';
                $designer->Ln();
                $designer->Ln(); 
                $designer->Cell(565,15,utf8_decode($linha),0,0,'L');
                $linha = '.................................................................................................................................................................';
                $designer->Ln();
                $designer->Ln(); 
                $designer->Cell(565,15,utf8_decode($linha),0,0,'L'); 
                $linha = 'Terapias indicadas:';
                $designer->Ln();
                $designer->Cell(565,15,utf8_decode($linha),0,0,'L');
                $designer->Ln(); 
                $designer->SetFont('Arial','B',10);
                $linha = 'TERAPIA';
                $designer->Cell(220,15,utf8_decode($linha),1,0,'L');
                $linha = 'Quant.';
                $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                $linha = 'TERAPIA';
                $designer->Cell(220,15,utf8_decode($linha),1,0,'L');
                $linha = 'Quant.';
                $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                $designer->SetFont('Arial','',10);
                $designer->Ln();
                $linha = 'Atendimento Profissional';
                $designer->Cell(220,15,utf8_decode($linha),1,0,'L');
                $linha = '';
                $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                $linha = 'Palestras';
                $designer->Cell(220,15,utf8_decode($linha),1,0,'L');
                $linha = '';
                $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                $designer->Ln();
                $linha = 'Apoio Terapêutico';
                $designer->Cell(220,15,utf8_decode($linha),1,0,'L');
                $linha = '';
                $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                $linha = 'Passe de Câmara';
                $designer->Cell(220,15,utf8_decode($linha),1,0,'L');
                $linha = '';
                $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                $designer->Ln();
                $linha = 'Bio Magnética';
                $designer->Cell(220,15,utf8_decode($linha),1,0,'L');
                $linha = '';
                $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                $linha = 'Quântica Planetária';
                $designer->Cell(220,15,utf8_decode($linha),1,0,'L');
                $linha = '';
                $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                $designer->Ln();
                $linha = 'Cristal Inca';
                $designer->Cell(220,15,utf8_decode($linha),1,0,'L');
                $linha = '';
                $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                $linha = 'TM - Terapia Magnética';
                $designer->Cell(220,15,utf8_decode($linha),1,0,'L');
                $linha = '';
                $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                $designer->Ln();
                $linha = 'Energética';
                $designer->Cell(220,15,utf8_decode($linha),1,0,'L');
                $linha = '';
                $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                $linha = 'Tratamento Físico';
                $designer->Cell(220,15,utf8_decode($linha),1,0,'L');
                $linha = '';
                $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                $designer->Ln();
                $linha = 'Florais';
                $designer->Cell(220,15,utf8_decode($linha),1,0,'L');
                $linha = '';
                $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                $linha = 'Triagem Caulim';
                $designer->Cell(220,15,utf8_decode($linha),1,0,'L');
                $linha = '';
                $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                $designer->Ln();
                $linha = 'Genérica';
                $designer->Cell(220,15,utf8_decode($linha),1,0,'L');
                $linha = '';
                $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                $linha = 'Triagem Cirurgia';
                $designer->Cell(220,15,utf8_decode($linha),1,0,'L');
                $linha = '';
                $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                $designer->Ln();
                $linha = 'Gira da Demanda';
                $designer->Cell(220,15,utf8_decode($linha),1,0,'L');
                $linha = '';
                $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                $linha = 'Universal';
                $designer->Cell(220,15,utf8_decode($linha),1,0,'L');
                $linha = '';
                $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                $designer->Ln();
                $linha = 'Grupo Charrua';
                $designer->Cell(220,15,utf8_decode($linha),1,0,'L');
                $linha = '';
                $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                $linha = 'Vibracional';
                $designer->Cell(220,15,utf8_decode($linha),1,0,'L');
                $linha = '';
                $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                $designer->Ln();
                $linha = 'Holoterapia';
                $designer->Cell(220,15,utf8_decode($linha),1,0,'L');
                $linha = '';
                $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                $linha = 'Zapper';
                $designer->Cell(220,15,utf8_decode($linha),1,0,'L');
                $linha = '';
                $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                $designer->Ln();
                $linha = 'Meridianoterapia';
                $designer->Cell(220,15,utf8_decode($linha),1,0,'L');
                $linha = '';
                $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                $linha = '';
                $designer->Cell(220,15,utf8_decode($linha),1,0,'L');
                $linha = '';
                $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                $designer->Ln();
                $linha = '____________________________________';
                $designer->Ln();
                $designer->Cell(250,15,utf8_decode($linha),0,0,'C');
                $linha = 'Data '.$data;
                $designer->SetX(275);
                $designer->Cell(275,15,utf8_decode($linha),0,0,'C');
                $designer->SetFont('Arial','B',10);
                $linha = 'Nome e Assinatura do Atendente Fraterno';
                $designer->Ln();
                $designer->Cell(250,15,utf8_decode($linha),0,0,'C');
                $designer->SetFont('Arial','',10);
                $designer->Ln();
                $linha = '=============================================================================================';
                $designer->Ln();
                $designer->Cell(565,15,utf8_decode($linha),0,0,'L');
                $designer->Ln();
                $designer->SetFont('Arial','B',12); 
                $linha = 'Parte de trás da ficha - deverá ser preenchido pelo Atendente Fraterno';
                $designer->Ln(); 
                $designer->Cell(565,15,utf8_decode($linha),0,0,'L');
                $designer->Ln(); 
                $designer->SetFont('Arial','',12);
                $linha = 'OBSERVAÇÕES';
                $designer->Ln(); 
                $designer->Cell(565,15,utf8_decode($linha),0,0,'L'); 
                $linha = '.................................................................................................................................................................';
                $designer->Ln();
                $designer->Ln(); 
                $designer->Cell(565,15,utf8_decode($linha),0,0,'L');
                $linha = '.................................................................................................................................................................';
                $designer->Ln();
                $designer->Ln(); 
                $designer->Cell(565,15,utf8_decode($linha),0,0,'L');
                $linha = '.................................................................................................................................................................';
                $designer->Ln();
                $designer->Ln(); 
                $designer->Cell(565,15,utf8_decode($linha),0,0,'L'); 
                $linha = 'Terapias indicadas:';
                $designer->Ln();
                $designer->Cell(565,15,utf8_decode($linha),0,0,'L');
                $designer->Ln(); 
                $designer->SetFont('Arial','B',10);
                $linha = 'TERAPIA';
                $designer->Cell(220,15,utf8_decode($linha),1,0,'L');
                $linha = 'Quant.';
                $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                $linha = 'TERAPIA';
                $designer->Cell(220,15,utf8_decode($linha),1,0,'L');
                $linha = 'Quant.';
                $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                $designer->SetFont('Arial','',10);
                $designer->Ln();
                $linha = 'Atendimento Profissional';
                $designer->Cell(220,15,utf8_decode($linha),1,0,'L');
                $linha = '';
                $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                $linha = 'Palestras';
                $designer->Cell(220,15,utf8_decode($linha),1,0,'L');
                $linha = '';
                $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                $designer->Ln();
                $linha = 'Apoio Terapêutico';
                $designer->Cell(220,15,utf8_decode($linha),1,0,'L');
                $linha = '';
                $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                $linha = 'Passe de Câmara';
                $designer->Cell(220,15,utf8_decode($linha),1,0,'L');
                $linha = '';
                $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                $designer->Ln();
                $linha = 'Bio Magnética';
                $designer->Cell(220,15,utf8_decode($linha),1,0,'L');
                $linha = '';
                $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                $linha = 'Quântica Planetária';
                $designer->Cell(220,15,utf8_decode($linha),1,0,'L');
                $linha = '';
                $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                $designer->Ln();
                $linha = 'Cristal Inca';
                $designer->Cell(220,15,utf8_decode($linha),1,0,'L');
                $linha = '';
                $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                $linha = 'TM - Terapia Magnética';
                $designer->Cell(220,15,utf8_decode($linha),1,0,'L');
                $linha = '';
                $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                $designer->Ln();
                $linha = 'Energética';
                $designer->Cell(220,15,utf8_decode($linha),1,0,'L');
                $linha = '';
                $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                $linha = 'Tratamento Físico';
                $designer->Cell(220,15,utf8_decode($linha),1,0,'L');
                $linha = '';
                $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                $designer->Ln();
                $linha = 'Florais';
                $designer->Cell(220,15,utf8_decode($linha),1,0,'L');
                $linha = '';
                $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                $linha = 'Triagem Caulim';
                $designer->Cell(220,15,utf8_decode($linha),1,0,'L');
                $linha = '';
                $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                $designer->Ln();
                $linha = 'Genérica';
                $designer->Cell(220,15,utf8_decode($linha),1,0,'L');
                $linha = '';
                $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                $linha = 'Triagem Cirurgia';
                $designer->Cell(220,15,utf8_decode($linha),1,0,'L');
                $linha = '';
                $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                $designer->Ln();
                $linha = 'Gira da Demanda';
                $designer->Cell(220,15,utf8_decode($linha),1,0,'L');
                $linha = '';
                $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                $linha = 'Universal';
                $designer->Cell(220,15,utf8_decode($linha),1,0,'L');
                $linha = '';
                $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                $designer->Ln();
                $linha = 'Grupo Charrua';
                $designer->Cell(220,15,utf8_decode($linha),1,0,'L');
                $linha = '';
                $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                $linha = 'Vibracional';
                $designer->Cell(220,15,utf8_decode($linha),1,0,'L');
                $linha = '';
                $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                $designer->Ln();
                $linha = 'Holoterapia';
                $designer->Cell(220,15,utf8_decode($linha),1,0,'L');
                $linha = '';
                $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                $linha = 'Zapper';
                $designer->Cell(220,15,utf8_decode($linha),1,0,'L');
                $linha = '';
                $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                $designer->Ln();
                $linha = 'Meridianoterapia';
                $designer->Cell(220,15,utf8_decode($linha),1,0,'L');
                $linha = '';
                $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                $linha = '';
                $designer->Cell(220,15,utf8_decode($linha),1,0,'L');
                $linha = '';
                $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                $designer->Ln();
                $linha = '____________________________________';
                $designer->Ln();
                $designer->Cell(250,15,utf8_decode($linha),0,0,'C');
                $linha = 'Data '.$data;
                $designer->SetX(275);
                $designer->Cell(275,15,utf8_decode($linha),0,0,'C');
                $designer->SetFont('Arial','B',10);
                $linha = 'Nome e Assinatura do Atendente Fraterno';
                $designer->Ln();
                $designer->Cell(250,15,utf8_decode($linha),0,0,'C');
            }
            else
            {   
                $linha = 'Ficha do Paciente';
                $designer->Cell(565,15,utf8_decode($linha),0,0,'C');
                $designer->Ln();
                $designer->Ln();
                $designer->Ln();
                $designer->SetFont('Arial','B',10);
                $linha = 'Paciente preencher com LETRA DE FORMA (legível)';
                $designer->Cell(565,15,utf8_decode($linha),0,0,'L');
                $designer->SetFont('Arial','',10);
                if ($person->birthdate)
                {
                    $birthdate=explode('-',$person->birthdate);
                    $date=date('d/m/Y');
                    $date=explode('/',$date);
                    
                    $anos=$date[2]-$birthdate[0];
                    
                    if($birthdate[1] > $date[1])
                    {
                        $anos = $anos - 1;
                    }
                    
                    if($birthdate[1] == $date[1])
                    {
                        if($birthdate[2] <= $date[0])
                        {
                            
                        }
                        else
                        {
                            $anos = $anos - 1;
                        }
                    }
                    
                    if ($birthdate[1] < $date[1])
                    {
                        
                    }
                    
                    $birthdate = ' '.$birthdate[2].' / '.$birthdate[1].' / '.$birthdate[0].' ';
                    $age = $anos . ' anos';
                }
                else
                {
                    $birthdate = '....../....../........';
                    $age = '...........';
                }
                $designer->Ln();
                $linha = 'Nome: '.$person->name;
                $designer->Cell(370,15,utf8_decode($linha),0,0,'L');
                $designer->setX(375);
                $linha =  'Data Nas: '.$birthdate;
                $designer->Cell(100,15,utf8_decode($linha),0,0,'L');
                $designer->SetX(490);
                $linha = ' Idade: '.$age;
                $designer->Cell(100,15,utf8_decode($linha),0,0,'L');
                $designer->Ln();
                $linha = 'Estado Civil: '.$person->maritalstatus;
                $designer->Cell(150,15,utf8_decode($linha),0,0,'L');
                $designer->SetX(155);
                $linha = ' Tel Celular: '.$person->celphone;
                $designer->Cell(190,15,utf8_decode($linha),0,0,'L');
                $designer->SetX(350);
                $linha = ' Tel Fixo: '.$person->phone;
                $designer->Cell(190,15,utf8_decode($linha),0,0,'L');
                $designer->Ln();
                $linha = 'Email: '.$person->email;
                $designer->Cell(565,15,utf8_decode($linha),0,0,'L'); 
                $designer->Ln();
                $linha = 'Endereço: '.$person->address;
                $designer->Cell(450,15,utf8_decode($linha),0,0,'L'); 
                $designer->SetX(455);
                $linha = ' CEP: '.$person->zip;
                $designer->Cell(100,15,utf8_decode($linha),0,0,'L');  
                $designer->Ln();                
                $linha = ' Bairro: '.$person->neighborhood;
                $designer->Cell(220,15,utf8_decode($linha),0,0,'L');
                $designer->SetX(225);
                $linha = ' Cidade: '.$person->city;
                $designer->Cell(230,15,utf8_decode($linha),0,0,'L');
                $designer->SetX(455);
                $linha = ' Estado: '.$person->state_id;
                $designer->Cell(10,15,utf8_decode($linha),0,0,'L');
                $designer->Ln();  
                $linha = ' RG: '.$person->rg;
                $designer->Cell(250,15,utf8_decode($linha),0,0,'L');
                $designer->SetX(255);
                $linha = ' CPF: '.$person->cpf;
                $designer->Cell(250,15,utf8_decode($linha),0,0,'L');
                $designer->Ln();
                $y = $designer->GetY();
                $designer->Line(35,$y,565,$y);
                $designer->Ln();
                $linha = '1. O que te motivou a procurar o CAEB?...............................................................................................................................';
                $designer->Cell(565,15,utf8_decode($linha),0,0,'L');
                $designer->Ln();
                $designer->Ln();
                $linha = '................................................................................................................................................................................................';
                $designer->Cell(565,15,utf8_decode($linha),0,0,'L');
                $designer->Ln();
                $linha = '2. Qual o problema que lhe traz ao tratamento CAEB?';
                $designer->Cell(565,15,utf8_decode($linha),0,0,'L');
                $designer->Ln();
                $designer->Ln();
                $linha = '................................................................................................................................................................................................';
                $designer->Cell(565,15,utf8_decode($linha),0,0,'L');
                $designer->Ln();
                $linha = '3. O que você espera do tratamento no CAEB?.....................................................................................................................';
                $designer->Cell(565,15,utf8_decode($linha),0,0,'L');
                $designer->Ln();
                $designer->Ln();
                $linha = '................................................................................................................................................................................................';
                $designer->Cell(565,15,utf8_decode($linha),0,0,'L');
                $designer->Ln();
                $linha = '4. Você conhece da doutrina espírita?';
                $designer->Cell(250,15,utf8_decode($linha),0,0,'L');
                $designer->SetX(255);
                $linha = '( ) Não ( ) Sim';
                $designer->Cell(200,15,utf8_decode($linha),0,0,'L');
                $designer->Ln();
                $linha = '5. Você já fez algum tratamento espiritual?';
                $designer->Cell(250,15,utf8_decode($linha),0,0,'L');
                $designer->SetX(255);
                $linha = '( ) Não ( ) Sim';
                $designer->Cell(200,15,utf8_decode($linha),0,0,'L');
                $designer->Ln();
                $linha = '6. Tem dores de cabeça?';
                $designer->Cell(250,15,utf8_decode($linha),0,0,'L');
                $designer->SetX(255);
                $linha = '( ) Sim. Quantas vezes por mês? ..........vezes.';
                $designer->Cell(200,15,utf8_decode($linha),0,0,'L');
                $designer->Ln();
                $linha = '7. Usa medicamento controlado?';
                $designer->Cell(250,15,utf8_decode($linha),0,0,'L');
                $designer->SetX(255);
                $linha = '( ) Não ( ) Sim Qual? .............................................................................';
                $designer->Cell(200,15,utf8_decode($linha),0,0,'L');
                $designer->Ln();
                $linha = '7. Usa medicamento controlado?';
                $designer->Cell(250,15,utf8_decode($linha),0,0,'L');
                $designer->SetX(255);
                $linha = '( ) Não ( ) Sim Qual? .............................................................................';
                $designer->Cell(200,15,utf8_decode($linha),0,0,'L');
                $designer->Ln();
                $linha = '8. Você está tendo acompanhamento médico?';
                $designer->Cell(250,15,utf8_decode($linha),0,0,'L');
                $designer->SetX(255);
                $linha = '( ) Não ( ) Sim';
                $designer->Cell(200,15,utf8_decode($linha),0,0,'L');
                $designer->Ln();
                $linha = '9. Alguma vez pensou em desistir de viver?';
                $designer->Cell(250,15,utf8_decode($linha),0,0,'L');
                $designer->SetX(255);
                $linha = '( ) Não ( ) Sim Porque? .........................................................................';
                $designer->Cell(200,15,utf8_decode($linha),0,0,'L');
                $designer->Ln();
                $designer->Ln();
                $linha = '................................................................................................................................................................................................';
                $designer->Cell(565,15,utf8_decode($linha),0,0,'L');
                $designer->Ln();
                $linha = '10. Tem pressentimentos?';
                $designer->Cell(250,15,utf8_decode($linha),0,0,'L');
                $designer->SetX(255);
                $linha = '( ) Não ( ) Sim';
                $designer->Cell(200,15,utf8_decode($linha),0,0,'L');
                $designer->Ln();
                $linha = '11. Você vê vultos?';
                $designer->Cell(250,15,utf8_decode($linha),0,0,'L');
                $designer->SetX(255);
                $linha = '( ) Não ( ) Sim';
                $designer->Cell(200,15,utf8_decode($linha),0,0,'L');
                $designer->Ln();
                $linha = '12. Ouve vozes?';
                $designer->Cell(250,15,utf8_decode($linha),0,0,'L');
                $designer->SetX(255);
                $linha = '( ) Não ( ) Sim';
                $designer->Cell(200,15,utf8_decode($linha),0,0,'L');
                $designer->Ln();
                $linha = '13. Sente alguém ao seu lado?';
                $designer->Cell(250,15,utf8_decode($linha),0,0,'L');
                $designer->SetX(255);
                $linha = '( ) Não ( ) Sim';
                $designer->Cell(200,15,utf8_decode($linha),0,0,'L');
                $designer->Ln();
                $linha = '14. Sente arrepios?';
                $designer->Cell(250,15,utf8_decode($linha),0,0,'L');
                $designer->SetX(255);
                $linha = '( ) Não ( ) Sim';
                $designer->Cell(200,15,utf8_decode($linha),0,0,'L');
                $designer->Ln();
                $linha = '15. Você muda de humor facilmente?';
                $designer->Cell(250,15,utf8_decode($linha),0,0,'L');
                $designer->SetX(255);
                $linha = '( ) Não ( ) Sim';
                $designer->Cell(200,15,utf8_decode($linha),0,0,'L');
                $designer->Ln();
                $linha = '16. Você tem:';
                $designer->Cell(250,15,utf8_decode($linha),0,0,'L');
                $designer->SetX(255);
                $linha = '( ) Fibromialgia  ( ) Artrite ( ) Artrose ( ) Reumatismo';
                $designer->Cell(200,15,utf8_decode($linha),0,0,'L');
                $designer->Ln();
                $linha = '17. Se for mulher, favor informar se está grávida.';
                $designer->Cell(250,15,utf8_decode($linha),0,0,'L');
                $designer->SetX(255);
                $linha = '( ) Não ( ) Sim';
                $designer->Cell(200,15,utf8_decode($linha),0,0,'L');
                $designer->Ln();
                $designer->Ln();
                $linha = 'Aos pacientes com doenças físicas trazer fotocópia do laudo médico, laudo(s) de exame(s) de imagens de seu médico, sendo eles realizado menos de 2 anos (sendo RECENTES).';
                $designer->MultiCell(555,15,utf8_decode($linha),0,'L');
                $linha = 'IMPORTANTE: Não fazemos diagnóstico médico e o acompanhamento com o seu médico deve continuar.';
                $designer->MultiCell(555,15,utf8_decode($linha),0,'L');
                $linha = 'Este documento é um documento sigiloso onde só o Atendente Fraterno e a Direção da Casa tomará conhecimento e encaminhará aos tratamentos da casa.';
                $designer->MultiCell(555,15,utf8_decode($linha),0,'L');
                $designer->Ln();
                $linha = 'Declaro estar ciente destas informações: ';
                $designer->Cell(210,15,utf8_decode($linha),0,'L');
                $designer->SetX(215);
                $designer->SetFont('Arial','B',10);
                $linha = 'Assinatura: ';
                $designer->Cell(100,15,utf8_decode($linha),0,'L');
                $designer->SetX(270);
                $designer->SetFont('Arial','',10);
                $linha = '__________________________________Data ____/____/20____';
                $designer->Cell(250,15,$linha,0,'L');
                $designer->Ln();
                $designer->Ln();
                $designer->SetFont('Arial','B',10);
                $linha = 'Nossa Missão: Através do amor obter a CURA. Seja bem-vindo!';
                $designer->MultiCell(555,15,utf8_decode($linha),0,'C');
                
                $designer->AddPage();
                $designer->Image($image_file, 30, 30, 100, 50, 'PNG', '', 'T', FALSE, 300, '', FALSE, FALSE, 0, FALSE, FALSE, FALSE);
                $designer->setY(45);
                $designer->SetFont('Arial','B',12);    
                $designer->Cell(565,15,utf8_decode('ATENDIMENTO FRATERNO'),0,0,'C');
                $designer->Ln();
                $designer->Cell(565,15,utf8_decode('Ficha do Paciente'),0,0,'C');
                $designer->Ln();
                $designer->Ln();
                $designer->Ln();
                $designer->SetFont('Arial','B',10);
                $linha = 'Parte de trás da ficha - deverá ser preenchido pelo Atendente Fraterno';
                $designer->Cell(565,15,utf8_decode($linha),0,0,'L');
                $designer->Ln();
                $designer->Ln();
                $designer->SetFont('Arial','',10);
                $linha = '18. Observações:';
                $designer->Cell(565,15,utf8_decode($linha),0,'L');
                $designer->Ln();
                $designer->Ln();
                $linha = '................................................................................................................................................................................................';
                $designer->Cell(565,15,utf8_decode($linha),0,0,'L');
                $designer->Ln();
                $designer->Ln();
                $linha = '................................................................................................................................................................................................';
                $designer->Cell(565,15,utf8_decode($linha),0,0,'L');
                $designer->Ln();
                $designer->Ln();
                $linha = '................................................................................................................................................................................................';
                $designer->Cell(565,15,utf8_decode($linha),0,0,'L');
                $designer->Ln();
                $designer->Ln();
                $linha = '................................................................................................................................................................................................';
                $designer->Cell(565,15,utf8_decode($linha),0,0,'L');
                $designer->Ln();
                $designer->Ln();
                $linha = '................................................................................................................................................................................................';
                $designer->Cell(565,15,utf8_decode($linha),0,0,'L');
                $designer->Ln();
                $designer->Ln();
                $linha = '19. Terapias indicadas:';
                $designer->Cell(565,15,utf8_decode($linha),0,0,'L');
                $designer->Ln();
                $designer->SetFont('Arial','B',10);
                $linha = 'TERAPIA';
                $designer->Cell(130,15,utf8_decode($linha),1,0,'L');
                $linha = 'Quant.';
                $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                $linha = 'Data';
                $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                $linha = 'Data';
                $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                $linha = 'Data';
                $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                $linha = 'Data';
                $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                $linha = 'Data';
                $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                $linha = 'Data';
                $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                $linha = 'Data';
                $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                $linha = 'Data';
                $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                $designer->Ln();
                $designer->SetFont('Arial','',10);
                $linha = 'Atendimento Profissional';
                $designer->Cell(130,15,utf8_decode($linha),1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Ln();
                $linha = 'Biomagnética';
                $designer->Cell(130,15,utf8_decode($linha),1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Ln();
                $linha = 'Cristal Inca';
                $designer->Cell(130,15,utf8_decode($linha),1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Ln();
                $linha = 'Energética';
                $designer->Cell(130,15,utf8_decode($linha),1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Ln();
                $linha = 'Genérica';
                $designer->Cell(130,15,utf8_decode($linha),1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Ln();
                $linha = 'Grupo Charrua';
                $designer->Cell(130,15,utf8_decode($linha),1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Ln();
                $linha = 'Holoterapia';
                $designer->Cell(130,15,utf8_decode($linha),1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Ln();
                $linha = 'Meridianoterapia';
                $designer->Cell(130,15,utf8_decode($linha),1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Ln();
                $linha = 'Palestras';
                $designer->Cell(130,15,utf8_decode($linha),1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Ln();
                $linha = 'Passe de Câmara';
                $designer->Cell(130,15,utf8_decode($linha),1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Ln();
                $linha = 'Quântica Planetária';
                $designer->Cell(130,15,utf8_decode($linha),1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Ln();
                $linha = 'TM - Terapia Magnética';
                $designer->Cell(130,15,utf8_decode($linha),1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Ln();
                $linha = 'Tratamento Físico';
                $designer->Cell(130,15,utf8_decode($linha),1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Ln();
                $linha = 'Triagem Caulim';
                $designer->Cell(130,15,utf8_decode($linha),1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Ln();
                $linha = 'Triagem Cirurgia';
                $designer->Cell(130,15,utf8_decode($linha),1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Ln();
                $linha = 'Universal';
                $designer->Cell(130,15,utf8_decode($linha),1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Ln();
                $linha = 'Vibracional';
                $designer->Cell(130,15,utf8_decode($linha),1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Ln();
                $linha = 'Zapper';
                $designer->Cell(130,15,utf8_decode($linha),1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Ln();
                $linha = '';
                $designer->Cell(130,15,utf8_decode($linha),1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Cell(45,15,'',1,0,'L');
                $designer->Ln();
                $designer->Ln();
                $linha = '20. Floral indicado:';
                $designer->Cell(150,15,utf8_decode($linha),0,0,'L');
                $designer->Ln();
                $designer->SetFont('Arial','B',10);
                $linha = 'Floral 1';
                $designer->Cell(120,15,utf8_decode($linha),1,0,'L');
                $linha = 'Floral 2 (se precisar)';
                $designer->Cell(120,15,utf8_decode($linha),1,0,'L');
                $designer->Ln();
                $designer->Cell(120,15,'',1,0,'L');
                $designer->Cell(120,15,'',1,0,'L');
                $designer->Ln();
                $designer->Cell(120,15,'',1,0,'L');
                $designer->Cell(120,15,'',1,0,'L');
                $designer->Ln();
                $designer->Cell(120,15,'',1,0,'L');
                $designer->Cell(120,15,'',1,0,'L');
                $designer->Ln();
                $designer->Cell(120,15,'',1,0,'L');
                $designer->Cell(120,15,'',1,0,'L');
                $designer->Ln();
                $designer->Cell(120,15,'',1,0,'L');
                $designer->Cell(120,15,'',1,0,'L');
                $designer->Ln();
                $designer->Cell(120,15,'',1,0,'L');
                $designer->Cell(120,15,'',1,0,'L');
                $designer->Ln();
                $designer->Ln();
                $designer->Cell(565,15,'____________________________________',0,0,'C');
                $designer->Ln();
                $linha = 'Nome e Assinatura do Atendente Fraterno';
                $designer->Cell(565,15,utf8_decode($linha),0,0,'C');
                $designer->Ln();
                $linha = 'Data '.date('d/m/Y');
                $designer->Cell(565,15,utf8_decode($linha),0,0,'C');
            }
            
            $file = 'app/output/FichaCadastral.pdf';
            
            if (!file_exists($file) OR is_writable($file))
            {
                 $designer->save($file);
                 parent::openFile($file);
            }
            else
            {
                throw new Exception(_t('Permission denied') . ': ' . $file);
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', '<b>Error</b> ' . $e->getMessage());
        }
    }
    
    public function onInputDialog($param)
    {        
        $form = new TQuickForm('input_form');
        $form->style = 'padding:20px';
        
        $script = new TElement('script');
        $script->type = 'text/javascript';
        $script->add('
            $(document).ready(function() {
                $("input[name=atendente_id]").focus();
            });
        ');
        parent::add($script);
        
        $criteria = new TCriteria;
        $criteria->add(new TFilter('assignmenter', '=', 'S'));
        $criteria->add(new TFilter('status', '=', 'A'));
        $criteria->setProperty('order', 'name');
        $atendente_id = new TDBSeekButton('atendente_id',TSession::getValue('unitbank'),'input_form','Person','name','atendente_id','atendente',$criteria);
        $atendente = new TEntry('atendente');
        $retorno = new TDate('retorno');
        $key = new THidden('key');
        
        $form->addQuickFields('Nome Atendente Fraterno', array($atendente_id, $atendente));
        $form->addQuickField('Retornar em', $retorno, 100);
        $form->addQuickField('', $key);
        
        $atendente_id->setSize(60);
        $atendente->setSize(250);
        $retorno->setMask('dd/mm/yyyy');
        $key->setValue($param['key']);
        
        $form->addQuickAction('Confirmar', new TAction(array($this, 'onPrintFichaAgendamentos')), 'fa:check-circle green');
        
        // show the input dialog
        new TInputDialog('Informar', $form);
    }
    
    public function onPrintFichaAgendamentos($param)
    {
        $key=$param['key'];
        
        try
        {                
            $designer = new TPDFDesigner;
            $designer->generate();
            $designer->setMargins(30,15); 
            $designer->SetXY(30,30); 
            
            TTransaction::open('caeb');
            $conn = TTransaction::get();
            
            $person = new Person($key);
            
            if ($person->assignmenter == 'S')
            {
                for ($i=0;$i<=1;$i++)
                {
                    $designer->SetFont('Arial','B',10);
                    $linha = 'FICHA DE AGENDAMENTO TERAPIA TAREFEIRO - CAEB';
                    $designer->Cell(300,15,utf8_decode($linha),0,0,'L');
                    $designer->SetFont('Arial','',10);
                    $designer->SetX(305);
                    $linha = '(obrigatório a apresentação deste para retirada de senha)';
                    $designer->Cell(565,15,utf8_decode($linha),0,0,'L');  
                    $designer->Ln();
                    $linha = 'Nome do Tarefeiro: '.$person->name;
                    $designer->Cell(455,15,utf8_decode($linha),0,0,'L');
                    $data = date('d/m/Y');
                    $linha = 'Data: '.$data;
                    $designer->Cell(150,15,utf8_decode($linha),0,0,'L');
                    $designer->Ln();
                    $linha = 'Nome Atendente Fraterno: '.$param['atendente'];
                    $designer->Cell(320,15,utf8_decode($linha),0,0,'L');
                    $linha = 'Retornar em: '.$param['retorno'].' Dia da semana';
                    $designer->Cell(230,15,utf8_decode($linha),0,0,'L');
                    $designer->SetFont('Arial','B',10);
                    $designer->Ln();
                    $linha = 'TERAPIA';
                    $designer->Cell(130,15,utf8_decode($linha),1,0,'L');
                    $linha = 'Quant.';
                    $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                    $linha = 'Data';
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = 'Data';
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = 'Data';
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = 'Data';
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = 'Data';
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = 'Data';
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = 'Data';
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = 'Data';
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');  
                    $designer->SetFont('Arial','',8);
                    $designer->Ln();            
                    
                    $query = "select start_time from calendar_event c left join terapy t on t.id = terapy_id 
                          where person_id = ".$key." and name like '%profissional%' and start_time >= '".date('Y-m-d')."'
                          order by start_time";
                    $results = $conn->query($query);
                        
                    if ($results)
                    {
                        $atendimentos = array();
                        $quantidade=0;
                        foreach ($results as $result)
                    	    {
                    	        $atendimentos[] = $result['start_time'];
                    	        $quantidade++;
                    	    }
                    }
                    
                    if ($quantidade==0)
                    {
                        $quantidade=null;
                    }
                    
                    $linha = 'Atendimento Profissional';
                    $designer->Cell(130,15,utf8_decode($linha),1,0,'L');
                    $linha = $quantidade;
                    $designer->Cell(50,15,utf8_decode($linha),1,0,'L');  
                    $linha = TDate::date2br($atendimentos[0]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[1]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[2]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[3]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[4]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[5]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[6]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[7]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $designer->Ln();
                    
                    $query = "select start_time from calendar_event c left join terapy t on t.id = terapy_id 
                          where person_id = ".$key." and name like '%biomagnética%' and start_time >= '".date('Y-m-d')."'
                          order by start_time";
                    $results = $conn->query($query);
                        
                    if ($results)
                    {
                        $atendimentos = array();
                        $quantidade=0;
                        foreach ($results as $result)
                    	    {
                    	        $atendimentos[] = $result['start_time'];
                    	        $quantidade++;
                    	    }
                    }
                    
                    if ($quantidade==0)
                    {
                        $quantidade=null;
                    }
                    
                    $linha = 'Biomagnética';
                    $designer->Cell(130,15,utf8_decode($linha),1,0,'L'); 
                    $linha = $quantidade;
                    $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[0]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[1]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[2]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[3]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[4]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[5]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[6]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[7]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $designer->Ln();
                    
                    $query = "select start_time from calendar_event c left join terapy t on t.id = terapy_id 
                          where person_id = ".$key." and name like '%inca%' and start_time >= '".date('Y-m-d')."'
                          order by start_time";
                    $results = $conn->query($query);
                        
                    if ($results)
                    {
                        $atendimentos = array();
                        $quantidade=0;
                        foreach ($results as $result)
                    	    {
                    	        $atendimentos[] = $result['start_time'];
                    	        $quantidade++;
                    	    }
                    }
                    
                    if ($quantidade==0)
                    {
                        $quantidade=null;
                    }
                    
                    $linha = 'Cristal Inca';
                    $designer->Cell(130,15,utf8_decode($linha),1,0,'L'); 
                    $linha = $quantidade;
                    $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[0]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[1]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[2]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[3]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[4]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[5]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[6]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[7]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $designer->Ln();
                    
                    $query = "select start_time from calendar_event c left join terapy t on t.id = terapy_id 
                          where person_id = ".$key." and name like '%energética%' and start_time >= '".date('Y-m-d')."'
                          order by start_time";
                    $results = $conn->query($query);
                        
                    if ($results)
                    {
                        $atendimentos = array();
                        $quantidade=0;
                        foreach ($results as $result)
                    	    {
                    	        $atendimentos[] = $result['start_time'];
                    	        $quantidade++;
                    	    }
                    }
                    
                    if ($quantidade==0)
                    {
                        $quantidade=null;
                    }
                    
                    $linha = 'Energética';
                    $designer->Cell(130,15,utf8_decode($linha),1,0,'L'); 
                    $linha = $quantidade;
                    $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[0]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[1]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[2]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[3]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[4]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[5]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[6]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[7]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $designer->Ln();
                    
                    $query = "select start_time from calendar_event c left join terapy t on t.id = terapy_id 
                          where person_id = ".$key." and name like '%genérica%' and start_time >= '".date('Y-m-d')."'
                          order by start_time";
                    $results = $conn->query($query);
                        
                    if ($results)
                    {
                        $atendimentos = array();
                        $quantidade=0;
                        foreach ($results as $result)
                    	    {
                    	        $atendimentos[] = $result['start_time'];
                    	        $quantidade++;
                    	    }
                    }
                    
                    if ($quantidade==0)
                    {
                        $quantidade=null;
                    }
                    
                    $linha = 'Genérica';
                    $designer->Cell(130,15,utf8_decode($linha),1,0,'L'); 
                    $linha = $quantidade;
                    $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[0]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[1]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[2]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[3]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[4]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[5]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[6]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[7]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L'); 
                    $designer->Ln();
                    
                    $query = "select start_time from calendar_event c left join terapy t on t.id = terapy_id 
                          where person_id = ".$key." and name like '%charrua%' and start_time >= '".date('Y-m-d')."'
                          order by start_time";
                    $results = $conn->query($query);
                        
                    if ($results)
                    {
                        $atendimentos = array();
                        $quantidade=0;
                        foreach ($results as $result)
                    	    {
                    	        $atendimentos[] = $result['start_time'];
                    	        $quantidade++;
                    	    }
                    }
                    
                    if ($quantidade==0)
                    {
                        $quantidade=null;
                    }
                    
                    $linha = 'Grupo Charrua';
                    $designer->Cell(130,15,utf8_decode($linha),1,0,'L'); 
                    $linha = $quantidade;
                    $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[0]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[1]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[2]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[3]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[4]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[5]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[6]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[7]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $designer->Ln();
                    
                    $query = "select start_time from calendar_event c left join terapy t on t.id = terapy_id 
                          where person_id = ".$key." and name like '%holoterapia%' and start_time >= '".date('Y-m-d')."'
                          order by start_time";
                    $results = $conn->query($query);
                        
                    if ($results)
                    {
                        $atendimentos = array();
                        $quantidade=0;
                        foreach ($results as $result)
                    	    {
                    	        $atendimentos[] = $result['start_time'];
                    	        $quantidade++;
                    	    }
                    }
                    
                    if ($quantidade==0)
                    {
                        $quantidade=null;
                    }
                    
                    $linha = 'Holoterapia';
                    $designer->Cell(130,15,utf8_decode($linha),1,0,'L'); 
                    $linha = $quantidade;
                    $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[0]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[1]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[2]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[3]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[4]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[5]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[6]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[7]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $designer->Ln();
                    
                    $query = "select start_time from calendar_event c left join terapy t on t.id = terapy_id 
                          where person_id = ".$key." and name like '%meridianoterapia%' and start_time >= '".date('Y-m-d')."'
                          order by start_time";
                    $results = $conn->query($query);
                        
                    if ($results)
                    {
                        $atendimentos = array();
                        $quantidade=0;
                        foreach ($results as $result)
                    	    {
                    	        $atendimentos[] = $result['start_time'];
                    	        $quantidade++;
                    	    }
                    }
                    
                    if ($quantidade==0)
                    {
                        $quantidade=null;
                    }
                    
                    $linha = 'Meridianoterapia';
                    $designer->Cell(130,15,utf8_decode($linha),1,0,'L'); 
                    $linha = $quantidade;
                    $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[0]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[1]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[2]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[3]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[4]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[5]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[6]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[7]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L'); 
                    $designer->Ln();
                    
                    $query = "select start_time from calendar_event c left join terapy t on t.id = terapy_id 
                          where person_id = ".$key." and name like '%palestras%' and start_time >= '".date('Y-m-d')."'
                          order by start_time";
                    $results = $conn->query($query);
                        
                    if ($results)
                    {
                        $atendimentos = array();
                        $quantidade=0;
                        foreach ($results as $result)
                    	    {
                    	        $atendimentos[] = $result['start_time'];
                    	        $quantidade++;
                    	    }
                    }
                    
                    if ($quantidade==0)
                    {
                        $quantidade=null;
                    }
                    
                    $linha = 'Palestras';
                    $designer->Cell(130,15,utf8_decode($linha),1,0,'L'); 
                    $linha = $quantidade;
                    $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[0]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[1]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[2]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[3]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[4]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[5]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[6]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[7]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $designer->Ln();
                    
                    $query = "select start_time from calendar_event c left join terapy t on t.id = terapy_id 
                          where person_id = ".$key." and name like '%câmara%' and start_time >= '".date('Y-m-d')."'
                          order by start_time";
                    $results = $conn->query($query);
                        
                    if ($results)
                    {
                        $atendimentos = array();
                        $quantidade=0;
                        foreach ($results as $result)
                    	    {
                    	        $atendimentos[] = $result['start_time'];
                    	        $quantidade++;
                    	    }
                    }
                    
                    if ($quantidade==0)
                    {
                        $quantidade=null;
                    }
                    
                    $linha = 'Passe de Câmara';
                    $designer->Cell(130,15,utf8_decode($linha),1,0,'L'); 
                    $linha = $quantidade;
                    $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[0]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[1]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[2]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[3]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[4]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[5]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[6]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[7]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $designer->Ln();
                    
                    $query = "select start_time from calendar_event c left join terapy t on t.id = terapy_id 
                          where person_id = ".$key." and name like '%planetária%' and start_time >= '".date('Y-m-d')."'
                          order by start_time";
                    $results = $conn->query($query);
                        
                    if ($results)
                    {
                        $atendimentos = array();
                        $quantidade=0;
                        foreach ($results as $result)
                    	    {
                    	        $atendimentos[] = $result['start_time'];
                    	        $quantidade++;
                    	    }
                    }
                    
                    if ($quantidade==0)
                    {
                        $quantidade=null;
                    }
                    
                    $linha = 'Quântica Planetária';
                    $designer->Cell(130,15,utf8_decode($linha),1,0,'L'); 
                    $linha = $quantidade;
                    $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[0]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[1]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[2]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[3]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[4]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[5]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[6]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[7]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $designer->Ln();
                    
                    $query = "select start_time from calendar_event c left join terapy t on t.id = terapy_id 
                          where person_id = ".$key." and name like '%magnética%' and start_time >= '".date('Y-m-d')."'
                          order by start_time";
                    $results = $conn->query($query);
                        
                    if ($results)
                    {
                        $atendimentos = array();
                        $quantidade=0;
                        foreach ($results as $result)
                    	    {
                    	        $atendimentos[] = $result['start_time'];
                    	        $quantidade++;
                    	    }
                    }
                    
                    if ($quantidade==0)
                    {
                        $quantidade=null;
                    }
                    
                    $linha = 'TM - Terapia Magnética';
                    $designer->Cell(130,15,utf8_decode($linha),1,0,'L'); 
                    $linha = $quantidade;
                    $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[0]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[1]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[2]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[3]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[4]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[5]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[6]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[7]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $designer->Ln();
                    
                    $query = "select start_time from calendar_event c left join terapy t on t.id = terapy_id 
                          where person_id = ".$key." and name like '%físico%' and start_time >= '".date('Y-m-d')."'
                          order by start_time";
                    $results = $conn->query($query);
                        
                    if ($results)
                    {
                        $atendimentos = array();
                        $quantidade=0;
                        foreach ($results as $result)
                    	    {
                    	        $atendimentos[] = $result['start_time'];
                    	        $quantidade++;
                    	    }
                    }
                    
                    if ($quantidade==0)
                    {
                        $quantidade=null;
                    }
                    
                    $linha = 'Tratamento Físico';
                    $designer->Cell(130,15,utf8_decode($linha),1,0,'L'); 
                    $linha = $quantidade;
                    $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[0]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[1]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[2]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[3]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[4]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[5]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[6]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[7]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L'); 
                    $designer->Ln();
                    
                    $query = "select start_time from calendar_event c left join terapy t on t.id = terapy_id 
                          where person_id = ".$key." and name like '%caulim%' and start_time >= '".date('Y-m-d')."'
                          order by start_time";
                    $results = $conn->query($query);
                        
                    if ($results)
                    {
                        $atendimentos = array();
                        $quantidade=0;
                        foreach ($results as $result)
                    	    {
                    	        $atendimentos[] = $result['start_time'];
                    	        $quantidade++;
                    	    }
                    }
                    
                    if ($quantidade==0)
                    {
                        $quantidade=null;
                    }
                    
                    $linha = 'Triagem Caulim';
                    $designer->Cell(130,15,utf8_decode($linha),1,0,'L'); 
                    $linha = $quantidade;
                    $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[0]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[1]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[2]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[3]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[4]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[5]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[6]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[7]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $designer->Ln();
                    
                    $query = "select start_time from calendar_event c left join terapy t on t.id = terapy_id 
                          where person_id = ".$key." and name like '%cirurgia%' and start_time >= '".date('Y-m-d')."'
                          order by start_time";
                    $results = $conn->query($query);
                        
                    if ($results)
                    {
                        $atendimentos = array();
                        $quantidade=0;
                        foreach ($results as $result)
                    	    {
                    	        $atendimentos[] = $result['start_time'];
                    	        $quantidade++;
                    	    }
                    }
                    
                    if ($quantidade==0)
                    {
                        $quantidade=null;
                    }
                    
                    $linha = 'Triagem Cirurgia';
                    $designer->Cell(130,15,utf8_decode($linha),1,0,'L'); 
                    $linha = $quantidade;
                    $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[0]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[1]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[2]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[3]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[4]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[5]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[6]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[7]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $designer->Ln();
                    
                    $query = "select start_time from calendar_event c left join terapy t on t.id = terapy_id 
                          where person_id = ".$key." and name like '%universal%' and start_time >= '".date('Y-m-d')."'
                          order by start_time";
                    $results = $conn->query($query);
                        
                    if ($results)
                    {
                        $atendimentos = array();
                        $quantidade=0;
                        foreach ($results as $result)
                    	    {
                    	        $atendimentos[] = $result['start_time'];
                    	        $quantidade++;
                    	    }
                    }
                    
                    if ($quantidade==0)
                    {
                        $quantidade=null;
                    }
                    
                    $linha = 'Universal';
                    $designer->Cell(130,15,utf8_decode($linha),1,0,'L'); 
                    $linha = $quantidade;
                    $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[0]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[1]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[2]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[3]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[4]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[5]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[6]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[7]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $designer->Ln();
                    
                    $query = "select start_time from calendar_event c left join terapy t on t.id = terapy_id 
                          where person_id = ".$key." and name like '%vibracional%' and start_time >= '".date('Y-m-d')."'
                          order by start_time";
                    $results = $conn->query($query);
                        
                    if ($results)
                    {
                        $atendimentos = array();
                        $quantidade=0;
                        foreach ($results as $result)
                    	    {
                    	        $atendimentos[] = $result['start_time'];
                    	        $quantidade++;
                    	    }
                    }
                    
                    if ($quantidade==0)
                    {
                        $quantidade=null;
                    }
                    
                    $linha = 'Vibracional';
                    $designer->Cell(130,15,utf8_decode($linha),1,0,'L'); 
                    $linha = $quantidade;
                    $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[0]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[1]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[2]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[3]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[4]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[5]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[6]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[7]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $designer->Ln();
                    
                    $query = "select start_time from calendar_event c left join terapy t on t.id = terapy_id 
                          where person_id = ".$key." and name like '%zapper%' and start_time >= '".date('Y-m-d')."'
                          order by start_time";
                    $results = $conn->query($query);
                        
                    if ($results)
                    {
                        $atendimentos = array();
                        $quantidade=0;
                        foreach ($results as $result)
                    	    {
                    	        $atendimentos[] = $result['start_time'];
                    	        $quantidade++;
                    	    }
                    }
                    
                    if ($quantidade==0)
                    {
                        $quantidade=null;
                    }
                    
                    $linha = 'Zapper';
                    $designer->Cell(130,15,utf8_decode($linha),1,0,'L'); 
                    $linha = $quantidade;
                    $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[0]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[1]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[2]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[3]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[4]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[5]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[6]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[7]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $designer->SetFont('Arial','B',8);
                    $designer->Ln();
                    $linha = '1. Se você tiver agendamento para a Triagem Cirurgia é obrigatório CÓPIA DO LAUDO MÉDICO, sem a cópia não seria possível a análise.';
                    $designer->Cell(565,10,utf8_decode($linha),0,true,'L');
                    $designer->SetFont('Arial','',8);
                    $linha = '2. No CAEB não é feito diagnósticos médicos e o acompanhamento com o seu médico deve continuar.';
                    $designer->Cell(565,10,utf8_decode($linha),0,true,'L');
                    $linha = '3. Deverá comparecer no dia agendados para retirar a senha de atendimento que será distribuída por ordem de chegada.';
                    $designer->Cell(565,10,utf8_decode($linha),0,true,'L');
                    $linha = '4. Seja solidário de preferência para Idosos e pacientes muito debilitados.';
                    $designer->Cell(565,10,utf8_decode($linha),0,true,'L');
                    $linha = '5. Você deve comparecer aos tratamentos usando roupas claras.';
                    $designer->Cell(565,10,utf8_decode($linha),0,true,'L');
                    $linha = '6. Caso você não possa comparecer por favor ligue para desmarcar sua terapia 47-3275-0001';
                    $designer->Cell(565,10,utf8_decode($linha),0,true,'L');
                    $designer->Ln();
                    $designer->Ln();
                }
            }
            else
            {
                for ($i=0;$i<=1;$i++)
                {
                    $designer->SetFont('Arial','B',10);
                    $linha = 'FICHA DE AGENDAMENTO TERAPIA TAREFEIRO - CAEB';
                    $designer->Cell(300,15,utf8_decode($linha),0,0,'L');
                    $designer->SetFont('Arial','',10);
                    $designer->SetX(305);
                    $linha = '(obrigatório a apresentação deste para retirada de senha)';
                    $designer->Cell(565,15,utf8_decode($linha),0,0,'L');  
                    $designer->Ln();
                    $linha = 'Nome do Tarefeiro: '.$person->name;
                    $designer->Cell(455,15,utf8_decode($linha),0,0,'L');
                    $data = date('d/m/Y');
                    $linha = 'Data: '.$data;
                    $designer->Cell(150,15,utf8_decode($linha),0,0,'L');
                    $designer->Ln();
                    $linha = 'Nome Atendente Fraterno: ';
                    $designer->Cell(320,15,utf8_decode($linha),0,0,'L');
                    $linha = 'Retornar em:____/____ Dia da semana';
                    $designer->Cell(230,15,utf8_decode($linha),0,0,'L');
                    $designer->SetFont('Arial','B',10);
                    $designer->Ln();
                    $linha = 'TERAPIA';
                    $designer->Cell(130,15,utf8_decode($linha),1,0,'L');
                    $linha = 'Quant.';
                    $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                    $linha = 'Data';
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = 'Data';
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = 'Data';
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = 'Data';
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = 'Data';
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = 'Data';
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = 'Data';
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = 'Data';
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');  
                    $designer->SetFont('Arial','',8);
                    $designer->Ln();
                    
                    $query = "select start_time from calendar_event c left join terapy t on t.id = terapy_id 
                          where person_id = ".$key." and name like '%Profissional%' and start_time >= '".date('Y-m-d')."'
                          order by start_time";
                    $results = $conn->query($query);
                        
                    if ($results)
                    {
                        $atendimentos = array();
                        $quantidade=0;
                        foreach ($results as $result)
                    	    {
                    	        $atendimentos[] = $result['start_time'];
                    	        $quantidade++;
                    	    }
                    }
                    
                    if ($quantidade==0)
                    {
                        $quantidade=null;
                    }
                    
                    $linha = 'Atendimento Profissional';
                    $designer->Cell(130,15,utf8_decode($linha),1,0,'L'); 
                    $linha = $quantidade;
                    $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[0]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[1]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[2]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[3]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[4]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[5]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[6]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[7]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L'); 
                    $designer->Ln();
                    
                    $query = "select start_time from calendar_event c left join terapy t on t.id = terapy_id 
                          where person_id = ".$key." and name like '%Terapêutico%' and start_time >= '".date('Y-m-d')."'
                          order by start_time";
                    $results = $conn->query($query);
                        
                    if ($results)
                    {
                        $atendimentos = array();
                        $quantidade=0;
                        foreach ($results as $result)
                    	    {
                    	        $atendimentos[] = $result['start_time'];
                    	        $quantidade++;
                    	    }
                    }
                    
                    if ($quantidade==0)
                    {
                        $quantidade=null;
                    }
                    
                    $linha = 'Apoio Terapêutico';
                    $designer->Cell(130,15,utf8_decode($linha),1,0,'L'); 
                    $linha = $quantidade;
                    $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[0]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[1]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[2]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[3]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[4]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[5]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[6]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[7]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L'); 
                    $designer->Ln();
                    
                    $query = "select start_time from calendar_event c left join terapy t on t.id = terapy_id 
                          where person_id = ".$key." and name like '%Biomagnética%' and start_time >= '".date('Y-m-d')."'
                          order by start_time";
                    $results = $conn->query($query);
                        
                    if ($results)
                    {
                        $atendimentos = array();
                        $quantidade=0;
                        foreach ($results as $result)
                    	    {
                    	        $atendimentos[] = $result['start_time'];
                    	        $quantidade++;
                    	    }
                    }
                    
                    if ($quantidade==0)
                    {
                        $quantidade=null;
                    }
                    
                    $linha = 'Biomagnética';
                    $designer->Cell(130,15,utf8_decode($linha),1,0,'L'); 
                    $linha = $quantidade;
                    $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[0]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[1]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[2]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[3]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[4]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[5]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[6]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[7]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $designer->Ln();
                    
                    $query = "select start_time from calendar_event c left join terapy t on t.id = terapy_id 
                          where person_id = ".$key." and name like '%Inca%' and start_time >= '".date('Y-m-d')."'
                          order by start_time";
                    $results = $conn->query($query);
                        
                    if ($results)
                    {
                        $atendimentos = array();
                        $quantidade=0;
                        foreach ($results as $result)
                    	    {
                    	        $atendimentos[] = $result['start_time'];
                    	        $quantidade++;
                    	    }
                    }
                    
                    if ($quantidade==0)
                    {
                        $quantidade=null;
                    }
                    
                    $linha = 'Cristal Inca';
                    $designer->Cell(130,15,utf8_decode($linha),1,0,'L'); 
                    $linha = $quantidade;
                    $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[0]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[1]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[2]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[3]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[4]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[5]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[6]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[7]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $designer->Ln();
                    
                    $query = "select start_time from calendar_event c left join terapy t on t.id = terapy_id 
                          where person_id = ".$key." and name like '%Energética%' and start_time >= '".date('Y-m-d')."'
                          order by start_time";
                    $results = $conn->query($query);
                        
                    if ($results)
                    {
                        $atendimentos = array();
                        $quantidade=0;
                        foreach ($results as $result)
                    	    {
                    	        $atendimentos[] = $result['start_time'];
                    	        $quantidade++;
                    	    }
                    }
                    
                    if ($quantidade==0)
                    {
                        $quantidade=null;
                    }
                    
                    $linha = 'Energética';
                    $designer->Cell(130,15,utf8_decode($linha),1,0,'L'); 
                    $linha = $quantidade;
                    $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[0]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[1]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[2]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[3]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[4]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[5]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[6]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[7]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $designer->Ln();
                    
                    $query = "select start_time from calendar_event c left join terapy t on t.id = terapy_id 
                          where person_id = ".$key." and name like '%Genérica%' and start_time >= '".date('Y-m-d')."'
                          order by start_time";
                    $results = $conn->query($query);
                        
                    if ($results)
                    {
                        $atendimentos = array();
                        $quantidade=0;
                        foreach ($results as $result)
                    	    {
                    	        $atendimentos[] = $result['start_time'];
                    	        $quantidade++;
                    	    }
                    }
                    
                    if ($quantidade==0)
                    {
                        $quantidade=null;
                    }
                    
                    $linha = 'Genérica';
                    $designer->Cell(130,15,utf8_decode($linha),1,0,'L'); 
                    $linha = $quantidade;
                    $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[0]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[1]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[2]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[3]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[4]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[5]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[6]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[7]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L'); 
                    $designer->Ln();
                    
                    $query = "select start_time from calendar_event c left join terapy t on t.id = terapy_id 
                          where person_id = ".$key." and name like '%Charrua%' and start_time >= '".date('Y-m-d')."'
                          order by start_time";
                    $results = $conn->query($query);
                        
                    if ($results)
                    {
                        $atendimentos = array();
                        $quantidade=0;
                        foreach ($results as $result)
                    	    {
                    	        $atendimentos[] = $result['start_time'];
                    	        $quantidade++;
                    	    }
                    }
                    
                    if ($quantidade==0)
                    {
                        $quantidade=null;
                    }
                    
                    $linha = 'Grupo Charrua';
                    $designer->Cell(130,15,utf8_decode($linha),1,0,'L'); 
                    $linha = $quantidade;
                    $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[0]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[1]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[2]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[3]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[4]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[5]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[6]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[7]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $designer->Ln();
                    
                    $query = "select start_time from calendar_event c left join terapy t on t.id = terapy_id 
                          where person_id = ".$key." and name like '%Holoterapia%' and start_time >= '".date('Y-m-d')."'
                          order by start_time";
                    $results = $conn->query($query);
                        
                    if ($results)
                    {
                        $atendimentos = array();
                        $quantidade=0;
                        foreach ($results as $result)
                    	    {
                    	        $atendimentos[] = $result['start_time'];
                    	        $quantidade++;
                    	    }
                    }
                    
                    if ($quantidade==0)
                    {
                        $quantidade=null;
                    }
                    
                    $linha = 'Holoterapia';
                    $designer->Cell(130,15,utf8_decode($linha),1,0,'L'); 
                    $linha = $quantidade;
                    $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[0]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[1]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[2]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[3]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[4]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[5]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[6]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[7]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $designer->Ln();
                    
                    $query = "select start_time from calendar_event c left join terapy t on t.id = terapy_id 
                          where person_id = ".$key." and name like '%Meridianoterapia%' and start_time >= '".date('Y-m-d')."'
                          order by start_time";
                    $results = $conn->query($query);
                        
                    if ($results)
                    {
                        $atendimentos = array();
                        $quantidade=0;
                        foreach ($results as $result)
                    	    {
                    	        $atendimentos[] = $result['start_time'];
                    	        $quantidade++;
                    	    }
                    }
                    
                    if ($quantidade==0)
                    {
                        $quantidade=null;
                    }
                    
                    $linha = 'Meridianoterapia';
                    $designer->Cell(130,15,utf8_decode($linha),1,0,'L'); 
                    $linha = $quantidade;
                    $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[0]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[1]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[2]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[3]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[4]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[5]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[6]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[7]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $designer->Ln();
                    
                    $query = "select start_time from calendar_event c left join terapy t on t.id = terapy_id 
                          where person_id = ".$key." and name like '%Palestras%' and start_time >= '".date('Y-m-d')."'
                          order by start_time";
                    $results = $conn->query($query);
                        
                    if ($results)
                    {
                        $atendimentos = array();
                        $quantidade=0;
                        foreach ($results as $result)
                    	    {
                    	        $atendimentos[] = $result['start_time'];
                    	        $quantidade++;
                    	    }
                    }
                    
                    if ($quantidade==0)
                    {
                        $quantidade=null;
                    }
                    
                    $linha = 'Palestras';
                    $designer->Cell(130,15,utf8_decode($linha),1,0,'L'); 
                    $linha = $quantidade;
                    $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[0]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[1]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[2]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[3]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[4]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[5]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[6]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[7]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $designer->Ln();
                    
                    $query = "select start_time from calendar_event c left join terapy t on t.id = terapy_id 
                          where person_id = ".$key." and name like '%Câmara%' and start_time >= '".date('Y-m-d')."'
                          order by start_time";
                    $results = $conn->query($query);
                        
                    if ($results)
                    {
                        $atendimentos = array();
                        $quantidade=0;
                        foreach ($results as $result)
                    	    {
                    	        $atendimentos[] = $result['start_time'];
                    	        $quantidade++;
                    	    }
                    }
                    
                    if ($quantidade==0)
                    {
                        $quantidade=null;
                    }
                    
                    $linha = 'Passe de Câmara';
                    $designer->Cell(130,15,utf8_decode($linha),1,0,'L'); 
                    $linha = $quantidade;
                    $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[0]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[1]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[2]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[3]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[4]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[5]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[6]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[7]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $designer->Ln();
                    
                    $query = "select start_time from calendar_event c left join terapy t on t.id = terapy_id 
                          where person_id = ".$key." and name like '%Quântica%' and start_time >= '".date('Y-m-d')."'
                          order by start_time";
                    $results = $conn->query($query);
                        
                    if ($results)
                    {
                        $atendimentos = array();
                        $quantidade=0;
                        foreach ($results as $result)
                    	    {
                    	        $atendimentos[] = $result['start_time'];
                    	        $quantidade++;
                    	    }
                    }
                    
                    if ($quantidade==0)
                    {
                        $quantidade=null;
                    }
                    
                    $linha = 'Quântica Planetária';
                    $designer->Cell(130,15,utf8_decode($linha),1,0,'L'); 
                    $linha = $quantidade;
                    $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[0]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[1]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[2]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[3]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[4]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[5]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[6]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[7]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $designer->Ln();
                    
                    $query = "select start_time from calendar_event c left join terapy t on t.id = terapy_id 
                          where person_id = ".$key." and name like '%Magnética%' and start_time >= '".date('Y-m-d')."'
                          order by start_time";
                    $results = $conn->query($query);
                        
                    if ($results)
                    {
                        $atendimentos = array();
                        $quantidade=0;
                        foreach ($results as $result)
                    	    {
                    	        $atendimentos[] = $result['start_time'];
                    	        $quantidade++;
                    	    }
                    }
                    
                    if ($quantidade==0)
                    {
                        $quantidade=null;
                    }
                    
                    $linha = 'TM - Terapia Magnética';
                    $designer->Cell(130,15,utf8_decode($linha),1,0,'L'); 
                    $linha = $quantidade;
                    $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[0]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[1]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[2]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[3]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[4]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[5]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[6]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[7]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $designer->Ln();
                    
                    $query = "select start_time from calendar_event c left join terapy t on t.id = terapy_id 
                          where person_id = ".$key." and name like '%Físico%' and start_time >= '".date('Y-m-d')."'
                          order by start_time";
                    $results = $conn->query($query);
                        
                    if ($results)
                    {
                        $atendimentos = array();
                        $quantidade=0;
                        foreach ($results as $result)
                    	    {
                    	        $atendimentos[] = $result['start_time'];
                    	        $quantidade++;
                    	    }
                    }
                    
                    if ($quantidade==0)
                    {
                        $quantidade=null;
                    }
                    
                    $linha = 'Tratamento Físico';
                    $designer->Cell(130,15,utf8_decode($linha),1,0,'L'); 
                    $linha = $quantidade;
                    $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[0]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[1]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[2]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[3]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[4]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[5]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[6]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[7]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $designer->Ln();
                    
                    $query = "select start_time from calendar_event c left join terapy t on t.id = terapy_id 
                          where person_id = ".$key." and name like '%Caulim%' and start_time >= '".date('Y-m-d')."'
                          order by start_time";
                    $results = $conn->query($query);
                        
                    if ($results)
                    {
                        $atendimentos = array();
                        $quantidade=0;
                        foreach ($results as $result)
                    	    {
                    	        $atendimentos[] = $result['start_time'];
                    	        $quantidade++;
                    	    }
                    }
                    
                    if ($quantidade==0)
                    {
                        $quantidade=null;
                    }
                    
                    $linha = 'Triagem Caulim';
                    $designer->Cell(130,15,utf8_decode($linha),1,0,'L'); 
                    $linha = $quantidade;
                    $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[0]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[1]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[2]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[3]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[4]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[5]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[6]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[7]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $designer->Ln();
                    
                    $query = "select start_time from calendar_event c left join terapy t on t.id = terapy_id 
                          where person_id = ".$key." and name like '%Cirurgia%' and start_time >= '".date('Y-m-d')."'
                          order by start_time";
                    $results = $conn->query($query);
                        
                    if ($results)
                    {
                        $atendimentos = array();
                        $quantidade=0;
                        foreach ($results as $result)
                    	    {
                    	        $atendimentos[] = $result['start_time'];
                    	        $quantidade++;
                    	    }
                    }
                    
                    if ($quantidade==0)
                    {
                        $quantidade=null;
                    }
                    
                    $linha = 'Triagem Cirurgia';
                    $designer->Cell(130,15,utf8_decode($linha),1,0,'L'); 
                    $linha = $quantidade;
                    $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[0]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[1]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[2]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[3]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[4]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[5]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[6]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[7]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $designer->Ln();
                    
                    $query = "select start_time from calendar_event c left join terapy t on t.id = terapy_id 
                          where person_id = ".$key." and name like '%Universal%' and start_time >= '".date('Y-m-d')."'
                          order by start_time";
                    $results = $conn->query($query);
                        
                    if ($results)
                    {
                        $atendimentos = array();
                        $quantidade=0;
                        foreach ($results as $result)
                    	    {
                    	        $atendimentos[] = $result['start_time'];
                    	        $quantidade++;
                    	    }
                    }
                    
                    if ($quantidade==0)
                    {
                        $quantidade=null;
                    }
                    
                    $linha = 'Universal';
                    $designer->Cell(130,15,utf8_decode($linha),1,0,'L'); 
                    $linha = $quantidade;
                    $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[0]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[1]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[2]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[3]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[4]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[5]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[6]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[7]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $designer->Ln();
                    
                    $query = "select start_time from calendar_event c left join terapy t on t.id = terapy_id 
                          where person_id = ".$key." and name like '%Vibracional%' and start_time >= '".date('Y-m-d')."'
                          order by start_time";
                    $results = $conn->query($query);
                        
                    if ($results)
                    {
                        $atendimentos = array();
                        $quantidade=0;
                        foreach ($results as $result)
                    	    {
                    	        $atendimentos[] = $result['start_time'];
                    	        $quantidade++;
                    	    }
                    }
                    
                    if ($quantidade==0)
                    {
                        $quantidade=null;
                    }
                    
                    $linha = 'Vibracional';
                    $designer->Cell(130,15,utf8_decode($linha),1,0,'L'); 
                    $linha = $quantidade;
                    $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[0]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[1]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[2]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[3]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[4]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[5]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[6]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[7]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $designer->Ln();
                    
                    $query = "select start_time from calendar_event c left join terapy t on t.id = terapy_id 
                          where person_id = ".$key." and name like '%Zapper%' and start_time >= '".date('Y-m-d')."'
                          order by start_time";
                    $results = $conn->query($query);
                        
                    if ($results)
                    {
                        $atendimentos = array();
                        $quantidade=0;
                        foreach ($results as $result)
                    	    {
                    	        $atendimentos[] = $result['start_time'];
                    	        $quantidade++;
                    	    }
                    }
                    
                    if ($quantidade==0)
                    {
                        $quantidade=null;
                    }
                    
                    $linha = 'Zapper';
                    $designer->Cell(130,15,utf8_decode($linha),1,0,'L'); 
                    $linha = $quantidade;
                    $designer->Cell(50,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[0]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[1]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[2]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[3]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[4]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[5]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[6]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $linha = TDate::date2br($atendimentos[7]);
                    $designer->Cell(45,15,utf8_decode($linha),1,0,'L');
                    $designer->SetFont('Arial','B',8);
                    $designer->Ln();
                    $linha = '1. Se você tiver agendamento para a Triagem Cirurgia é obrigatório CÓPIA DO LAUDO MÉDICO, sem a cópia não seria possível a análise.';
                    $designer->Cell(565,10,utf8_decode($linha),0,true,'L');
                    $designer->SetFont('Arial','',8);
                    $linha = '2. Seja solidário de preferência para Idosos e pacientes muito debilitados.';
                    $designer->Cell(565,10,utf8_decode($linha),0,true,'L');
                    $linha = '3. Procure marcar as terapias fora do teu dia de trabalho.';
                    $designer->Cell(565,10,utf8_decode($linha),0,true,'L');
                    $linha = '4. Caso você não possa comparecer por favor ligue para desmarcar sua terapia 47-3275-0001';
                    $designer->Cell(565,10,utf8_decode($linha),0,true,'L');
                    $designer->Ln();
                    $designer->Ln();
                }
            }
            
            TTransaction::close();
            
            $file = 'app/output/FichaAgendamento.pdf';
            
            if (!file_exists($file) OR is_writable($file))
            {
                 $designer->save($file);
                 parent::openFile($file);
            }
            else
            {
                throw new Exception(_t('Permission denied') . ': ' . $file);
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', '<b>Error</b> ' . $e->getMessage());
        }   
    }
}