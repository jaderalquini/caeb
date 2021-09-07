<?php
/**
 * SystemUnit Active Record
 * @author  <your-name-here>
 */
class SystemUnit extends TRecord
{
    const TABLENAME = 'system_unit';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    private $uf;
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('name');
        parent::addAttribute('cnpj');
        parent::addAttribute('zip');
        parent::addAttribute('uf_id');
        parent::addAttribute('city');
        parent::addAttribute('neighborhood');
        parent::addAttribute('address');
        parent::addAttribute('phone');
        parent::addAttribute('fax');
        parent::addAttribute('site');
        parent::addAttribute('email');
        parent::addAttribute('logo');
        parent::addAttribute('bank');
    }
    
    /**
     * Method get_uf
     * Sample of usage: $empresa->uf->attribute;
     * @returns UF instance
     */
    public function get_uf()
    {        
        try
        {
            $obj = new UF($this->uf_id);
            return $obj->nome; 
        }  
        catch (Exception $e) // in case of exception
        {
            return NULL;    
        }
    }
}
