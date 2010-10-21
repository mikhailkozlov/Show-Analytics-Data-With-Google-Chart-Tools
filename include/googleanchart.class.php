<?php 
/*
 * Author: Mikhail A Kozlov
 * Website: http://mikhailkozlov.com/
 * Version: 0.1
 * 
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 * 
 * 
 * 
 */


class ChartLight{
	private $url='http://chart.apis.google.com/chart?'; 
	private $data = array(
		'chtt'=>'', // chart title
		'cht'=>'bvs', // chart grouping type (bvs - stacked, bvg - grouped )
		'chxt'=>'y,x', // chart axis 
		'chbh'=>array('width'=>'a','spacing'=>'5','gspacing'=>'10'), // bar size: width (a - auto, r- rela, n - size like 20), spacing (number), group spacing (number)
		'chs'=>array('width'=>'600','height'=>'225'), // chart size
		'chco'=>array('FF0000'), // color
		'chg'=>array(0,10,0,0),  // chart grid
		'chts'=>array(676767,11), // color, size
		'chds'=>'', // data set range like: 0,40
		'chxr'=>array(), // axis range	0,0,40|1,0,30  y must be o to max value, second 0 to number of elements
		'chd'=>array(), // data set 
		'chxl'=>array(), // axis x labels 1:|1|2|3|4|5|6|7|8|9|10| will create range

	);
	private $values = array();
	
	function __construct($input=array(),$format=false){
		// let's take style from holder and apply it to the graph
		if($format!== false && is_array($format)){
			// set dimensions
			$this->data['chs']['width'] = (array_key_exists('width',$format)) ? $format['width']:$this->data['chs']['width']; 
			$this->data['chs']['height'] = (array_key_exists('height',$format)) ? $format['height']:$this->data['chs']['height'];
			// set custom color
			if( array_key_exists('color',$format) ){
				$format['color'] = trim($format['color']);
				$format['color'] = ltrim($format['color'],'rgb(');
				$format['color'] = rtrim($format['color'],')');
				$format['color'] = explode(',',$format['color']);
				foreach($format['color'] as $i=>$vl){
					$format['color'][$i] = dechex(intval($vl)); 
				}
				$this->data['chco']=implode('',$format['color']);
			}
		}
		
		// before we start we need to count data and find max value
		if(count($input)>0){
			$key = key(current($input));
			$max = 0;
			foreach($input as $k=>$v){
				$x[]=substr($k,6,2); // date
				$y[]=$v[$key];
				$max = ($v[$key] > $max) ? $v[$key]:$max;
			}
			$this->chd = array(implode(',',$y));
			$this->chxl = array('x'=>implode('|',$x));
			$this->chds = array(0,($max+10));
			$this->chxr = array('0,0,'.($max+10),'1,0,'.count($input));
		}
	}
	
	function __set($name,$val){
		$this->data[$name]=$val;
	}
	
	function buildLink(){
		$aUrl = array();
		foreach($this->data as $k=>$v){
			if(is_array($v)){
				switch($k){
					case 'chds': 
						$aUrl[] = $k.'='.implode(',',$v);
					break;
					case 'chxr': //axis range
						$aUrl[] = $k.'='.implode('|',$v);
					break;
					case 'chxl': // labels
						if(count($v) > 0){
							$s = $k.'=';
							if(array_key_exists('y',$v)){
								if(is_array($v['y'])){
									$s .= '0:|'.implode('|',range($v['y'][0],$v['y'][1],$v['y'][2]));
								}else{
									$s .= '01:|'.$v['y'];
								}
							}
							if(array_key_exists('x',$v)){
								$s .=(array_key_exists('y',$v)) ? '|':'';
								if(is_array($v['x'])){
									$s .= '1:|'.implode('|',range($v['x'][0],$v['x'][1],$v['x'][2]));
								}else{
									$s .= '1:|'.$v['x'];
								}
							}
							if($k.'=' != $s){
								$aUrl[] = $s;
							}
							unset($s);
						}
					break;
					case 'chd': // build data sets
						if(count($v) > 0){
							$aUrl[] = $k.'=t:'.implode('|',$v);
						}
					break;
					case 'chs': // size has x in the middle
						$aUrl[] = $k.'='.implode('x',$v);
					break;
					default:
						$aUrl[] = $k.'='.implode(',',$v);
					break;
				}
			}else{
				if(!empty($v)){
					$aUrl[] = $k.'='.$v;
				}
			}
		}
		return implode('&amp;',$aUrl);
	}
	function draw(){
		return '<div style="height:0; overflow:hidden"><img src="'.$this->url.$this->buildLink().'" alt="" width="'.$this->data['chs']['width'].'" height="'.$this->data['chs']['height'].'" border="0" /></div>';
	}
}