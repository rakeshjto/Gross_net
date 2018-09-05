<?php
/*error_reporting(E_ALL);
 ini_set('display_errors', 1);*/
set_time_limit(900);


include('head.html');
$conn = odbc_connect("reports","reports","reports123");
$fdate= strtoupper($_POST['fdate']);
$tdate= strtoupper($_POST['tdate']);
$first_day_of_fdate= strtoupper(date('01-M-Y', strtotime($fdate)));
$first_day_of_tdate= strtoupper(date('01-M-Y', strtotime($tdate)));
$sno=1; 
switch ($_POST['svc'])
{
	case "LL": // IF THE SERVICE IS LL
		$sql= "SELECT A.SDE,C.LL_TAR,
count(case when ORDER_TYPE='New' AND ORDER_SUB_TYPE='Provision' then 1 end) LLPROV,
count(case when ORDER_TYPE='New' AND ORDER_SUB_TYPE='Provision' AND RECONNECTION_ORDER='Y' then 1 end) RC,
count(case when ORDER_TYPE='Disconnect' AND ORDER_SUB_TYPE IN ('Disconnect','Disconnect Due to NP','Disconnect Due to Misuse') then 1 end) LLDIS
from 
EXCHANGE_CODE A, CDR_CRM_ORDERS B, (SELECT SDE,SUM(LL_TAR) LL_TAR FROM MON_TARGETS_SDE_WISE WHERE MON_YY BETWEEN '" .$first_day_of_fdate. "' AND '" .$first_day_of_tdate. "' GROUP BY SDE) C 
WHERE A.EXCHANGE_CODE=B.EXCHANGE_CODE AND A.SDE=C.SDE(+) AND
TRUNC(ORDER_COMP_DATE) BETWEEN '" . $fdate . "' AND '" . $tdate . "'  AND
SERVICE_TYPE in ('Landline', 'ISDN', 'DSPT', 'LL PCO') AND ORDER_STATUS='Complete' 
group by A.SDE,C.LL_TAR ORDER BY SUBSTR(A.SDE,-3)";
		

		//echo $sql;
		$odbcexec = odbc_exec($conn,$sql);
		echo "<h2>LandLine Gross and Net from " . $fdate ." to " .$tdate. "</h2><br>";
		echo "<table class='datagrid' border='1' width='60%'>";
		echo '<thead>';
		echo "<tr>";
		echo "<th>SNo</th>";
		echo "<th>SDE</th>";
		echo "<th>Target</th>";
		//echo "<th>SDE NAME</th>";
		//echo "<th colspan='2'>Annual<br>Target</th>";
		//echo "<th>SDE HR No</th>";
		echo "<th>LL Provisions<sub>(Reconnections)</sub></th>";
		echo "<th>Disconnetions</th>";
		echo "<th>Net<br>Achievement</th>";
		echo "</tr>";
		echo '</thead>';
		 $sn=1;
		while($data = odbc_fetch_array($odbcexec))
		{
			echo "<tr>";
			echo "<td>" .$sn. "</a></td>";
			echo "<td>" .$data['SDE']. "</a></td>";
			echo "<td>" .$data['LL_TAR']. "</td>";
			echo "<td><a href='gross_net_det.php?sde=". $data['SDE'] . "&fdate= " .$fdate. "&tdate=" .$tdate. "&service=LL&order_type=LLPROV' target='_blank'>" .$data['LLPROV']. "</a><sub>(" .$data['RC']. ")</sub></td>";
			echo "<td><a href='gross_net_det.php?sde=". $data['SDE'] . "&fdate= " .$fdate. "&tdate=" .$tdate. "&service=LL&order_type=LLDIS' target='_blank'>" .$data['LLDIS']. "</a></td>";
			$llnet= $data['LLPROV']-$data['LLDIS'];
			echo "<td>" .$llnet. "</td>";
			echo "</tr>";
			
			$lltar= $lltar+$data['LL_TAR'];
			$gpro= $gpro+$data['LLPROV'];
			$rc= $rc+$data['RC'];
			$dis= $dis+$data['LLDIS'];
			$llnet_tot= $llnet_tot+ $llnet;
			$sn++;
		}

		echo '<tfoot>';
		echo '<tr>';
		echo "<td COLSPAN='2'>Total</td>";
		//echo "<th>". $fy_tar_gross ."</th>";
		echo "<th>". $lltar ."</th>";
		echo "<th><a href='gross_net_det.php?sde=SSA&fdate= " .$fdate. "&tdate=" .$tdate. "&service=LL&order_type=LLPROV' target='_blank'>". $gpro ."<sub>(" .$rc. ")</sub></a></th>";
		echo "<th><a href='gross_net_det.php?sde=SSA&fdate= " .$fdate. "&tdate=" .$tdate. "&service=LL&order_type=LLDIS' target='_blank'>". $dis ."</a></th>";
		echo "<th>". $llnet_tot ."</th>";
		echo '</tr>';
		echo '</tfoot>';
		echo "</table><br>";
		//echo "<a href='gross_net_ssa_detailed.php?fdate=" .$fdate. "&tdate=" .$tdate. "&svc=" .$_POST['svc']. "' target='_blank'><button type='button' class='btn btn-success btn-lg pull-right'>Click Here for Detailed Information</button></a>";
		
		break;
						
	case "BB": // IF THE SERVICE IS BB

		$sql= "SELECT A.SDE,C.BB_TAR,
count(case when ORDER_TYPE='Modify' AND ORDER_sub_type='Broadband Provision'  then 1 end) BBPROV,
count(case when ((ORDER_TYPE='Disconnect' AND ORDER_SUB_TYPE IN ('Disconnect','Disconnect Due to Misuse','Disconnect Due to NP') AND BB_CONN_TYPE='POSTPAID' AND BB_USER_ID IS NOT NULL) OR (ORDER_TYPE='Modify' AND ORDER_SUB_TYPE='Broadband Disconnection')) then 1 end) BBDIS
from
EXCHANGE_CODE A, CDR_CRM_ORDERS B, (SELECT SDE,SUM(BB_TAR) BB_TAR FROM MON_TARGETS_SDE_WISE WHERE MON_YY BETWEEN '" .$first_day_of_fdate. "' AND '" .$first_day_of_tdate. "' GROUP BY SDE) C 
WHERE A.EXCHANGE_CODE=B.EXCHANGE_CODE AND A.SDE=C.SDE(+) AND
TRUNC(ORDER_COMP_DATE) BETWEEN '" . $fdate . "' AND '" . $tdate . "' AND
SERVICE_TYPE in ('Landline', 'ISDN', 'DSPT', 'LL PCO') AND ORDER_STATUS='Complete'
group by A.SDE,C.BB_TAR ORDER BY SUBSTR(A.SDE,-3)";

		//echo $sql;
		$odbcexec = odbc_exec($conn,$sql);

		echo "<h2>Broadband Gross and Net from " . $fdate ." to " .$tdate. "</h2><br>";
		echo "<table class='datagrid' border='1' width='60%'>";
		echo '<thead>';
		echo "<tr>";
		echo "<th>SNo</th>";
		echo "<th>SDE</th>";
		echo "<th>Target</th>";
		echo "<th>BB Provisions</th>";
		echo "<th>BB Disconnetions</th>";
		echo "<th>Net<br>Achievement</th>";
		echo "</tr>";
		echo '</thead>';
		$sn=1;
		while($data = odbc_fetch_array($odbcexec))
		{
			echo "<tr>";
			echo "<td>" .$sn. "</a></td>";
			echo "<td>" .$data['SDE']. "</a></td>";
			echo "<td>" .$data['BB_TAR']. "</td>";
			echo "<td><a href='gross_net_det.php?sde=". $data['SDE'] . "&fdate=" .$fdate. "&tdate=" .$tdate. "&service=BB&order_type=BBPROV' target='_blank'>" .$data['BBPROV']. "</a></td>";
			echo "<td><a href='gross_net_det.php?sde=". $data['SDE'] . "&fdate=" .$fdate. "&tdate=" .$tdate. "&service=BB&order_type=BBDIS' target='_blank'>" .$data['BBDIS']. "</a></td>";
			$bbnet= $data['BBPROV']-$data['BBDIS'];
			echo "<td>" .$bbnet. "</td>";
			echo "</tr>";
			$bbtar= $bbtar+$data['BB_TAR'];
			$gpro= $gpro+$data['BBPROV'];
			$bbdis= $bbdis+$data['BBDIS'];
			$bbnet_total= $bbnet_total+ $bbnet;
			$sn++;
		}
		// @todo Click Here for Detailed Info link to be given
		// @todo DataTables search box, Info alignment
		echo '<tfoot>';
		echo '<tr>';
		echo "<td colspan='2'> Total</td>";
		echo "<td>". $bbtar ."</td>";
		echo "<td><a href='gross_net_det.php?sde=SSA&fdate= " .$fdate. "&tdate=" .$tdate. "&service=BB&order_type=BBPROV' target='_blank'>". $gpro ."</a></td>";
		echo "<td><a href='gross_net_det.php?sde=SSA&fdate= " .$fdate. "&tdate=" .$tdate. "&service=BB&order_type=BBDIS' target='_blank'>". $bbdis ."</a></td>";
		echo "<td>". $bbnet_total ."</td>";
		echo '</tr>';
		echo '</tfoot>';

		echo "</table><br>";
		//echo "<a href='gross_net_ssa_detailed.php?fdate=" .$fdate. "&tdate=" .$tdate. "&svc=" .$_POST['svc']. "' target='_blank'><button type='button' class='btn btn-success btn-lg pull-right'>Click Here for Detailed Information</button></a>";
		break;
			

	case "FTTH": // IF THE SERVICE IS FTTH Broadband ORDER_SUB_TYPE in('VPN Provision','Broadband Provision')
		//to find out new provisions having both LL & BB		
		$sql_prov= "SELECT CUST_ACCNT_NO FROM CDR_CRM_ORDERS WHERE ORDER_TYPE='New' AND TRUNC(ORDER_COMP_DATE) BETWEEN '" . $fdate . "' AND '" . $tdate . "' AND SERVICE_TYPE LIKE '%FTTH%' AND ORDER_STATUS='Complete' GROUP BY CUST_ACCNT_NO HAVING COUNT(*)=2";
		$odbcexec1 = odbc_exec($conn,$sql_prov);
	while ($data1 = odbc_fetch_array($odbcexec1)){
			$data2 .= "'" .$data1[CUST_ACCNT_NO]. "',";	
		}
		//to find out Disconnections having both LL & BB		
		$sql_dis= "SELECT CUST_ACCNT_NO FROM CDR_CRM_ORDERS WHERE ORDER_TYPE='Disconnect' AND TRUNC(ORDER_COMP_DATE) BETWEEN '" . $fdate . "' AND '" . $tdate . "' AND SERVICE_TYPE LIKE '%FTTH%' AND ORDER_STATUS='Complete' GROUP BY CUST_ACCNT_NO HAVING COUNT(*)=2";
		$odbcexec3 = odbc_exec($conn,$sql_dis);
	while ($data3 = odbc_fetch_array($odbcexec3)){
			$data4 .= "'" .$data3[CUST_ACCNT_NO]. "',";	
		}
		//to remove duplicate criteria is find out if the same number was given as new provisions in the last 30 days 
		$sql_duplicate= "SELECT PHONE_NO FROM CDR_CRM_ORDERS WHERE ORDER_TYPE='New' AND  TRUNC(ORDER_COMP_DATE) BETWEEN TO_DATE('" . $fdate . "')-30 AND TO_DATE('" . $fdate . "')-1  AND SERVICE_TYPE LIKE '%FTTH%' AND ORDER_STATUS='Complete'";
		$odbcexecduplicate = odbc_exec($conn,$sql_duplicate);
	while ($avoid_duplicate = odbc_fetch_array($odbcexecduplicate)){
			$avoid_duplicate_data .= "'" .$avoid_duplicate[PHONE_NO]. "',";	
		}
		
		
		$sql= "SELECT * FROM 
(SELECT SUBSTR(SDE,-3) SDE,SUM(FTTH_TAR) FTTH_TAR FROM MON_TARGETS_SDE_WISE WHERE  TRUNC(MON_YY) BETWEEN '" .$first_day_of_fdate. "' AND '" .$first_day_of_tdate. "' GROUP BY SUBSTR(SDE,-3)) A,
		(SELECT SUBSTR(A.SDE,-3) SDE, 
COUNT(DISTINCT (CASE WHEN ORDER_TYPE='New' AND ORDER_SUB_TYPE='Broadband Provision' AND CUST_ACCNT_NO NOT IN (" . $data2 . "'123') AND PHONE_NO NOT IN (" . $avoid_duplicate_data . "'123') THEN PHONE_NO END)) BBONLYPROV,
COUNT(DISTINCT (CASE WHEN ORDER_TYPE='New' AND CUST_ACCNT_NO  IN (" . $data2 . "'123') AND PHONE_NO NOT IN (" . $avoid_duplicate_data . "'123') THEN PHONE_NO END)) PROV,
COUNT(DISTINCT (CASE WHEN ORDER_TYPE='Disconnect' AND SERVICE_SUB_TYPE='FTTH BroadBand' AND CUST_ACCNT_NO NOT IN (" . $data4 . "'123') THEN PHONE_NO END)) BBONLYDIS,
COUNT(DISTINCT (CASE WHEN ORDER_TYPE='Disconnect' AND CUST_ACCNT_NO  IN (" . $data4 . "'123') THEN PHONE_NO END)) DIS
FROM EXCHANGE_CODE A
LEFT JOIN CDR_CRM_ORDERS B  ON 
A.EXCHANGE_CODE=B.EXCHANGE_CODE 
AND TRUNC(B.ORDER_COMP_DATE) BETWEEN '" . $fdate . "' AND '" . $tdate . "'
AND B.SERVICE_TYPE LIKE '%FTTH%' 
AND b.ORDER_STATUS='Complete'
GROUP BY SUBSTR(A.SDE,-3))B
WHERE A.SDE=B.SDE";
		//echo $sql;
		$odbcexec = odbc_exec($conn,$sql);

		echo "<h2>FTTH Gross and Net from " . $fdate ." to " .$tdate. "</h2><br>";
		echo "<table id='datagrid' class='ftthtable' border='1' width='50%'>";
		echo '<thead>';
		echo "<tr>";
		echo "<th rowspan='2'>Sl No</th>";
		echo "<th rowspan='2'>Station</th>";
		echo "<th rowspan='2'>Gross Target</th>";
		echo "<th colspan='2'>Provisions</th>";
		echo "<th colspan='2'>Disconnetions</th>";
		echo "<th rowspan='2'>Net<br>Achievement</th>";
		echo "</tr>";
		echo "<tr><th>BB Only</th><th>LL+BB</th>";
		echo "<th>BB Only</th><th>LL+BB</th></tr>";
		echo '</thead>';
		
		$ftth_tar= 0;
		$llonlyprov= 0;
		$bbonlyprov=0;
		$prov=0;
		$llonlydis=0;
		$bbonlydis=0;
		$dis=0;
		$net= 0;
		
		while($data = odbc_fetch_array($odbcexec))
		{
			 $net_result= ($data['BBONLYPROV']+$data['PROV'])-($data['LLONLYDIS']+$data['BBONLYDIS']+$data['DIS']);
			echo "<tr>";
			echo "<td>" .$sno. "</td>";
			echo "<td><a href='gross_net_exch.php?sde=" . $data['SDE'] . "&fdate=" .$fdate. "&tdate=" .$tdate. "&service=FTTH' target='_blank'>" .$data['SDE']. "</a></td>";
			echo "<td>" .$data['FTTH_TAR']. "</td>";
			echo "<td><a href='gross_net_det.php?sde=SSA&fdate=" .$fdate. "&tdate=" .$tdate. "&service=FTTH&order_type=PROV&order_Sub_type=BB&sdca=" .$data['SDE']. "' target='_blank'>" .$data['BBONLYPROV']. "</a></td>";
			echo "<td><a href='gross_net_det.php?sde=SSA&fdate=" .$fdate. "&tdate=" .$tdate. "&service=FTTH&order_type=PROV&order_Sub_type=LLBB&sdca=" .$data['SDE']. "' target='_blank'>" .$data['PROV']. "</a></td>";
			echo "<td><a href='gross_net_det.php?sde=SSA&fdate=" .$fdate. "&tdate=" .$tdate. "&service=FTTH&order_type=DIS&order_Sub_type=BB&sdca=" .$data['SDE']. "' target='_blank'>" .$data['BBONLYDIS']. "</a></td>";
			echo "<td><a href='gross_net_det.php?sde=SSA&fdate=" .$fdate. "&tdate=" .$tdate. "&service=FTTH&order_type=DIS&order_Sub_type=LLBB&sdca=" .$data['SDE']. "' target='_blank'>" .$data['DIS']. "</a></td>";
			echo "<td>" .$net_result. "</td>";
			

			echo "</tr>";
			$bbonlyprov= $bbonlyprov + $data['BBONLYPROV']; 
			$prov= $prov+ $data['PROV']; 
			$bbonlydis= $bbonlydis + $data['BBONLYDIS']; 
			$dis= $dis + $data['DIS'];
			$net= $net+ $net_result;
			$wkg= $wkg+ $data['WKG'];
			$ftth_tar= $ftth_tar + $data['FTTH_TAR']; 
			$sno= $sno+1;
		}
		echo '<tfoot>';
		echo '<tr>';
		echo "<th colspan='2'> SSA</th>";
		echo "<th>". $ftth_tar ."</a></th>";
		echo "<th>". $bbonlyprov ."</a></th>";
		echo "<th>". $prov ."</a></th>";
		echo "<th>". $bbonlydis ."</a></th>";
		echo "<th>". $dis ."</a></th>";
		echo "<th>". $net ."</a></th>";
		echo '</tr>';
		echo '</tfoot>';
		echo "</table>";
		
		break;
			
	case "IN": // IF THE SERVICE IS IN
		$sql= "select A.PROV PROV,B.DIS DIS,(A.PROV-B.DIS) NET,C.WKG WKG from
	(SELECT COUNT(DISTINCT PHONE_NO) PROV FROM CDR_CRM_ORDERS WHERE ORDER_TYPE='New'  and TRUNC(ORDER_COMP_DATE)  BETWEEN '" . $fdate . "' AND '" . $tdate . "'  AND SERVICE_TYPE LIKE '%paid IN%' AND ORDER_STATUS='Complete' ) A,
	(SELECT COUNT(DISTINCT PHONE_NO) DIS FROM CDR_CRM_ORDERS WHERE ORDER_TYPE='Disconnect'  and TRUNC(ORDER_COMP_DATE)  BETWEEN '" . $fdate . "' AND '" . $tdate . "'   AND SERVICE_TYPE LIKE '%paid IN%' AND ORDER_STATUS='Complete' ) B,
	(SELECT COUNT(DISTINCT PHONE_NO) WKG FROM CDRHYD_WORKING_LINES WHERE SERVICE_TYPE like '%paid IN%' and SERVICE_STATUS='Active') C";
		$odbcexec = odbc_exec($conn,$sql);
		echo "<h2>IN Services Gross and Net from " . $fdate ." to " .$tdate. "</h2><br>";
		echo "<table id='datagrid' border='1' width='50%'>";
		echo '<thead>';
		echo "<tr>";
		echo "<th>SSA</th>";
		echo "<th>Provisions</th>";
		echo "<th>Disconnetions</th>";
		echo "<th>Net<br>Achievement</th>";
		echo "<th>Wkg<br>as on Date</th>";
		echo "</tr>";
		echo '</thead>';
		while($data = odbc_fetch_array($odbcexec))
		{
			echo "<tr>";
			echo "<td>Total</td>";
			echo "<td><a href='gross_net_det.php?sde=SSA&fdate= " .$fdate. "&tdate=" .$tdate. "&service=IN&order_type=PROV' target='_blank'>" .$data['PROV']. "</a></td>";
			echo "<td><a href='gross_net_det.php?sde=SSA&fdate= " .$fdate. "&tdate=" .$tdate. "&service=IN&order_type=DIS' target='_blank'>" .$data['DIS']. "</a></td>";
			echo "<td>" .$data['NET']. "</td>";
			echo "<td><a href='wkg_conns.php?stn=SSA&service=IN' target='_blank'>" .$data['WKG']. "</td>";
			echo "</tr>";
		}

		echo "</table>";
		break;


	case "NGN": // IF THE SERVICE IS LFMT
		$sql= "SELECT A.service_sub_type,NVL(B.PROV,0) PROV,NVL(B.DIS,0) DIS,NVL((B.PROV-B.DIS),0) NET, C.WKG FROM
(SELECT DISTINCT service_sub_type  from CDR_CRM_ORDERS WHERE service_type IN ('FMT','MMVC Postpaid')) A,
(select service_sub_type,COUNT(CASE WHEN ORDER_TYPE='New' THEN 1 END) PROV,COUNT(CASE WHEN ORDER_TYPE='Disconnect' THEN 1 END) DIS FROM CDR_CRM_ORDERS WHERE  TRUNC(ORDER_COMP_DATE)  BETWEEN '" . $fdate . "' AND '" . $tdate . "' AND ORDER_STATUS='Complete' AND service_type IN ('FMT','MMVC Postpaid') GROUP BY  service_sub_type) B,
(SELECT service_sub_type,COUNT(DISTINCT PHONE_NO) WKG FROM CDRHYD_WORKING_LINES WHERE service_type IN ('FMT','MMVC Postpaid') and SERVICE_STATUS='Active' group by service_sub_type) C
WHERE A.service_sub_type=B.service_sub_type(+) AND A.service_sub_type=C.service_sub_type(+)";
		//ECHO $sql;
		$odbcexec = odbc_exec($conn,$sql);
		echo "<h2>NGN Services Gross and Net from " . $fdate ." to " .$tdate. "</h2><br>";
		echo "<table id='datagrid' border='1' width='50%'>";
		echo '<thead>';
		echo "<tr>";
		echo "<th>NGN Service</th>";
		echo "<th>Provisions</th>";
		echo "<th>Disconnetions</th>";
		echo "<th>Net<br>Achievement</th>";
		echo "<th>Wkg<br>as on Date</th>";
		echo "</tr>";
		echo '</thead>';
		while($data = odbc_fetch_array($odbcexec))
		{
			echo "<tr>";
			echo "<td>" .$data['SERVICE_SUB_TYPE']. "</a></td>";
			echo "<td><a href='gross_net_det.php?sde=SSA&fdate= " .$fdate. "&tdate=" .$tdate. "&service=" .$data['SERVICE_SUB_TYPE']. "&order_type=PROV' target='_blank'>" .$data['PROV']. "</a></td>";
			echo "<td><a href='gross_net_det.php?sde=SSA&fdate= " .$fdate. "&tdate=" .$tdate. "&service=" .$data['SERVICE_SUB_TYPE']. "&order_type=DIS' target='_blank'>" .$data['DIS']. "</a></td>";
			echo "<td>" .$data['NET']. "</td>";
			echo "<td><a href='wkg_conns.php?stn=SSA&service=" .$data['SERVICE_SUB_TYPE']. "' target='_blank'>" .$data['WKG']. "</td>";
			echo "</tr>";
		}

		echo "</table>";
		break;
}


?>
  



