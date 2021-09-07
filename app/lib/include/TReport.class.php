<?php
class TReport extends TPDFDesigner
{
    var $title = '';
    var $filtro = '';
    var $columns = array();
    
    function setTitle($param)
    {
        global $title;
        $title = $param;
    }
    
    function setFilters($param)
    {
        global $filters;
        $filters = $param;
    }
    
    function setColumns($param)
    {
        global $columns;
        $columns = $param;
    }
    
    function Header()
    {
        global $title;
        global $filters;
        global $columns;
                
        $image_file = TSession::getValue('unitlogo');
        $this->Image($image_file, 30, 30, 75, 75, 'PNG', '', 'T', FALSE, 300, '', FALSE, FALSE, 0, FALSE, FALSE, FALSE);
        $this->SetXY(145, 35);
        $this->SetTextColor(0,126,196);
        $this->SetFont('Arial','B',24);        
        $this->Cell(200,15,utf8_decode($title),0,0,'L');        
        $this->SetXY(145, 60);
        $this->SetFont('Arial','B',12);
        if (sizeof($filters)>0)
        {
            $this->Cell(0,10,utf8_decode('Filtros do Relatório:'),0,0,'L');
            $this->SetTextColor(0,0,0);
            foreach ($filters as $filter)
            {
                $this->SetX(265);
                $this->Cell(0,10,utf8_decode($filter),0,0,'L');
                $this->Ln();
            }
        }
        $Y = $this->GetY();
        if ($Y < 115)
        {
            $Y = 115;
        }
        $this->SetTextColor(0,126,196);
        $this->SetXY(30, $Y);
        $this->SetFont('Arial','B',10);
        foreach ($columns as $column)
        {
            $this->Cell($column['size'],15,utf8_decode($column['text']),0,0,$column['align']);
        }
        $this->Ln();
    }
    
    function Footer()
    {
        setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
        date_default_timezone_set('America/Sao_Paulo');
        
        $this->SetXY(30, -30);
        $this->SetFont('Arial','',10);
        $this->Cell(0, 10, strftime('%A, %d de %B de %Y', strtotime('today')), 0, 0, 'L');
        $this->SetXY(130, -30);
        $this->Cell(0, 10, utf8_decode('Página '.$this->PageNo().' de {nb}'),0,0,'R');
    }
}