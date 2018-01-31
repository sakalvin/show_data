<?php
class calldata{
		
	function connection(){
		$dbuser = "root";
		$dbpass = "root";
		$dbhost = "localhost";
		$conn = mysqli_connect($dbhost, $dbuser, $dbpass);
		return $conn;
	}
	
	function select_table($query){
		
		$connection = $this->connection();
		
		$exe	= mysqli_query($connection,$query);
		
		$isi = null;
		
		$isi = '
				<table class="table table-bordered table-condensed table-hover">';
		//--judul
		$isi .='
				<tr>';
		$field = mysqli_num_fields($exe);
        for ($a=0;$a<$field;$a++) {
			$kolom[$a] = mysqli_fetch_field_direct($exe,$a);
            $isi .= '
					<th>'. $kolom[$a]->name .'</th>';
        }
		$isi .='
				</tr>';
		//--end judul
		
		//--data
		if(mysqli_num_rows($exe)>=1)
		{
			while($dt=mysqli_fetch_row($exe))
			{
				$isi .='
						<tr>';
				for ($b=0;$b<$field;$b++) {
					$isi .='
							<td>'.$dt[$b].'</td>';
				}
				$isi .='
						</tr>';
			}
		}
		else {
			$isi .='
				<tr><td colspan="'.$field.'"> not found data</td></tr>';
		}
		//--end data
		
		$isi .='
				</table>';
		
		return $isi;
		mysqli_close($connection);
	}
	
	function select_table_date($query){
		
		$connection = $this->connection();
		$exe		= mysqli_query($connection,$query);
		
		//---field 0 untuk zona
		$zona 		= null;
		$nzona		= 0;
		
		//---field 1 untuk tanggal
		$tanggal 	= null;
		$ntanggal	= 0;
		
		//---field 2 untuk nilai
		$nilai 		= null;
		
		$isi = null;
		$field 		= mysqli_num_fields($exe);
		$kolom_zone = mysqli_fetch_field_direct($exe,0);
		$nama_zona	= $kolom_zone->name;
		$row 		= mysqli_num_rows($exe);
		if($row>=1)
		{
			//--data
			while($dt=mysqli_fetch_row($exe))
			{
				if($zona[$nzona]!=$dt[0]) { $nzona++; $nnilai=0; $zona[$nzona]=$dt[0];  }
				$nilai[$nzona][$dt[1]]=$dt[2]; 
				
				foreach($tanggal as $keytanggal => $valuetanggal)
				{
					if($valuetanggal==$dt[1]) { $statustanggal = 1; break; }
					else { $statustanggal = 0; }
				}
				
				if($statustanggal==0) { $ntanggal++; $tanggal[$ntanggal]=$dt[1]; }
			}
			//--end data
			
				$isi = '
				<table class="table table-bordered table-condensed table-hover">
				<tr class="info">
					<th width="20px">No</th>
					<th class="text-center">'.$nama_zona.'</th>';
				foreach($tanggal as $keytanggal => $valuetanggal)
				{
					$isi .='
							<th class="text-center" nowrap>'.$valuetanggal.'</th>';
				}
				
				$isi .='</tr>';
				$no_data=0;
				foreach($zona as $keyzona => $valuezona)
				{
					$no_data++;
					$isi .='<tr>';
					$isi .='<td align="left" nowrap>'.$no_data.'</td>';
					$isi .='<td align="left" nowrap>'.$valuezona.'</td>';
					foreach($nilai[$keyzona] as $keynilai => $valuenilai)
					{
						$isi .='<td> &nbsp; '. number_format($valuenilai,2) .'</td>';
					}
					$isi .='</tr>';
				}
				
				$isi .='
						</table>';
		}
		else {
			$isi ='not found data';
		}
		
		
		return $isi;
		mysqli_close($connection);
	}
	
	function select_data_array($query){
		
		$connection = $this->connection();
		$exe	= mysqli_query($connection,$query);
		
		$isi = null;
		
		//--judul
		$field = mysqli_num_fields($exe);
        for ($a=0;$a<$field;$a++) {
			$kolom[$a] = mysqli_fetch_field_direct($exe,$a);
            $isi['judul'][$a] = $kolom[$a]->name;
        }
		//--end judul
		
		//--data
		if(mysqli_num_rows($exe)>=1)
		{
			$no = 0;
			while($dt=mysqli_fetch_row($exe))
			{
				for ($b=0;$b<$field;$b++) {
					$isi['data'][$no][$b]=$dt[$b];
				}
				$no++;
			}
		}
		//--end data
		
		return $isi;
		mysqli_close($connection);
	}
	
	function select_table_page($query,$link){
		
		$connection = $this->connection();
		$exe	= mysqli_query($connection,$query);
		
		//paging
			$dataPerPage = 10;
			if(isset($_GET['page']))
			{ $noPage = $_GET['page']; } 
			else { $noPage = 1; }
			$offset = ($noPage - 1) * $dataPerPage;
			$data = mysqli_num_rows($exe);
			$jumData = $data;
			$jumPage = ceil($jumData/$dataPerPage);
		//end continue
		
		$query2 = $query." LIMIT $offset, $dataPerPage";
		$exe2	= mysqli_query($connection,$query2);
		
		$isi = null;
		
		$isi = '
				<table class="table table-bordered table-condensed table-hover">';
		//--judul
		$isi .='
				<tr>';
		$field = mysqli_num_fields($exe2);
        for ($a=0;$a<$field;$a++) {
			$kolom[$a] = mysqli_fetch_field_direct($exe2,$a);
            $isi .= '
					<th class="danger" nowrap>'. $kolom[$a]->name .'</th>';
        }
		$isi .='
				</tr>';
		//--end judul
		
		//--data
		if(mysqli_num_rows($exe2)>=1)
		{
			while($dt=mysqli_fetch_row($exe2))
			{
				$isi .='
						<tr>';
				for ($b=0;$b<$field;$b++) {
					$isi .='
							<td align="left" nowrap>'.$dt[$b].'</td>';
				}
				$isi .='
						</tr>';
			}
		}
		else {
			$isi .='
				<tr><td colspan="'.$field.'"> not found data</td></tr>';
		}
		//--end data
		
		$isi .='
				</table>';
				
			//continue pagging
			$isi .='
			<ul class="pagination">';
	
					if ($noPage > 1) {
							$isi .='<li><a style="cursor:pointer;" onClick="jin_ajax_req(\''.$_SERVER["PHP_SELF"].'?page='.($noPage-1).$link.'\', \'showdata\');">Prev</a></li>';
					}
					
					for($page = 1; $page <= $jumPage; $page++)
					{
							 if ((($page >= $noPage - 3) && ($page <= $noPage + 3)) || ($page == 1) || ($page == $jumPage)) 
							 {   
								if (($showPage == 1) && ($page != 2))  $isi .='<li class="active"><a>...</a></li>'; 
								if (($showPage != ($jumPage - 1)) && ($page == $jumPage))  $isi .='<li class="active"><a>...</a></li>';
								if ($page == $noPage) $isi .=' <li class="active"><a>'.$page.'</a></li>';
								else {
										$isi .='<li><a style="cursor:pointer;" onClick="jin_ajax_req(\''.$_SERVER["PHP_SELF"].'?page='.$page.$link.'\', \'showdata\');">'.$page.'</a></li>';
								}
								$showPage = $page;          
							 }
					}
						
						
				if ($noPage < $jumPage) {
					$isi .='<li><a style="cursor:pointer;" onClick="jin_ajax_req(\''.$_SERVER["PHP_SELF"].'?page='.($noPage+1).$link.'\', \'showdata\');">Next</a></li>';
				}
				
					$isi .='</ul>';
			//end pagging
		
		return $isi;
		mysqli_close($connection);
	}
	
	function download_csv($query,$name)
	{
		function cleanData(&$str)
		  {
			if($str == 't') $str = 'TRUE';
			if($str == 'f') $str = 'FALSE';
			if(preg_match("/^0/", $str) || preg_match("/^\+?\d{8,}$/", $str) || preg_match("/^\d{4}.\d{1,2}.\d{1,2}/", $str)) {
			  $str = "$str";
			}
			if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
		  }
		  
		  $connection = $this->connection();
		  
		  $sql = mysqli_query($connection,$query);
		  		  // filename for download
		  $filename = "download" . $name . ".csv";

		  header("Content-Disposition: attachment; filename=\"$filename\"");
		  header("Content-Type: text/csv");

		  $out = fopen("php://output", 'w');

		  $flag = false;
		  while(false !== ($row = mysqli_fetch_assoc($sql))) {
			if(!$flag) {
			  // display field/column names as first row
			  fputcsv($out, array_keys($row), ';', '"');
			  $flag = true;
			}
			array_walk($row, 'cleanData');
			fputcsv($out, array_values($row), ';', '"');
		  }

	  fclose($out);
	  exit;
		mysqli_close($connection);
	}
	
	function download_xls_date($query)
	{
		$filename ="data_xls_".date('Y-m-d').".xls";
		header('Content-type: application/ms-excel');
		header('Content-Disposition: attachment; filename='.$filename);
		
		$connection = $this->connection();
		$exe		= mysqli_query($connection,$query);
		
		//---field 0 untuk zona
		$zona 		= null;
		$nzona		= 0;
		
		//---field 1 untuk tanggal
		$tanggal 	= null;
		$ntanggal	= 0;
		
		//---field 2 untuk nilai
		$nilai 		= null;
		$nnilai		= 0;
		
		$isi = null;
		$field 		= mysqli_num_fields($exe);
		$kolom_zona = mysqli_fetch_field_direct($exe,0);
		$nama_zona	= $kolom_zona->name;
		$row 		= mysqli_num_rows($exe);
		if($row>=1)
		{
			//--data
			while($dt=mysqli_fetch_row($exe))
			{
				if($zona[$nzona]!=$dt[0]) { $nzona++; $nnilai=0; $zona[$nzona]=$dt[0];  }
				if($nilai[$nzona][$nnilai]!=$dt[2]) { $nnilai++; $nilai[$nzona][$nnilai]=$dt[2]; }
				
				foreach($tanggal as $keytanggal => $valuetanggal)
				{
					if($valuetanggal==$dt[1]) { $statustanggal = 1; break; }
					else { $statustanggal = 0; }
				}
				
				if($statustanggal==0) { $ntanggal++; $tanggal[$ntanggal]=$dt[1]; }
			}
			//--end data
			
				$isi = '
				<table class="table table-bordered table-condensed table-hover">
				<tr class="info">
					<th width="20px">No</th>
					<th class="text-center">'.$nama_zona.'</th>';
				foreach($tanggal as $keytanggal => $valuetanggal)
				{
					$isi .='
							<th class="text-center" nowrap>'.$valuetanggal.'</th>';
				}
				
				$isi .='</tr>';
				$no_data=0;
				foreach($zona as $keyzona => $valuezona)
				{
					$no_data++;
					$isi .='<tr>';
					$isi .='<td align="left" nowrap>'.$no_data.'</td>';
					$isi .='<td align="left" nowrap>'.$valuezona.'</td>';
					foreach($nilai[$keyzona] as $keynilai => $valuenilai)
					{
						$isi .='<td>'. number_format($valuenilai,2) .'</td>';
					}
					$isi .='</tr>';
				}
				
				$isi .='
						</table>';
		}
		else {
			$isi ='not found data';
		}
		
		
		echo $isi;
		mysqli_close($connection);
	}
	
	function select_data_chart($query)
	{		
		$fusionchart_lib 	= "plugins/fusioncharts/FusionCharts/FusionCharts.js";
		$fusionchart_swf 	= "plugins/fusioncharts/FusionCharts/MSLine.swf";
		
		
		$lebar_chart 		= "100%";
		$tinggi_chart 		= "350";
	
		$connection = $this->connection();
		$exe		= mysqli_query($connection,$query);
		
		//---field 0 untuk zona
		$zona 		= null;
		$nzona		= 0;
		
		//---field 1 untuk tanggal
		$tanggal 	= null;
		$ntanggal	= 0;
		
		//---field 2 untuk nilai
		$nilai 		= null;
		$nnilai		= 0;
		
		$isi = null;
		$field 		= mysqli_num_fields($exe);
		$kolom_zona = mysqli_fetch_field_direct($exe,0);
		$nama_zona	= $kolom_zona->name;
		$row 		= mysqli_num_rows($exe);
		if($row>=1)
		{
			//--data
			while($dt=mysqli_fetch_row($exe))
			{
				if($zona[$nzona]!=$dt[0]) { $nzona++; $nnilai=0; $zona[$nzona]=$dt[0];  }
				if($nilai[$nzona][$nnilai]!=$dt[2]) { $nnilai++; $nilai[$nzona][$nnilai]=$dt[2]; }
				
				foreach($tanggal as $keytanggal => $valuetanggal)
				{
					if($valuetanggal==$dt[1]) { $statustanggal = 1; break; }
					else { $statustanggal = 0; }
				}
				
				if($statustanggal==0) { $ntanggal++; $tanggal[$ntanggal]=$dt[1]; }
			}
			//--end data
			
			
				$category 	= "<categories>";
			
				foreach($tanggal as $keytanggal => $valuetanggal)
				{
					$category.="<category name='".$valuetanggal."' />";
				}
				$category.="</categories>";
				
				foreach($zona as $keyzona => $valuezona)
				{
					$dataset	.= "<dataset seriesname='".$valuezona."' renderas='Line'>";
					foreach($nilai[$keyzona] as $keynilai => $valuenilai)
					{
						$dataset.= "<set value='".number_format($valuenilai,2)."' />";
					}
					$dataset.= "</dataset>";
				}
				
				$isi ='<script language="Javascript" src="'.$fusionchart_lib.'"></script>
						<object width="'.$lebar_chart.'" height="'.$tinggi_chart.'" id="MSLine" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" >
							<param name="movie" value="'.$fusionchart_swf.'" />
							<embed src="'.$fusionchart_swf.'" flashVars="&chartWidth=660&chartHeight=300&DOMId=myChartId&registerWithJS=1&debugMode=0&dataXML=
							<chart caption=\''.$nama_zona.'\' yAxisName=\'PERSEN\' YAxisMaxValue=\'100\' showValues=\'0\' showborder=\'0\' formatNumberScale=\'0\' sformatNumberScale=\'0\' setAdaptiveYMin=\'1\' thousandSeparator=\'.\' decimalSeparator=\',\' labelDisplay=\'Rotate\' slantLabels=\'2\' basefontcolor=\'000\' numdivlines=\'7\' plotGradientColor=\'\'>
								'.$category.'
								'.$dataset.'
								</chart>" width="'. $lebar_chart.'" height="'.$tinggi_chart.'" name="Area" 
										  quality="high" 
										  type="application/x-shockwave-flash" 
										  pluginspage="http://www.macromedia.com/go/getflashplayer" />
										
								  </object>';
		}
		else {
			$isi ='not found data';
		}
		
		
		return $isi;
		mysqli_close($connection);
	}
}
?>