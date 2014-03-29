<?php

/*
 * This file is part of the slatr project
 * Copyleft Alex Bello
 *
 * For more information visit: http://github.com/alexmbe/slatr 
 *
 * slatr is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * slatr is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with slatr.  If not, see http://www.gnu.org/licenses/
 */

define('SLA', true);
require_once("code.init.php");

if (!$user || !$sec->CheckCsrfToken($_GET['token'])) {
	// Exit silently as per OWASP 2007 A6
	exit;
}

set_time_limit(600);

require_once "./include/excellib/class.writeexcel_workbook.inc.php";
require_once "./include/excellib/class.writeexcel_worksheet.inc.php";

//$fname = tempnam(dirname(__FILE__)."/temp", "report.xls");
$fname = tempnam("./temp", "report.xls");
$workbook =& new writeexcel_workbook($fname);
$worksheet =& $workbook->addworksheet('SLA Report');


//Set column width
$worksheet->set_column('A:A', 20);
$worksheet->set_column('B:B', 20);
$worksheet->set_column('C:C', 20);
$worksheet->set_column('D:D', 20);
$worksheet->set_column('E:E', 20);
$worksheet->set_column('F:F', 20);
$worksheet->set_column('G:G', 30);

//Set formats for headers, table headers and data
$header_format =& $workbook->addformat(array(
                                            bold    => 0,
                                            color   => 'black',
                                            size    => 12,
                                            fg_color => 'white',
                                            border   => '1',
                                            align => 'left', 
                                            border_color =>"silver"                                 
                                        ));
$header_format_bold =& $workbook->addformat(array(
                                            bold    => 1,
                                            color   => 'black',
                                            size    => 12,
                                            fg_color => 'white',
                                            border   => '1',
                                            border_color =>"silver"                                  
                                        ));                                        

//Set header format, and headers
$report_format =& $workbook->addformat(array(
                                            bold    => 0,
                                            color   => 'black',
                                            size    => 10,
                                            fg_color => 'white',
                                            align => 'center',
                                            border   => '1',
                                        ));
$report_format_yellow =& $workbook->addformat(array(
                                            bold    => 0,
                                            color   => 'black',
                                            size    => 10,
                                            fg_color => 'yellow',
                                            align => 'center',
                                            border   => '1', 
                                        ));
$report_format_red =& $workbook->addformat(array(
                                            bold    => 0,
                                            color   => 'black',
                                            size    => 10,
                                            fg_color => 'red',
                                            align => 'center',
                                            border   => '1',
                                        ));
$report_format_bold =& $workbook->addformat(array(
                                            bold    => 1,
                                            color   => 'black',
                                            size    => 10,
                                            fg_color => 'white',
                                            align => 'center',
                                            border   => '1', 
                                        ));                                        
$bold_format =& $workbook->addformat(array(bold    => 1,border   => '1'));

$tbl_header_format =& $workbook->addformat(array(
                                            bold    => 1,
                                            color   => 'black',
                                            size    => 10,
                                            fg_color => 'silver',
                                            align => 'center',
                                            border   => '1',
                                            border_color =>"silver"                                
                                        ));

# Only one cell should contain text, the others should be blank.
$worksheet->write      ("A1", "Reported Period: ", $header_format);
$worksheet->write("B1",date("F j, Y",$_SESSION['startdate'])." - ".date("F j, Y",$_SESSION['enddate']- 24*60*60),$header_format_bold);
$worksheet->write_blank("C1",                 $header_format);
$worksheet->write_blank("D1",                 $header_format);
$worksheet->write_blank("E1",                 $header_format);
$worksheet->write_blank("F1",                 $header_format);
$worksheet->write_blank("G1",                 $header_format);

//$worksheet->write      ("A3", "All sites", $header_format);

//write headers for all sites table
$worksheet->write("A4", "Site Name", $tbl_header_format);
$worksheet->write("B4","Uptime (%)",$tbl_header_format);
$worksheet->write("C4","SLA (%)",$tbl_header_format);
$worksheet->write("D4","Full outage (min)",$tbl_header_format);
$worksheet->write("E4","Partial outage (min)",$tbl_header_format);
$worksheet->write("F4","Maintenance (min)",$tbl_header_format);




			$allEvents = $db->SelectAll("select * from events WHERE 
				 	(end_date > %d AND end_date <= %d) OR
			 		(start_date >= %d AND start_date < %d) order by start_date",
				$_SESSION['startdate'], $_SESSION['enddate'], $_SESSION['startdate'], $_SESSION['enddate']
				);
	  		
			 $period_in_min = ($_SESSION[enddate] - $_SESSION[startdate])/60;

				//setup start values
  				$total_full_outgage = 0;
 				$total_partial_outgage = 0;
 				$total_maintanance = 0;
 				$total_uptime = 0;
 				$allSitesId = array();
 				//check if we need report for all sites
 				if((array_search('all', $_SESSION['sites'])!==false)){          	 
 					$allSites = $db->SelectAll("select * from sites order by site_order");
 					foreach($allSites  as $site) $allSitesId[] = $site->id;
 				}else{
 					$allSitesId = $_SESSION['sites'];
 				}
 				$i = 5;						//excel row counter
 				$no_of_sites = 0;			//site counter		
 				foreach($allSitesId as $siteId){
 					$no_of_sites++;
 					$site = $db->SelectOne("select * from sites where id=%d order by site_order", $siteId);
 					$full_outgage = 0;
 					$partial_outgage = 0;
 					$maintanance = 0;
 					foreach($allEvents as $event){
 						//check if site is affected by event
 						$allAffectedSites = explode(",",$event->sites); 
 						
 						if(array_search($site->id, $allAffectedSites)!==false){ 
 							if($event->start_date < $_SESSION['startdate']) $event->start_date = $_SESSION['startdate'];
 							if($event->end_date > $_SESSION['enddate']) $event->end_date = $_SESSION['enddate'];
 								
 							if($event->type == '1') $full_outgage += ($event->end_date - $event->start_date)/60;
 							if($event->type == '2') $partial_outgage += ($event->end_date - $event->start_date)/60;
 							if($event->type == '3') $maintanance += ($event->end_date - $event->start_date)/60; 	
 						}
 					}
 					$uptime = number_format((($period_in_min - $full_outgage)/$period_in_min)*100,2);
 					$total_uptime += $uptime;
 					$total_full_outgage += $full_outgage;
 					$total_partial_outgage += $partial_outgage;
 					$total_maintanance += $maintanance;
 					//write data to excel file
					
					if ((float)$uptime < (float)$site->sla)
					{
						$format = $report_format_red;
					}
					elseif ((float)$uptime > (float)$site->sla && (float)$uptime < 100)
					{
					$format = $report_format_yellow;
					}
					else
					{
					$format = $report_format;
					}
					$worksheet->write("A{$i}", $site->sitename, $format);
					$worksheet->write("B{$i}",$uptime,$format);
					$worksheet->write("C{$i}",$site->sla,$format);
					$worksheet->write("D{$i}",$full_outgage,$format);
					$worksheet->write("E{$i}",$partial_outgage,$format);
					$worksheet->write("F{$i}",$maintanance,$format);
 					$i++;	

                }//for all sites
                
                
					//write data for total row                  
					$i = $i + 1;
					$worksheet->write("A{$i}", "Total: ", $report_format_bold);
					$worksheet->write("B{$i}",number_format($total_uptime/$no_of_sites,2),$report_format_bold);
					$worksheet->write("C{$i}","",$report_format_bold);
					$worksheet->write("D{$i}",$total_full_outgage,$report_format_bold);
					$worksheet->write("E{$i}",$total_partial_outgage,$report_format_bold);
					$worksheet->write("F{$i}",$total_maintanance,$report_format_bold);

					//Set label
					$i = $i + 3;
					$worksheet->write      ("A{$i}", "Events summary", $header_format_bold);            
					$i = $i + 3;
					//set table headers
					$worksheet->write("A{$i}", "Event Type", $tbl_header_format);
					$worksheet->write("B{$i}","Event Category",$tbl_header_format);
					$worksheet->write("C{$i}","Start Date",$tbl_header_format);
					$worksheet->write("D{$i}","End Date",$tbl_header_format);
					$worksheet->write("E{$i}","Duration",$tbl_header_format);
					$worksheet->write("F{$i}","Affected Site(s)",$tbl_header_format);
					$worksheet->write("G{$i}","Description",$tbl_header_format);
					
					$i = $i + 1;
                    


                $allSitesId = array();
                if((array_search('all', $_SESSION['sites'])!==false)){          	 
 					$allSites = $db->SelectAll("select * from sites order by site_order");
 					foreach($allSites  as $site) $allSitesId[] = $site->id;
 				}else{
 					$allSitesId = $_SESSION['sites'];
 				}
 				

                foreach($allEvents as $event)
                {
                	if($event->start_date < $_SESSION[startdate]) $event->start_date = $_SESSION[startdate];
 					if($event->end_date > $_SESSION[enddate]) $event->end_date = $_SESSION[enddate];
 							
                	$allSites = array();
                    $allSitesIds = explode(",", $event->sites);
                    $event_ok = '0';
                    foreach($allSitesIds as $siteId){
                    	//foreach($_POST['sites'] as $siteReportId){
 							if(array_search($siteId, $allSitesId) !== false){
 								$event_ok='1';
 							}
                	//}
						$allSites[] = $db->SelectOne("select * from sites where id=%d order by site_order", $siteId);
                    }
                    if ($event_ok == '0') continue; 
                    $site_names = "";
                    foreach($allSites as $site){
                    	$site_names .= chr(10);
                    	$site_names .= $site->sitename;
                    }
                    $site_names = substr($site_names,1);

					//setup text wrap                    
					$report_format->set_text_wrap();						  
					$report_format->set_align('center');
					$report_format->set_align('vcenter');
					//write data to excel rows						
					$worksheet->write("A{$i}", $allEventTypes[$event->type], $report_format);
					$worksheet->write("B{$i}",$allEventCategories[$event->category],$report_format);
					$worksheet->write("C{$i}",date("m/d/Y",$event->start_date)."  ".date("h:i a",$event->start_date),$report_format);
					$worksheet->write("D{$i}",date("m/d/Y",$event->end_date)."  ".date("h:i a",$event->end_date),$report_format);
					$worksheet->write("E{$i}",($event->end_date-$event->start_date)/60,$report_format);
					$worksheet->write("F{$i}",$site_names,$report_format);
					$worksheet->write("G{$i}",$event->description,$report_format);
					$i++;

                }   




$workbook->close();

$file_name = "sla_report_".date("m_d_Y",time()).".xls";
header("Content-Type: application/x-msexcel; name=\"$file_name\"");
header("Content-Disposition: inline; filename=\"$file_name\"");
$fh=fopen($fname, "rb");
fpassthru($fh);
unlink($fname);
?>
