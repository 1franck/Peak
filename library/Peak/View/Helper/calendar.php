<?php

/**
 * Peak Simple Calendar helper
 * 
 * @author  Francois Lajoie
 * @version $Id$
 */
class View_Helper_Calendar
{   
            
    public function thisMonth()
    {
        $calendar = array();
		
		$year = date('Y');
		$month = date('m');
		
		# date('N'); work only with php5 >= 5.1 
		//$firstdayname = date('N', mktime(0, 0, 0, $month, 1, $year));	
		$days = array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun');
        $firstdayname = array_search(date('D',mktime(0, 0, 0, $month, 1, $year)), $days) + 1;
        
		$monthdays = date('t');	
		
		for($i = 1;$i <= $firstdayname;++$i) { $calendar[] = ''; }		
		for($i = 1;$i <= $monthdays;++$i) { $calendar[] =  $i; }
	
		$total = count($calendar);
		
		if($total > 28)	{
			for($i = $total;$i < 35;++$i) { $calendar[$i] = ''; }	
		}    
		else if($total > 35)	{
			for($i = $total;$i < 42;++$i) { $calendar[$i] = ''; }	
		}
		
		$todaykey = date('j') + ($firstdayname - 2);
		$calendar[$todaykey] = $calendar[$todaykey];

		return $calendar;	
    }
    
}