<?php
// include the Google Analytics PHP class
	include('include/googleanalytics.class.php');
	include('include/googleanchart.class.php');
	try {
		$aParams = (isset($_POST)) ? $_POST:$_GET;
		if(is_array($aParams['class'])){
			foreach($aParams['class'] as $i=>$val){
				switch($val){
					case 'visits':
						$aParams['metric'] = $val;
					break;
					case 'line':
					case 'bars':
						$aParams['type'] = $val;
					break;
					case (stripos($val,'last_') !== false):
					case (stripos($val,'this_') !== false):
						$aParams['range'] = $val;
					break;
					
				}
			}
		}else{
			$metric = (!empty($aParams['class'])) ? $aParams['class']:'visits';
		}
		// create an instance of the GoogleAnalytics class using your own Google {email} and {password}
		$ga = new GoogleAnalytics('[GOOGLE_ACCOUNT]','[PASSWORD]');
		// set the Google Analytics profile you want to access - format is 'ga:123456';
		$ga->setProfile('ga:[ACCOUNT#NUMBER]');

		// set the date range we want for the report - format is YYYY-MM-DD
		if(array_key_exists('range',$aParams)){
			$aParams['range'] = explode('_',$aParams['range']);
			switch($aParams['range'][0]){
				case 'last':
					$r = '-';
					$r .= (intval($aParams['range'][1]) > 0) ? intval($aParams['range'][1]):'30';
					$r .= (array_key_exists(2,$aParams['range'])) ? ' '.$aParams['range'][2]:' days';
					$ga->setDateRange(date('Y-m-d',strtotime($r)) , date('Y-m-d',strtotime('now')) );
				break;
			}
		}else{		
			$ga->setDateRange(date('Y-m-d',strtotime('-30 days')) , date('Y-m-d',strtotime('now')) );
		}
		
		// get the report for date and country filtered by Australia, showing pageviews and visits
		$report = $ga->getReport(
			array('dimensions'=>urlencode('ga:date'),
				'metrics'=>urlencode('ga:'.$aParams['metric'])
				)
			);
		//ksort($report);
		
		//print out the $report array
		$c = new ChartLight($report,$aParams);
		// show graph
		echo $c->draw();
	} catch (Exception $e) { 
		print 'Error: ' . $e->getMessage(); 
	}
?>