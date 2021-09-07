<?php
/**
 * FullCalendarDatabaseView
 */
class FullCalendarDatabaseView extends TPage
{
    protected $form;
    private $fc;
    
    /**
     * Page constructor
     */
    public function __construct($param)
    {
        parent::__construct();
        parent::include_js('app/lib/include/application.js');
        
        $this->form = new BootstrapFormBuilder('form_FullCalendarDatabaseView');
        $this->form->setFormTitle(_t('Schedulings'));
        
        $start_date = new TDate('start_date');
        $end_date = new TDate('end_date');
        $person_id = new TDBSeekButton('person_id',TSession::getValue('unitbank'),'form_FullCalendarDatabaseView','Person','name','person_id','person');
        $person = new TEntry('person');
        $terapy_id = new TDBCombo('terapy_id',TSession::getValue('unitbank'),'Terapy','id','name');
        
        $start_date->setSize(100);
        $end_date->setSize(100);
        $person_id->setSize(100);
        $person->setSize('calc(100% - 100px)');
        $terapy_id->setSize('50%');
        
        $start_date->setMask('dd/mm/yyyy');        
        $end_date->setMask('dd/mm/yyyy');
        
        $this->form->addFields( [$l1 = new TLabel(_t('Start Date'))], [$start_date], [$l2 = new TLabel(_t('End Date'))], [$end_date] );
        $this->form->addFields( [$l3 = new TLabel(_t('Person'))], [$person_id, $person] );
        $this->form->addFields( [$l4 = new TLabel(_t('Terapy'))], [$terapy_id] );
        
        $l1->setFontStyle('bold');
        $l2->setFontStyle('bold');
        $l3->setFontStyle('bold');
        $l4->setFontStyle('bold');
        
        $this->form->addAction(_t('Find'), new TAction(array('CalendarEventList', 'onReload')), 'fa:search');
        $this->form->addAction(_t('Report'), new TAction(array($this, 'onGenerateReport')), 'fa:print');
        
        $this->fc = new TFullCalendar(date('Y-m-d'), 'agendaDay');
        $this->fc->setReloadAction(new TAction(array($this, 'getEvents')));
        $this->fc->setDayClickAction(new TAction(array('CalendarEventForm', 'onStartEdit')));
        $this->fc->setEventClickAction(new TAction(array('CalendarEventForm', 'onEdit')));
        $this->fc->setEventUpdateAction(new TAction(array('CalendarEventForm', 'onUpdateEvent')));
        
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add($this->fc);
                
        parent::add($container);
    }
    
    /**
     * Output events as an json
     */
    public static function getEvents($param=NULL)
    {        
        $return = array();
        try
        {
            TTransaction::open(TSession::getValue('unitbank'));            
            
            $events = CalendarEvent::where('start_time', '>=', $param['start'])
                                    ->where('end_time',   '<=', $param['end'])->load();
            
            if ($events)
            {
                foreach ($events as $event)
                {
                    $event_array = $event->toArray();
                    $event_array['start'] = str_replace( ' ', 'T', $event_array['start_time']);
                    $event_array['end'] = str_replace( ' ', 'T', $event_array['end_time']);
                    $return[] = $event_array;
                }
            }
            TTransaction::close();
            echo json_encode($return);
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
    
    /**
     * Reconfigure the callendar
     */
    public function onReload($param = null)
    {
        if (isset($param['view']))
        {
            $this->fc->setCurrentView($param['view']);
        }
        
        if (isset($param['date']))
        {
            $this->fc->setCurrentDate($param['date']);
        }
    }
    
    public function onGenerateReport($param)
    {        
        $data = $this->form->getData();
        
        try
        {
            // open a transaction with database 'samples'
            TTransaction::open(TSession::getValue('unitbank'));
            $conn = TTransaction::get();
            
            $query="select c.id id, start_time, p.name person , t.name terapy, showedup from calendar_event c 
                     left join person p on p.id = person_id 
                     left join terapy t on t.id = terapy_id";
                     
            $where=array();
            $filters=array();
            if ($param['start_date'] != '' && $param['end_date'])
            {
                $where[]="date(start_time) between '".TDate::date2us($param['start_date'])."' and '".TDate::date2us($param['end_date'])."'";
                $filters[]=" Data de: ".$param['start_date'];
                $filters[]=" Data até: ".$param['end_date'];
            }
            
            if ($param['person_id'] != '')
            {
                $where[]='person_id='.$param['person_id'];
                $filters[]=_t('Person').': '.$param['person'];
            }
            
            if ($param['terapy_id'] != '')
            {
                $where[]='terapy_id='.$param['terapy_id'];
                $terapy = new Terapy($param['terapy_id']);
                $filters[]=_t('Terapy').': '.$terapy->name;
            }
            
            if (sizeof($where))
            {
                $query.=' where '.implode(' and ', $where);
            }
                                  
            $results = $conn->query($query);
            
            if ($results)
            {                
                $designer = new TReport();
                $designer->setTitle('Relatório de Atendimentos');
                $columns = array();
                $columns[0]['size'] = 60;
                $columns[0]['text'] = _t('Date');
                $columns[0]['align'] = 'R';
                $columns[1]['size'] = 245;
                $columns[1]['text'] = _t('Person');
                $columns[1]['align'] = 'L';
                $columns[2]['size'] = 180;
                $columns[2]['text'] = _t('Terapy');
                $columns[2]['align'] = 'L';
                $columns[4]['size'] = 25;
                $columns[4]['text'] = _t('Showedup');
                $columns[4]['align'] = 'C';
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
                foreach ($results as $result)
                {
                    $i++;
                    $designer->SetTextColor(0,0,0);
                    $designer->Cell(60,15,TDate::date2br($result['start_time']),0,0,'R',$fill);
                    $designer->Cell(245,15,$result['person'],0,0,'L',$fill);
                    $designer->Cell(180,15,$result['terapy'],0,0,'L',$fill);
                    if ($result['showedup'] == 'N') {
                        $designer->SetTextColor(255,0,0);
                        $designer->Cell(40,15,utf8_decode(_t('No')),0,0,'C',$fill);
                    } else if ($result['showedup'] == 'S') {
                        $designer->SetTextColor(0,128,0);
                        $designer->Cell(40,15,_t('Yes'),0,0,'C',$fill);
                    } else {
                        $designer->Cell(40,15,'',0,0,'C',$fill);
                    }
                    $designer->Ln();
                    $fill = !$fill;
                }
                
                $designer->SetTextColor(0,126,196);
                $designer->Cell(450,15,'Total de Agendamentos',0,0,'R',$fill);
                $designer->Cell(40,15,$i,0,0,'L',$fill);
                
                // stores the file
                if (!file_exists("app/output/CalendarEventReport.pdf") OR is_writable("app/output/CalendarEventReport.pdf"))
                {
                    $designer->save("app/output/CalendarEventReport.pdf");
                }
                else
                {
                    throw new Exception(_t('Permission denied') . ': ' . "app/output/CalendarEventReport.pdf");
                }
                    
                parent::openFile("app/output/CalendarEventReport.pdf");
                    
                // shows the success message
                //new TMessage('info', 'Report generated. Please, enable popups in the browser (just in the web).');
            }
            else
            {
                new TMessage('error', 'No records found');
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
}