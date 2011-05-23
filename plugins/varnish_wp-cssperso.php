<?php
/*
 * Support for personnalized css 
 * Url none
 * Version 1
 */
class WPVarnishPurger_WPCssPerso extends WPVarnishPurgerCore {
   
   function mustActivate(){
      return true; 
   }      
  
   function addActions(){      
        add_action('update_option_css_perso', array(&$this, 'WPVarnishPurgerPurgeAll'), 99);     
   }
   
}
$WPVarnishPurgerCssPerso = & new WPVarnishPurger_WPCssPerso();

