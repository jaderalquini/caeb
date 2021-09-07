<?php
class AdiantiMenuBuilder
{
    public static function parse($file, $theme)
    {
        switch ($theme)
        {
            case 'alquiniweb':
                ob_start();
                $callback = array('SystemPermission', 'checkPermission');
                $menu = TMenuBar::newFromXML($file, $callback,'nav navbar-nav');
                $menu->show();
                $menu_string = ob_get_clean();
                return $menu_string;
                break;
            case 'theme1':
                ob_start();
                $callback = array('SystemPermission', 'checkPermission');
                $menu = TMenuBar::newFromXML($file, $callback);
                $menu->show();
                $menu_string = ob_get_clean();
                return $menu_string;
                break;
            case 'theme2':
                ob_start();
                $callback = array('SystemPermission', 'checkPermission');
                $xml = new SimpleXMLElement(file_get_contents($file));
                $menu = new TMenu($xml, $callback, 1, 'nav collapse', '');
                $menu->class = 'nav';
                $menu->id    = 'side-menu';
                $menu->show();
                $menu_string = ob_get_clean();
                return $menu_string;
                break;
            default:
                ob_start();
                $callback = array('SystemPermission', 'checkPermission');
                $xml = new SimpleXMLElement(file_get_contents($file));
                $menu = new TMenu($xml, $callback, 1, 'treeview-menu', 'treeview', '');
                $menu->class = 'sidebar-menu';
                $menu->id    = 'side-menu';
                $menu->show();
                $menu_string = ob_get_clean();
                return $menu_string;
                break;
        }
    }
}