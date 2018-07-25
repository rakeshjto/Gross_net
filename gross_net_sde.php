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
switch ($_POST['svc'])
{
	case "LL": // IF THE SERVICE IS LL
		$sql= "SELECT A.STATION_CODE,A.SDE,LL_TAR,A.LLPROV,A.RC,A.LLDIS,NVL(B.LL49,0) LL49 FROM
				(SELECT A.STATION_CODE,A.SDE,C.LL_TAR,
count(case when ORDER_TYPE='New' AND ORDER_SUB_TYPE='Provision' then 1 end) LLPROV,
count(case when ORDER_TYPE='New' AND ORDER_SUB_TYPE='Provision' AND RECONNECTION_ORDER='Y' then 1 end) RC,
count(case when ORDER_TYPE='Disconnect' AND ORDER_SUB_TYPE IN ('Disconnect','Disconnect Due to NP','Disconnect Due to Misuse') then 1 end) LLDIS
from 
EXCHANGE_CODE A, CDR_CRM_ORDERS B, (SELECT SDE,SUM(LL_TAR) LL_TAR FROM MON_TARGETS_SDE_WISE WHERE MON_YY IN ('" .$first_day_of_fdate. "','" .$first_day_of_tdate. "') GROUP BY SDE) C 
WHERE A.EXCHANGE_CODE=B.EXCHANGE_CODE AND A.SDE=C.SDE(+) AND
TRUNC(ORDER_COMP_DATE) BETWEEN '" . $fdate . "' AND '" . $tdate . "'  AND
SERVICE_TYPE in ('Landline', 'ISDN', 'DSPT', 'LL PCO') AND ORDER_STATUS='Complete' 
group by A.STATION_CODE,A.SDE,C.LL_TAR) A,
		
(SELECT A.STATION_CODE,A.SDE,count(*) LL49
from EXCHANGE_CODE A,CDR_CRM_ORDERS B,LL49_ORDERS C
WHERE B.ORDER_NO=C.ORDER_NO AND A.EXCHANGE_CODE=B.EXCHANGE_CODE AND
TRUNC(B.ORDER_COMP_DATE) BETWEEN '" . $fdate . "' AND '" . $tdate . "'
AND B.SERVICE_TYPE in ('Landline', 'ISDN', 'DSPT', 'LL PCO')
AND B.ORDER_STATUS='Complete' GROUP BY A.STATION_CODE,A.SDE) B
WHERE A.SDE=B.SDE(+)";
		

		//echo $sql;
		$odbcexec = odbc_exec($conn,$sql);
		echo "<h2>LandLine Gross and Net from " . $fdate ." to " .$tdate. "</h2><br>";
		echo "<table class='datagrid' border='1' width='60%'>";
		echo '<thead>';
		echo "<tr>";
		echo "<th rowspan='2'>SDCA</th>";
		echo "<th rowspan='2'>SDE</th>";
		echo "<th rowspan='2'>Target</th>";
		//echo "<th>SDE NAME</th>";
		//echo "<th colspan='2'>Annual<br>Target</th>";
		//echo "<th>SDE HR No</th>";
		echo "<th colspan='2'>LL Provisions</th>";
		echo "<th rowspan='2'>Disconnetions</th>";
		echo "<th rowspan='2'>Net<br>Achievement</th>";
		echo "</tr>";
		echo "</tr>";
		echo "<th>Total LL<sub>(Reconnections)</sub></th>";
		echo "<th>LL49</th>";
		echo "</tr>";
		echo '</thead>';
		 
		while($data = odbc_fetch_array($odbcexec))
		{
			echo "<tr>";
			echo "<td><a href='gross_net_exch.php?stn=" . $data['STATION_CODE'] . "&fdate= " .$fdate. "&tdate=" .$tdate. "&service=LL' target='_blank'>" .$data['STATION_CODE']. "</a></td>";
			echo "<td>" .$data['SDE']. "</a></td>";
			echo "<td>" .$data['LL_TAR']. "</td>";
			//echo "<td>" .$data['LEV2_SDE_DE_ID']. "</td>";
			echo "<td><a href='gross_net_det.php?sde=". $data['SDE'] . "&fdate= " .$fdate. "&tdate=" .$tdate. "&service=LL&order_type=LLPROV' target='_blank'>" .$data['LLPROV']. "</a><sub>(" .$data['RC']. ")</sub></td>";
			echo "<td><a href='gross_net_det.php?sde=". $data['SDE'] . "&fdate= " .$fdate. "&tdate=" .$tdate. "&service=LL&order_type=LL49' target='_blank'>" .$data['LL49']. "</a></td>";
			echo "<td><a href='gross_net_det.php?sde=". $data['SDE'] . "&fdate= " .$fdate. "&tdate=" .$tdate. "&service=LL&order_type=LLDIS' target='_blank'>" .$data['LLDIS']. "</a></td>";
			$llnet= $data['LLPROV']-$data['LLDIS'];
			echo "<td>" .$llnet. "</td>";
			echo "</tr>";
				
			$gpro= $gpro+$data['LLPROV'];
			$LL49pro= $LL49pro+$data['LL49'];
			$rc= $rc+$data['RC'];
			$dis= $dis+$data['LLDIS'];
			$llnet_tot= $llnet_tot+ $llnet;
		}

		echo '<tfoot>';
		echo '<tr>';
		echo "<td COLSPAN='3'>Total</td>";
		//echo "<th>". $fy_tar_gross ."</th>";
		//echo "<th>". $fy_tar_net ."</th>";
		
		echo "<th><a href='gross_net_det.php?sde=SSA&fdate= " .$fdate. "&tdate=" .$tdate. "&service=LL&order_type=LLPROV' target='_blank'>". $gpro ."<sub>(" .$rc. ")</sub></a></th>";
		echo "<th><a href='gross_net_det.php?sde=SSA&fdate= " .$fdate. "&tdate=" .$tdate. "&service=LL&order_type=LL49' target='_blank'>". $LL49pro ."</a></th>";
		echo "<th><a href='gross_net_det.php?sde=SSA&fdate= " .$fdate. "&tdate=" .$tdate. "&service=LL&order_type=LLDIS' target='_blank'>". $dis ."</a></th>";
		echo "<th>". $llnet_tot ."</th>";
		echo '</tr>';
		echo '</tfoot>';
		echo "</table><br>";
		echo "<a href='gross_net_ssa_detailed.php?fdate=" .$fdate. "&tdate=" .$tdate. "&svc=" .$_POST['svc']. "' target='_blank'><button type='button' class='btn btn-success btn-lg pull-right'>Click Here for Detailed Information</button></a>";
		
		break;
						
	case "BB": // IF THE SERVICE IS BB

		$sql= "SELECT A.STATION_CODE,A.SDE,A.BB_TAR,NVL(A.BBPROV,0) BBPROV,NVL(A.BBDIS,0) BBDIS,NVL(B.BB249,0) BB249,0,NVL(C.BB1199,0)  BB1199,0 FROM
(SELECT A.STATION_CODE,A.SDE,C.BB_TAR,
count(case when ORDER_TYPE='Modify' AND ORDER_sub_type='Broadband Provision'  then 1 end) BBPROV,
count(case when ((ORDER_TYPE='Disconnect' AND ORDER_SUB_TYPE IN ('Disconnect','Disconnect Due to Misuse','Disconnect Due to NP') AND BB_CONN_TYPE='POSTPAID' AND BB_USER_ID IS NOT NULL) OR (ORDER_TYPE='Modify' AND ORDER_SUB_TYPE='Broadband Disconnection')) then 1 end) BBDIS
from
EXCHANGE_CODE A, CDR_CRM_ORDERS B, (SELECT SDE,SUM(BB_TAR) BB_TAR FROM MON_TARGETS_SDE_WISE WHERE MON_YY IN ('" .$first_day_of_fdate. "','" .$first_day_of_tdate. "') GROUP BY SDE) C 
WHERE A.EXCHANGE_CODE=B.EXCHANGE_CODE AND A.SDE=C.SDE(+) AND
TRUNC(ORDER_COMP_DATE) BETWEEN '" . $fdate . "' AND '" . $tdate . "' AND
SERVICE_TYPE in ('Landline', 'ISDN', 'DSPT', 'LL PCO') AND ORDER_STATUS='Complete'
group by A.STATION_CODE,A.SDE,C.BB_TAR) A,

(SELECT A.STATION_CODE,A.SDE,count(*) BB249
from EXCHANGE_CODE A,CDR_CRM_ORDERS B,BB249_ORDERS C
WHERE B.ORDER_NO=C.ORDER_NO AND A.EXCHANGE_CODE=B.EXCHANGE_CODE AND
TRUNC(B.ORDER_COMP_DATE) BETWEEN '" . $fdate . "' AND '" . $tdate . "'
AND B.SERVICE_TYPE in ('Landline', 'ISDN', 'DSPT', 'LL PCO')
AND B.ORDER_STATUS='Complete' GROUP BY A.STATION_CODE,A.SDE) B,
		
(SELECT A.STATION_CODE,A.SDE,count(*) BB1199
from EXCHANGE_CODE A,CDR_CRM_ORDERS B,BB1199_ORDERS C
WHERE B.ORDER_NO=C.ORDER_NO AND A.EXCHANGE_CODE=B.EXCHANGE_CODE AND
TRUNC(B.ORDER_COMP_DATE) BETWEEN '" . $fdate . "' AND '" . $tdate . "'
AND B.SERVICE_TYPE in ('Landline', 'ISDN', 'DSPT', 'LL PCO')
AND B.ORDER_STATUS='Complete' GROUP BY A.STATION_CODE,A.SDE) C
WHERE A.SDE=B.SDE(+) AND A.SDE=C.SDE(+)";

		//echo $sql;
		$odbcexec = odbc_exec($conn,$sql);

		echo "<h2>Broadband Gross and Net from " . $fdate ." to " .$tdate. "</h2><br>";
		echo "<table class='datagrid' border='1' width='60%'>";
		echo '<thead>';
		echo "<tr>";
		echo "<th rowspan='2'>SDCA</th>";
		echo "<th rowspan='2'>SDE</th>";
		echo "<th rowspan='2'>Monthly  Target</th>";
		echo "<th colspan='3'>BB Provisions</th>";
		echo "<th rowspan='2'>BB Disconnetions</th>";
		echo "<th rowspan='2'>Net<br>Achievement</th>";
		echo "</tr>";
		echo "<tr>";
		echo "<th>Total BB</th>";
		echo "<th>BB249</th>";
		echo "<th>BB1199</th>";
		echo "</tr>";
		echo '</thead>';
	
		while($data = odbc_fetch_array($odbcexec))
		{
			echo "<tr>";
			echo "<td><a href='gross_net_exch.php?stn=" . $data['STATION_CODE'] . "&fdate=" .$fdate. "&tdate=" .$tdate. "&service=BB' target='_blank'>" .$data['STATION_CODE']. "</a></td>";
			echo "<td>" .$data['SDE']. "</a></td>";
			echo "<td>" .$data['BB_TAR']. "</td>";
			echo "<td><a href='gross_net_det.php?sde=". $data['SDE'] . "&fdate=" .$fdate. "&tdate=" .$tdate. "&service=BB&order_type=BBPROV' target='_blank'>" .$data['BBPROV']. "</a></td>";
			echo "<td><a href='gross_net_det.php?sde=". $data['SDE'] . "&fdate=" .$fdate. "&tdate=" .$tdate. "&service=BB&order_type=BB249' target='_blank'>" .$data['BB249']. "</a></td>";
			echo "<td><a href='gross_net_det.php?sde=". $data['SDE'] . "&fdate=" .$fdate. "&tdate=" .$tdate. "&service=BB&order_type=BB1199' target='_blank'>" .$data['BB1199']. "</a></td>";
			echo "<td><a href='gross_net_det.php?sde=". $data['SDE'] . "&fdate=" .$fdate. "&tdate=" .$tdate. "&service=BB&order_type=BBDIS' target='_blank'>" .$data['BBDIS']. "</a></td>";
			$bbnet= $data['BBPROV']-$data['BBDIS'];
			echo "<td>" .$bbnet. "</td>";
			echo "</tr>";
				
			$fy_tar_gross=$fy_tar_gross+$data['FY_TARGET_GROSS'];
			$fy_tar_net=$fy_tar_net+$data['FY_TARGET_NET'];
			$mon_tar_gross=$mon_tar_gross+$data['MON_TARGET_GROSS'];
			$mon_tar_net=$mon_tar_net+$data['MON_TARGET_NET'];
				
			$gpro= $gpro+$data['BBPROV'];
			$BB249pro= $BB249pro+$data['BB249'];
			$BB1199pro= $BB1199pro+$data['BB1199'];
			$bbdis= $bbdis+$data['BBDIS'];
			$bbnet_total= $bbnet_total+ $bbnet;
		}
		// @todo Click Here for Detailed Info link to be given
		// @todo DataTables search box, Info alignment
		echo '<tfoot>';
		echo '<tr>';
		echo "<td colspan='3'> Total</td>";
		echo "<td><a href='gross_net_det.php?sde=SSA&fdate= " .$fdate. "&tdate=" .$tdate. "&service=BB&order_type=BBPROV' target='_blank'>". $gpro ."</a></td>";
		echo "<td><a href='gross_net_det.php?sde=SSA&fdate= " .$fdate. "&tdate=" .$tdate. "&service=BB&order_type=BB249' target='_blank'>". $BB249pro ."</a></td>";
		echo "<td><a href='gross_net_det.php?sde=SSA&fdate= " .$fdate. "&tdate=" .$tdate. "&service=BB&order_type=BB1199' target='_blank'>". $BB1199pro ."</a></td>";
		echo "<td><a href='gross_net_det.php?sde=SSA&fdate= " .$fdate. "&tdate=" .$tdate. "&service=BB&order_type=BBDIS' target='_blank'>". $bbdis ."</a></td>";
		echo "<td>". $bbnet_total ."</td>";
		echo '</tr>';
		echo '</tfoot>';

		echo "</table><br>";
		echo "<a href='gross_net_ssa_detailed.php?fdate=" .$fdate. "&tdate=" .$tdate. "&svc=" .$_POST['svc']. "' target='_blank'><button type='button' class='btn btn-success btn-lg pull-right'>Click Here for Detailed Information</button></a>";
		break;
			

	case "FTTH": // IF THE SERVICE IS FTTH Broadband ORDER_SUB_TYPE in('VPN Provision','Broadband Provision')
				
		$sql_prov= "SELECT CUST_ACCNT_NO FROM CDR_CRM_ORDERS WHERE ORDER_TYPE='New' AND TRUNC(ORDER_COMP_DATE) BETWEEN '" . $fdate . "' AND '" . $tdate . "' AND SERVICE_TYPE LIKE '%FTTH%' AND ORDER_STATUS='Complete' GROUP BY CUST_ACCNT_NO HAVING COUNT(*)=2";
		$odbcexec1 = odbc_exec($conn,$sql_prov);
	while ($data1 = odbc_fetch_array($odbcexec1)){
			$data2 .= "'" .$data1[CUST_ACCNT_NO]. "',";	
		}
		
		$sql_dis= "SELECT CUST_ACCNT_NO FROM CDR_CRM_ORDERS WHERE ORDER_TYPE='Disconnect' AND TRUNC(ORDER_COMP_DATE) BETWEEN '" . $fdate . "' AND '" . $tdate . "' AND SERVICE_TYPE LIKE '%FTTH%' AND ORDER_STATUS='Complete' GROUP BY CUST_ACCNT_NO HAVING COUNT(*)=2";
		$odbcexec3 = odbc_exec($conn,$sql_dis);
	while ($data3 = odbc_fetch_array($odbcexec3)){
			$data4 .= "'" .$data3[CUST_ACCNT_NO]. "',";	
		}
		//print_r($data2);
		$sql= " SELECT A.STATION_CODE,NVL(G.LLONLYPROV,0) LLONLYPROV, NVL(H.BBONLYPROV,0) BBONLYPROV,NVL(B.PROV,0) PROV,NVL(I.LLONLYDIS,0) LLONLYDIS,NVL(J.BBONLYDIS,0) BBONLYDIS,NVL(C.DIS,0) DIS,((NVL(G.LLONLYPROV,0)+NVL(H.BBONLYPROV,0)+NVL(B.PROV,0))-(NVL(I.LLONLYDIS,0)+NVL(J.BBONLYDIS,0)+nvl(C.DIS,0))) NET, NVL(D.WKG,0) WKG,NVL(E.FTTH_TAR,0) FTTH_TAR FROM
	(select STATION_CODE,STD_CODE from cdr_stn_std_code WHERE STATION_CODE IN ('ELR','BMV','NVL','TKU','TGM','PKL')) a,
	(select DISTINCT STATION_CODE,SDE,STD_CODE from EXCHANGE_CODE WHERE SDE IN ('SDEU1ELR','SDEU2BMV','SDEU2TKU','SDERNVL','SDEUTGM','SDEUPKL')) F,
	(SELECT SUBSTR(PHONE_NO,1,5) STD_CODE,COUNT(DISTINCT PHONE_NO) LLONLYPROV FROM CDR_CRM_ORDERS WHERE ORDER_TYPE='New' AND ORDER_sub_type='Provision' and TRUNC(ORDER_COMP_DATE) BETWEEN '" . $fdate . "' AND '" . $tdate . "' AND SERVICE_TYPE LIKE '%FTTH%' AND ORDER_STATUS='Complete' AND PHONE_NO NOT IN (SELECT PHONE_NO FROM CDR_CRM_ORDERS WHERE ORDER_TYPE='New' AND  TRUNC(ORDER_COMP_DATE) BETWEEN TO_DATE('" . $fdate . "')-30 AND TO_DATE('" . $fdate . "')-1  AND SERVICE_TYPE LIKE '%FTTH%' AND ORDER_STATUS='Complete') AND CUST_ACCNT_NO NOT IN (" . $data2 . "'123') GROUP BY SUBSTR(PHONE_NO,1,5) ) G,
	(SELECT SUBSTR(PHONE_NO,1,5) STD_CODE,COUNT(DISTINCT PHONE_NO) BBONLYPROV FROM CDR_CRM_ORDERS WHERE ORDER_TYPE='New' AND ORDER_sub_type='Broadband Provision' and TRUNC(ORDER_COMP_DATE) BETWEEN '" . $fdate . "' AND '" . $tdate . "' AND SERVICE_TYPE LIKE '%FTTH%' AND ORDER_STATUS='Complete' AND PHONE_NO NOT IN (SELECT PHONE_NO FROM CDR_CRM_ORDERS WHERE ORDER_TYPE='New' AND  TRUNC(ORDER_COMP_DATE) BETWEEN TO_DATE('" . $fdate . "')-30 AND TO_DATE('" . $fdate . "')-1  AND SERVICE_TYPE LIKE '%FTTH%' AND ORDER_STATUS='Complete') AND CUST_ACCNT_NO NOT IN (" . $data2 . "'123') GROUP BY SUBSTR(PHONE_NO,1,5) ) H,
	(SELECT SUBSTR(PHONE_NO,1,5) STD_CODE,COUNT(DISTINCT PHONE_NO) PROV FROM CDR_CRM_ORDERS WHERE ORDER_TYPE='New' and TRUNC(ORDER_COMP_DATE) BETWEEN '" . $fdate . "' AND '" . $tdate . "' AND SERVICE_TYPE LIKE '%FTTH%' AND ORDER_STATUS='Complete' AND PHONE_NO NOT IN (SELECT PHONE_NO FROM CDR_CRM_ORDERS WHERE ORDER_TYPE='New' AND  TRUNC(ORDER_COMP_DATE) BETWEEN TO_DATE('" . $fdate . "')-30 AND TO_DATE('" . $fdate . "')-1  AND SERVICE_TYPE LIKE '%FTTH%' AND ORDER_STATUS='Complete') AND CUST_ACCNT_NO IN (" . $data2 . "'123') GROUP BY SUBSTR(PHONE_NO,1,5) ) B,
	(SELECT SUBSTR(PHONE_NO,1,5) STD_CODE,COUNT(DISTINCT PHONE_NO) LLONLYDIS FROM CDR_CRM_ORDERS WHERE ORDER_TYPE='Disconnect' AND SERVICE_SUB_TYPE='FTTH Voice' and TRUNC(ORDER_COMP_DATE) BETWEEN '" . $fdate . "' AND '" . $tdate . "' AND SERVICE_TYPE LIKE '%FTTH%' AND ORDER_STATUS='Complete' AND CUST_ACCNT_NO NOT IN (" . $data4 . "'123') GROUP BY SUBSTR(PHONE_NO,1,5) ) I,
	(SELECT SUBSTR(PHONE_NO,1,5) STD_CODE,COUNT(DISTINCT PHONE_NO) BBONLYDIS FROM CDR_CRM_ORDERS WHERE ORDER_TYPE='Disconnect' AND SERVICE_SUB_TYPE='FTTH BroadBand' and TRUNC(ORDER_COMP_DATE) BETWEEN '" . $fdate . "' AND '" . $tdate . "' AND SERVICE_TYPE LIKE '%FTTH%' AND ORDER_STATUS='Complete' AND CUST_ACCNT_NO NOT IN (" . $data4 . "'123') GROUP BY SUBSTR(PHONE_NO,1,5) ) J,
	(SELECT SUBSTR(PHONE_NO,1,5) STD_CODE,COUNT(DISTINCT PHONE_NO) DIS FROM CDR_CRM_ORDERS WHERE ORDER_TYPE='Disconnect' and TRUNC(ORDER_COMP_DATE) BETWEEN '" . $fdate . "' AND '" . $tdate . "' AND SERVICE_TYPE LIKE '%FTTH%' AND ORDER_STATUS='Complete' AND CUST_ACCNT_NO IN (" . $data4 . "'123') GROUP BY SUBSTR(PHONE_NO,1,5) ) C,
	(SELECT SUBSTR(PHONE_NO,1,5) STD_CODE,COUNT(DISTINCT PHONE_NO) WKG FROM CDRHYD_WORKING_LINES WHERE SERVICE_TYPE LIKE '%FTTH%' and SERVICE_STATUS='Active' GROUP BY SUBSTR(PHONE_NO,1,5)) D,
	(SELECT SDE,SUM(FTTH_TAR) FTTH_TAR FROM MON_TARGETS_SDE_WISE WHERE MON_YY IN ('" .$first_day_of_fdate. "','" .$first_day_of_tdate. "') GROUP BY SDE) E
	WHERE A.STD_CODE=B.STD_CODE(+) AND A.STD_CODE=C.STD_CODE(+) AND A.STD_CODE=D.STD_CODE(+) AND A.STD_CODE=F.STD_CODE(+) AND F.SDE=E.SDE(+) AND A.STD_CODE=G.STD_CODE(+) AND A.STD_CODE=H.STD_CODE(+) AND A.STD_CODE=I.STD_CODE(+) AND A.STD_CODE=J.STD_CODE(+)";
	//to avoid doubleing of connections in the given period this where clause is introduced-- PHONE_NO NOT IN (SELECT PHONE_NO FROM CDR_CRM_ORDERS WHERE ORDER_TYPE='New' AND  TRUNC(ORDER_COMP_DATE) BETWEEN TO_DATE('" . $fdate . "')-30 AND TO_DATE('" . $fdate . "')-1  AND SERVICE_TYPE LIKE '%FTTH%' AND ORDER_STATUS='Complete')
	
		//echo $sql;
		//08819-297049 was inactive on 01/10/2014 10:31:28 SELECT PHONE_NO FROM CDR_CRM_ORDERS WHERE ORDER_TYPE='New' AND TRUNC(ORDER_COMP_DATE)<'01-NOV-2017' AND SERVICE_TYPE LIKE '%FTTH%' AND ORDER_STATUS='Complete'
		$odbcexec = odbc_exec($conn,$sql);

		$diff= date_diff(date_create($fdate),date_create($tdate)); // Findout number of days between $fdate and $tdate
		//echo $diff->days+1; // Findout number of days between $fdate and $tdate with required format Eg: 25
		$prorata_target= ROUND(($annual_net_target * $diff->days+1 )/365);
		echo "<h2>FTTH Gross and Net from " . $fdate ." to " .$tdate. "</h2><br>";
		echo "<table id='datagrid' class='ftthtable' border='1' width='50%'>";
		echo '<thead>';
		echo "<tr>";
		echo "<th rowspan='2'>SDCA</th>";;
		//echo "<th>Annual Gross Target</th>";
		echo "<th rowspan='2'>Gross Target</th>";
		echo "<th colspan='3'>Provisions</th>";
		echo "<th colspan='3'>Disconnetions</th>";
		echo "<th rowspan='2'>Net<br>Achievement</th>";
		echo "<th rowspan='2'>Wkg<br>as on Date</th>";
		echo "</tr>";
		echo "<tr><th>LL Only</th><th>BB Only</th><th>LL+BB</th>";
		echo "<th>LL Only</th><th>BB Only</th><th>LL+BB</th></tr>";
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
			 
			echo "<tr>";
			echo "<td>" .$data['STATION_CODE']. "</td>";
			//echo "<td>" .$annual_net_target. "</td>";
			echo "<td>" .$data['FTTH_TAR']. "</td>";
			echo "<td><a href='gross_net_det.php?sde=SSA&fdate=" .$fdate. "&tdate=" .$tdate. "&service=FTTH&order_type=PROV&order_Sub_type=LL&sdca=" .$data['STATION_CODE']. "' target='_blank'>" .$data['LLONLYPROV']. "</a></td>";
			echo "<td><a href='gross_net_det.php?sde=SSA&fdate=" .$fdate. "&tdate=" .$tdate. "&service=FTTH&order_type=PROV&order_Sub_type=BB&sdca=" .$data['STATION_CODE']. "' target='_blank'>" .$data['BBONLYPROV']. "</a></td>";
			echo "<td><a href='gross_net_det.php?sde=SSA&fdate=" .$fdate. "&tdate=" .$tdate. "&service=FTTH&order_type=PROV&order_Sub_type=LLBB&sdca=" .$data['STATION_CODE']. "' target='_blank'>" .$data['PROV']. "</a></td>";
			echo "<td><a href='gross_net_det.php?sde=SSA&fdate=" .$fdate. "&tdate=" .$tdate. "&service=FTTH&order_type=DIS&order_Sub_type=LL&sdca=" .$data['STATION_CODE']. "' target='_blank'>" .$data['LLONLYDIS']. "</a></td>";
			echo "<td><a href='gross_net_det.php?sde=SSA&fdate=" .$fdate. "&tdate=" .$tdate. "&service=FTTH&order_type=DIS&order_Sub_type=BB&sdca=" .$data['STATION_CODE']. "' target='_blank'>" .$data['BBONLYDIS']. "</a></td>";
			echo "<td><a href='gross_net_det.php?sde=SSA&fdate=" .$fdate. "&tdate=" .$tdate. "&service=FTTH&order_type=DIS&order_Sub_type=LLBB&sdca=" .$data['STATION_CODE']. "' target='_blank'>" .$data['DIS']. "</a></td>";
			echo "<td>" .$data['NET']. "</td>";
			echo "<td><a href='http://10.33.49.6/reports/ftth.php' target='_blank'>" .$data['WKG']. "</a></td>";


			echo "</tr>";
			$llonlyprov= $llonlyprov + $data['LLONLYPROV']; 
			$bbonlyprov= $bbonlyprov + $data['BBONLYPROV']; 
			$prov= $prov+ $data['PROV'];
			$llonlydis= $llonlydis + $data['LLONLYDIS']; 
			$bbonlydis= $bbonlydis + $data['BBONLYDIS']; 
			$dis= $dis + $data['DIS'];
			$net= $net+ $data['NET'];
			$wkg= $wkg+ $data['WKG'];
			 
		}
		echo '<tfoot>';
		echo '<tr>';
		echo "<th> SSA</th>";
		echo "<th>". $ftth_tar ."</a></th>";
		echo "<th>". $llonlyprov ."</a></th>";
		echo "<th>". $bbonlyprov ."</a></th>";
		echo "<th>". $prov ."</a></th>";
		echo "<th>". $llonlydis ."</a></th>";
		echo "<th>". $bbonlydis ."</a></th>";
		echo "<th>". $dis ."</a></th>";
		echo "<th>". $net ."</a></th>";
		echo "<th>". $wkg ."</a></th>";
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
  



