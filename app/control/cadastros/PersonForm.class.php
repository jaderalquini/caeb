<?php
/**
 * PersonForm Registration
 * @author  <your name here>
 */
class PersonForm extends TPage
{
    protected $form; // form
    protected $course_list;
    protected $terapy_list;
    protected $scheduling_list;
    
    use Adianti\Base\AdiantiStandardFormTrait; // Standard form methods
    
    /**
     * Class constructor
     * Creates the page and the registration form
     */
    function __construct()
    {
        parent::__construct();
        parent::include_js('app/lib/include/application.js');
        
        $this->setDatabase(TSession::getValue('unitbank'));              // defines the database
        $this->setActiveRecord('Person');     // defines the active record
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_Person');
        $this->form->setFormTitle(_t('Peopple'));

        // create the form fields
        $id = new THidden('id');
        $name = new TEntry('name');
        $rg = new TEntry('rg');
        $cpf = new TEntry('cpf');
        $birthdate = new TDate('birthdate');
        $maritalstatus = new TCombo('maritalstatus');
        $address = new TEntry('address');
        $neighborhood = new TEntry('neighborhood');
        $zip = new TEntry('zip');
        $city = new TEntry('city');
        $state_id = new TDBCombo('state_id','zip','State','id','name');
        $phone = new TEntry('phone');
        $celphone = new TEntry('celphone');
        $email = new TEntry('email');
        $assignmenter = new TRadioGroup('assignmenter');
        $status = new TRadioGroup('status');
        $registerdate = new TDate('registerdate');
        $terapy_id = new TDBCombo('terapy_id',TSession::getValue('unitbank'),'Terapy','id','name');
        $terapy_weekday = new TCombo('terapy_weekday');
        $course_id = new TDBCombo('course_id',TSession::getValue('unitbank'),'Course','id','name');
        $course_weekday = new TCombo('course_weekday');
        
        $id->setEditable(FALSE);
        
        // define the sizes
        $id->setSize(100);
        $name->setSize('100%');
        $cpf->setSize(150);
        $birthdate->setSize(100);
        $zip->setSize(100);
        $address->setSize('100%');
        $neighborhood->setSize('100%');
        $city->setSize('100%');
        $phone->setSize(150);
        $celphone->setSize(150);
        $email->setSize('100%');
        $registerdate->setSize(100);
        $terapy_id->setSize('100%');
        $terapy_weekday->setSize(100);
        $course_id->setSize('100%');
        $course_weekday->setSize(100);
        
        $cpf->setMask('999.999.999-99');
        $birthdate->setMask('dd/mm/yyyy');
        $zip->setMask('99999-999');
        $phone->setMask('(99) 9999-9999');
        $celphone->setMask('(99) 99999-9999');
        $registerdate->setMask('dd/mm/yyyy');
        
        $maritalstatus->addItems(array('INDEFINIDO' => 'INDEFINIDO', 'CASADO(A)' => 'CASADO(A)', 'UNIÃO ESTAVEL' => 'UNIÃO ESTAVEL', 'SOLTEIRO(A)' => 'SOLTEIRO(A)', 
                                       'DIVORCIADO(A)' => 'DIVORCIADO(A)', 'SEPARADO(A)' => 'SEPARADO(A)', 'VIÚVO(A)' => 'VIÚVO(A)'));
        
        $assignmenter->addItems(array('S' => 'Sim', 'N' => 'Não'));
        $assignmenter->setLayout('horizontal');
        
        $status->addItems(array('A' => 'Ativo', 'I' => 'Inativo'));
        $status->setLayout('horizontal');
        
        $zip->setExitAction(new TAction(array($this,'onExitZip')));
        $assignmenter->setChangeAction(new TAction(array($this, 'onChangeAssignmenter')));
        $terapy_id->setChangeAction(new TAction(array($this, 'onChangeTerapy')));
        $course_id->setChangeAction(new TAction(array($this, 'onChangeCourse')));
        
        $name->addValidation(_t('Name'), new TRequiredValidator);
        $birthdate->addValidation(_t('Birth Date'), new TDateValidator);
        $registerdate->addValidation(_t('Register Date'), new TDateValidator);
        
        $this->form->appendPage('Informações Pessoais');
                
        $div = new TElement('div');
        $div->id = 'Status';
        $div->add($status);        

        // add the fields        
        $this->form->addFields( [$l1 = new TLabel(_t('Name'))], [$name],  [$l2 = new TLabel('RG')], [$rg] );
        $this->form->addFields( [$l3 = new TLabel('CPF')], [$cpf], [$l4 = new TLabel(_t('Birth Date'))], [$birthdate] );
        $this->form->addFields( [$l5 = new TLabel(_t('Marital Status'))], [$maritalstatus], [$l6 = new TLabel(_t('Zip'))], [$zip] );  
        $this->form->addFields( [$l7 = new TLabel(_t('Address'))], [$address], [$l8 = new TLabel(_t('Neighborhood'))], [$neighborhood] );
        $this->form->addFields( [$l9 = new TLabel(_t('City'))], [$city], [$l10 = new TLabel(_t('State'))], [$state_id] );
        $this->form->addFields( [$l11 = new TLabel(_t('Phone'))], [$phone], [$l12 = new TLabel(_t('Cel Phone'))], [$celphone] );
        $this->form->addFields( [$l13 = new TLabel('E-mail')], [$email], [$l14 = new TLabel(_t('Assignmenter'))], [$assignmenter] );
        $this->form->addFields( [$l15 = new TLabel('Status')], [$div], [$l16 = new TLabel(_t('Register Date'))], [$registerdate] );
        $this->form->addFields( [new TLabel('')], [$id] );
        
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
        $l13->id='labelStatus';
        $l14->setFontStyle('bold');
        $l15->setFontStyle('bold');
        $l16->setFontStyle('bold');       
        
        $name->placeholder = _t('Name');
        $cpf->placeholder = 'CPF';
        $birthdate->placeholder = _t('Birth Date');
        $zip->placeholder = _t('Zip');
        $address->placeholder = _t('Address');
        $neighborhood->placeholder = _t('Neighborhood');
        $city->placeholder = _t('City');
        $phone->placeholder = _t('Phone');
        $celphone->placeholder = _t('Cel Phone');
        $email->placeholder = 'E-mail';
        $registerdate->placeholder = _t('Register Date');
        $terapy_id->placeholder = _t('Id');
        $course_id->placeholder = _t('Id');
        
        $add_terapy = TButton::create('addTerapy', array($this, 'onAddTerapy'), _t('Add'), 'fa:plus green');
        $add_course  = TButton::create('addCourse',  array($this,'onAddCourse'), _t('Add'), 'fa:plus green');
        
        $label1 = new TLabel('Terapias', '#000000', 12, 'bi');
        $label1->style='text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
        
        $this->form->appendPage('Informações sobre Tarefa/Estudo');
        $this->form->addContent( [$label1] );
        
        $this->form->addFields( [$l15 = new TLabel(_t('Terapy'))], [$terapy_id], [$l16 = new TLabel(_t('Weekday'))], [$terapy_weekday, $add_terapy] );
        
        $l15->setFontStyle('bold');
        $l16->setFontStyle('bold');
        
        $this->terapy_list = new TQuickGrid;
        $this->terapy_list->setHeight(100);
        $this->terapy_list->makeScrollable();
        $this->terapy_list->style='width: 100%;';
        $this->terapy_list->id = 'terapy_list';
        $this->terapy_list->disableDefaultClick();
        $this->terapy_list->addQuickColumn('', 'delete', 'center', '5%');
        $this->terapy_list->addQuickColumn(_t('Terapy'), 'terapy', 'left', '70%');
        $this->terapy_list->addQuickColumn(_t('Weekday'), 'weekday', 'left', '25%');
        $this->terapy_list->createModel();
        
        $this->form->addContent(array($this->terapy_list));
        
        $label2 = new TLabel('Cursos', '#000000', 12, 'bi');
        $label2->style='text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
        
        $this->form->addContent( [$label2] );
        
        $this->form->addFields( [$l17 = new TLabel(_t('Course'))], [$course_id], [$l18 = new TLabel(_t('Weekday'))], [$course_weekday, $add_course] );
        
        $l17->setFontStyle('bold');
        $l18->setFontStyle('bold');
        
        $this->course_list = new TQuickGrid;
        $this->course_list->setHeight(100);
        $this->course_list->makeScrollable();
        $this->course_list->style='width: 100%;';
        $this->course_list->id = 'course_list';
        $this->course_list->disableDefaultClick();
        $this->course_list->addQuickColumn('', 'delete', 'center', '5%');
        $this->course_list->addQuickColumn(_t('Course'), 'course', 'left', '70%');
        $this->course_list->addQuickColumn(_t('Weekday'), 'weekday', 'left', '25%');
        $this->course_list->createModel();
        
        $this->form->addContent(array($this->course_list));
        
        $this->form->appendPage('Histórico de Atendimentos');
        
        $this->scheduling_list = new TQuickGrid;
        $this->scheduling_list->setHeight(300);
        $this->scheduling_list->makeScrollable();
        $this->scheduling_list->style='width: 100%;';
        $this->scheduling_list->id='scheduling_lis';
        $this->scheduling_list->disableDefaultClick();
        $this->scheduling_list->addQuickColumn(_t('Date'), 'date', 'left', '20%');
        $this->scheduling_list->addQuickColumn(_t('Terapy'), 'terapy', 'left', '70%');
        $this->scheduling_list->addQuickColumn(_t('Showedup'), 'showedup', 'left', '10%');
        $this->scheduling_list->createModel();
        
        $this->form->addContent(array($this->scheduling_list));
        
        // create the form actions
        $this->form->addAction(_t('Save'), new TAction(array($this, 'onSave')), 'fa:floppy-o');
        $this->form->addAction(_t('New'),  new TAction(array($this, 'onEdit')), 'bs:plus-sign green');        
        //$this->form->addAction('Imprimir Ficha de Cadastro', new TAction(array($this, 'onPrintFichaCadastral')), 'fa:print');
        //$this->form->addAction('Imprimir Ficha de Agendamentos', new TAction(array($this, 'onPrintFichaAgendamentos')), 'fa:print');
        $this->form->addAction(_t('Back to the listing'), new TAction(array('PersonList','onReload')),  'fa:table blue' );
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', 'PersonList'));
        $container->add($this->form);
        
        parent::add($container);
    }
    
    public static function onSave($param)
    {
        try
        {
            // open a transaction with database 'permission'
            TTransaction::open(TSession::getValue('unitbank'));
            
            $object = new Person;
            $object->fromArray( $param );
            
            $object->birthdate = TDate::date2us($object->birthdate);
            $object->registerdate = TDate::date2us($object->registerdate);
            
            $object->store();
            $object->clearParts();
            
            $object->birthdate = TDate::date2br($object->birthdate);
            $object->registerdate = TDate::date2br($object->registerdate);
            
            $terapies_weekdays = TSession::getValue('terapy_list');
            if (!empty($terapies_weekdays))
            {
                foreach ($terapies_weekdays as $terapy => $weekday)
                {
                    $object->addPersonTerapyWeekday( new Terapy( $terapy ), new Weekday( $weekday ) );
                }
            }
            
            $courses_weekdays = TSession::getValue('course_list');
            if (!empty($courses_weekdays))
            {
                foreach ($courses_weekdays as $course => $weekday)
                {
                    $object->addPersonCourseWeekday( new Course( $course), new Weekday( $weekday ) );
                }
            }
            
            TForm::sendData('form_Person', $object);
            
            // close the transaction
            TTransaction::close();
            
            // shows the success message
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', $e->getMessage());
            
            // undo all pending operations
            TTransaction::rollback();
        }
    }
    
    public function onEdit($param)
    {
        try
        {
            if (isset($param['key']))
            {
                // get the parameter $key
                $key=$param['key'];
                
                // open a transaction with database 'permission'
                TTransaction::open(TSession::getValue('unitbank'));
                
                // instantiates object System_user
                $object = new Person($key);
                $object->birthdate = TDate::date2br($object->birthdate);
                $object->registerdate = TDate::date2br($object->registerdate);
                
                $data = array();
                foreach ($object->getPersonTerapiesWeekdays() as $t => $w)
                {
                		$data[$t] = $w;
                    $terapy = new Terapy($t);
                    $weekday = new Weekday($w);                    
                    
                    $item = new stdClass;
                    $item->terapy = $terapy->name;
                    $item->weekday = $weekday->name;                    
                    
                    $i = new TElement('i');
                    $i->{'class'} = 'fa fa-trash red';
                    $btn = new TElement('a');
                    $btn->{'onclick'} = "__adianti_ajax_exec('class=PersonForm&method=deleteTerapy&id={$terapy->id}');$(this).closest('tr').remove();";
                    $btn->{'class'} = 'btn btn-default btn-sm';
                    $btn->add( $i );
                    
                    $item->delete = $btn;
                    $tr = $this->terapy_list->addItem($item);
                    $tr->{'style'} = 'width: 100%;display: inline-table;';
                }
                
                TSession::setValue('terapy_list', $data);
                
                $data = array();
                foreach ($object->getPersonCoursesWeekdays() as $c => $w)
                {                    
                	$data[$c] = $w;
                    $course = new Course($c);
                    $weekday = new Weekday($w);
                    
                    $item = new stdClass;
                    $item->course = $course->name;
                    $item->weekday = $weekday->name;                    
                    
                    $i = new TElement('i');
                    $i->{'class'} = 'fa fa-trash red';
                    $btn = new TElement('a');
                    $btn->{'onclick'} = "__adianti_ajax_exec('class=PersonForm&method=deleteCourse&id={$terapy->id}');$(this).closest('tr').remove();";
                    $btn->{'class'} = 'btn btn-default btn-sm';
                    $btn->add( $i );
                    
                    $item->delete = $btn;
                    $tr = $this->course_list->addItem($item);
                    $tr->{'style'} = 'width: 100%;display: inline-table;';
                }
                
                TSession::setValue('course_list', $data);
                
                foreach ($object->getPersonSchedulings() as $scheduling)
                {
                    $terapy = new Terapy($scheduling->terapy_id);
                    
                    $item = new stdClass;
                    $item->date = TDate::date2br(substr($scheduling->start_time, 0, 10));
                    $item->terapy = $terapy->name;
                    
                    if ($scheduling->showedup=='N')
                    {
                        $class = 'danger';
                        $label = _t('No');
                    } else if ($scheduling->showedup=='S') {
                        $class = 'success';
                        $label = _t('Yes');
                    }
                    $div = new TElement('span');
                    $div->class="label label-{$class}";
                    $div->style="text-shadow:none; font-size:12px; font-weight:lighter";
                    $div->add($label);
                    
                    $item->showedup = $div;
                    
                    $tr = $this->scheduling_list->addItem($item);
                    $tr->{'style'} = 'width: 100%;display: inline-table;';
                }
                
                $this->form->setData($object);                
                $this->onChangeAssignmenter($param);
                
                // close the transaction
                TTransaction::close();
            }
            else
            {
                $this->form->clear();
                $this->onChangeAssignmenter(array('assignmenter' => 'N'));
                TSession::setValue('terapylist', null);
            }
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
     * Add a Terapy
     */
    public static function onAddTerapy($param)
    {
    		try
        {
            $terapy=$param['terapy_id'];
            $weekday=$param['terapy_weekday'];
            $terapy_list = TSession::getValue('terapy_list');
            
            if (!empty($terapy) AND empty($terapy_list[$terapy]))
            {
                TTransaction::open(TSession::getValue('unitbank'));
                $terapy_list[$terapy] = $weekday;
                TSession::setValue('terapy_list', $terapy_list);
                $terapy = Terapy::find($terapy);
                $weekday = Weekday::find($weekday);                
                TTransaction::close();
                
                $i = new TElement('i');
                $i->{'class'} = 'fa fa-trash red';
                $btn = new TElement('a');
                $btn->{'onclick'} = "__adianti_ajax_exec(\'class=PersonForm&method=deleteTerapy&id=$id\');$(this).closest(\'tr\').remove();";
                $btn->{'class'} = 'btn btn-default btn-sm';
                $btn->add($i);
                
                $tr = new TTableRow;
                $tr->{'class'} = 'tdatagrid_row_odd';
                $tr->{'style'} = 'width: 100%;display: inline-table;';
                $cell = $tr->addCell( $btn );
                $cell->{'style'}='text-align:center';
                $cell->{'class'}='tdatagrid_cell';
                $cell->{'width'} = '5%';
                $cell = $tr->addCell( $terapy->name );
                $cell->{'class'}='tdatagrid_cell';
                $cell->{'width'} = '70%';
                $cell = $tr->addCell( $weekday->name );
                $cell->{'class'}='tdatagrid_cell';
                $cell->{'width'} = '25%';
                
                TScript::create("tdatagrid_add_serialized_row('terapy_list', '$tr');");
                
                $data = new stdClass;
                $data->terapy_id = '';
                $data->terapy_weekday = '';
                TForm::sendData('form_Person', $data);
            }  
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
    
    public static function deleteTerapy($param)
    {
        $terapies = TSession::getValue('terapy_list');
        unset($terapies[ $param['id'] ]);
        TSession::setValue('terapy_list', $terapies);
    }
    
     /**
     * Add a Course
     */
    public static function onAddCourse($param)
    {
        try
        {
            $course = $param['course_id'];
            $weekday = $param['course_weekday'];
            $course_list = TSession::getValue('course_list');
            
            if (!empty($course) AND empty($terapy_list[$course]))
            {
                TTransaction::open(TSession::getValue('unitbank'));
                $course_list[$course] = $weekday;
                TSession::setValue('course_list', $course_list);
                $course = Course::find($course);
                $weekday = Weekday::find($weekday);                
                TTransaction::close();
                
                $i = new TElement('i');
                $i->{'class'} = 'fa fa-trash red';
                $btn = new TElement('a');
                $btn->{'onclick'} = "__adianti_ajax_exec(\'class=PersonForm&method=deleteCourse&id=$id\');$(this).closest(\'tr\').remove();";
                $btn->{'class'} = 'btn btn-default btn-sm';
                $btn->add($i);
                
                $tr = new TTableRow;
                $tr->{'class'} = 'tdatagrid_row_odd';
                $tr->{'style'} = 'width: 100%;display: inline-table;';
                $cell = $tr->addCell( $btn );
                $cell->{'style'}='text-align:center';
                $cell->{'class'}='tdatagrid_cell';
                $cell->{'width'} = '5%';
                $cell = $tr->addCell( $course->name );
                $cell->{'class'}='tdatagrid_cell';
                $cell->{'width'} = '70%';
                $cell = $tr->addCell( $weekday->name );
                $cell->{'class'}='tdatagrid_cell';
                $cell->{'width'} = '25%';
                
                TScript::create("tdatagrid_add_serialized_row('course_list', '$tr');");
                
                $data = new stdClass;
                $data->terapy_id = '';
                $data->terapy_weekday = '';
                TForm::sendData('form_Person', $data);
            }    
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
    
    public static function deleteCourse($param)
    {
        $courses = TSession::getValue('course_list');
        unset($courses[ $param['id'] ]);
        TSession::setValue('course_list', $courses);
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
    
    public static function onChangeAssignmenter($param)
    {
        TScript::create('assignmenter = $("input[name=assignmenter]:checked").val();
                        if (assignmenter == "S") {
                            $(".panel ul > li:nth-child(2)").show();
                            $("#Status").show();
                            $("#labelStatus").show();
                        } else {
                            $(".panel ul > li:nth-child(2)").hide();
                            $("#Status").hide();
                            $("#labelStatus").hide();
                        }                        
                    ');
    }
    
    public static function onChangeTerapy($param)
    {
        try
        {
            TTransaction::open(TSession::getValue('unitbank'));
            
            $weekdays = array();
            $weekdays[] = null;
            
            $repository = new TRepository('TerapyWeekday');
            $criteria = new TCriteria;
            $criteria->add(new TFilter('terapy_id', '=', $param['terapy_id']));
            $objects = $repository->load($criteria);
            
            if ($objects)
            {                
                foreach ($objects as $object)
                {
                    $weekday = new Weekday($object->weekday_id);
                    $weekdays[$weekday->id] = $weekday->name;
                }
                
                asort($weekdays);
            }
            
            TCombo::reload('form_Person', 'terapy_weekday', $weekdays);
            
            TTransaction::close();    
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
    
    public static function onChangeCourse($param)
    {
        try
        {
            TTransaction::open(TSession::getValue('unitbank'));
            
            $weekdays = array();
            $weekdays[] = null;
            
            $repository = new TRepository('CourseWeekday');
            $criteria = new TCriteria;
            $criteria->add(new TFilter('course_id', '=', $param['course_id']));
            $objects = $repository->load($criteria);
            
            if ($objects)
            {                
                foreach ($objects as $object)
                {
                    $weekday = new Weekday($object->weekday_id);
                    $weekdays[$weekday->id] = $weekday->name;
                }
            }
            
            TCombo::reload('form_Person', 'course_weekday', $weekdays);
            
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
        
    }
}