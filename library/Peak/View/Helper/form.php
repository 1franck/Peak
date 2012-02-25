<?php
/**
 * Access to /form/ objects helpers
 *
 * @author  Francois Lajoie
 * @version $Id$
 */
class Peak_View_Helper_form
{
    /**
     * Select form element 
     *
     * @return Peak_View_Helper_Form_Select
     */
    public function select()
    {
        return new Peak_View_Helper_form_select();
    }
    
    /**
     * Input form element
     *
     * @return Peak_View_Helper_Form_Input
     */
    public function input()
    {
        return new Peak_View_Helper_form_input();
    }
}