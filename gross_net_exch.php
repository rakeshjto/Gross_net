<?php
include('head.html');
$conn = odbc_connect("reports","reports","reports123");
 $sde=$_REQUEST['sde']; 
 /*switch ($stn) 
			{
				case "NVL":
					$stations="'NVL','PLV'";
				 break;
				default:
					$stations="'" .$stn . "'";
			}
			*/
 $fdate=$_REQUEST['fdate'];
 $tdate=$_REQUEST['tdate'];
 $service=$_REQUEST['service'];
 if($service=="LL") // If the Service is Landline
 {
	 echo "<h3 style='text-align: center;color: blue'>LandLine Gross and Net from" . $fdate ." to " . $tdate . " under " . $sde . " </h3><br>";
        echo "<table id='datagrid' border='1' width='50%'>";
        echo '<thead>';
		echo "<tr>";
        echo "<th>Exchange</th>";
		echo "<th>Provisions</th>";
		echo "<th>Disconnetions</th>";
		echo "<th>Net<br>Achievement</th>";
		echo "</tr>";
        echo '</thead>';
		
	$sql= "select EXCH,LLPROV,LLDIS,(LLPROV-LLDIS) LLNET from 
	(select A.exchange_code EXCH,
	count(case when ORDER_TYPE='New' AND ORDER_SUB_TYPE='Provision' then 1 end) LLPROV,
	count(case when ORDER_TYPE='Disconnect' AND ORDER_SUB_TYPE IN ('Disconnect','Disconnect Due to NP','Disconnect Due to Misuse') then 1 end) LLDIS
	FROM (SELECT EXCHANGE_CODE FROM EXCHANGE_CODE WHERE SDE= '" . $sde ."') a LEFT  outer join CDR_CRM_ORDERS B
	ON A.EXCHANGE_CODE=B.EXCHANGE_CODE AND TRUNC(B.ORDER_COMP_DATE)  BETWEEN '" . $fdate . "' AND '" . $tdate . "' AND SERVICE_TYPE in ('Landline', 'ISDN', 'DSPT', 'LL PCO')
	AND ORDER_STATUS='Complete'  group by A.exchange_code)";
	//echo $sql;
$odbcexec = odbc_exec($conn,$sql); 	
	 while($data = odbc_fetch_array($odbcexec))
        {
            echo "<tr>";
            echo "<td>" .$data['EXCH']. "</td>";
            echo "<td><a href='gross_net_det.php?exch=" . $data['EXCH'] . "&fdate= " .$fdate. "&tdate=" .$tdate. "&service=LL&order_type=LLPROV' target='_blank'>" .$data['LLPROV']. "</a></td>";
            echo "<td><a href='gross_net_det.php?exch=" . $data['EXCH'] . "&fdate= " .$fdate. "&tdate=" .$tdate. "&service=LL&order_type=LLDIS' target='_blank'>" .$data['LLDIS']. "</a></td>";
			echo "<td>" .$data['LLNET']. "</td>";
          
            echo "</tr>";
            $gpro= $gpro+$data['LLPROV'];
			$dis= $dis+$data['LLDIS'];
			$llnet= $llnet+ $data['LLNET'];
        }
		echo '<tfoot>';
        echo "<th>Total</th>";
        echo "<th>". $gpro ."</th>";
        echo "<th>". $dis ."</th>";
        echo "<th>". $llnet ."</th>";
        echo '</tfoot>';
		echo "</table>";
 }
else if($service=="BB") // If the Service is BROADBAND
{
	echo "<h3 style='text-align: center;color: blue'>Broadband Gross and Net from" . $fdate ." to " . $tdate . " under " . $sde . "</h3><br>";
        echo "<table id='datagrid' border='1' width='50%'>";
        echo '<thead>';
		echo "<tr>";
        echo "<th>Exchange</th>";
		echo "<th>BB Provisions</th>";
		echo "<th>BB Disconnetions</th>";
		echo "<th>Net BB<br>Achievement</th>";
		echo "</tr>";
		echo '</thead>';
		
	$sql= "select EXCH,BBPROV,BBDIS,(BBPROV-BBDIS) BBNET from 
	(select A.exchange_code EXCH,
	count(case when ORDER_TYPE='Modify' AND ORDER_sub_type='Broadband Provision'  then 1 end) BBPROV,
	count(case when ((ORDER_TYPE='Disconnect' AND ORDER_SUB_TYPE IN ('Disconnect','Disconnect Due to Misuse','Disconnect Due to NP') AND BB_CONN_TYPE='POSTPAID' AND BB_USER_ID IS NOT NULL) OR (ORDER_TYPE='Modify' AND ORDER_SUB_TYPE='Broadband Disconnection')) then 1 end) BBDIS
	FROM (SELECT EXCHANGE_CODE FROM EXCHANGE_CODE WHERE SDE= '" . $sde ."') a LEFT  outer join CDR_CRM_ORDERS B
	ON A.EXCHANGE_CODE=B.EXCHANGE_CODE AND TRUNC(ORDER_COMP_DATE)  BETWEEN '" . $fdate . "' AND '" . $tdate . "'  AND SERVICE_TYPE in ('Landline', 'ISDN', 'DSPT', 'LL PCO')
	AND ORDER_STATUS='Complete'  group by A.exchange_code)";
	//echo $sql;
$odbcexec = odbc_exec($conn,$sql); 	
	 while($data = odbc_fetch_array($odbcexec))
        {
            echo "<tr>";
            echo "<td>" .$data['EXCH']. "</td>";
            echo "<td><a href='gross_net_det.php?exch=" . $data['EXCH'] . "&fdate=" .$fdate. "&tdate=" .$tdate. "&service=BB&order_type=BBPROV' target='_blank'>" .$data['BBPROV']. "</a></td>";
            echo "<td><a href='gross_net_det.php?exch=" . $data['EXCH'] . "&fdate=" .$fdate. "&tdate=" .$tdate. "&service=BB&order_type=BBDIS' target='_blank'>" .$data['BBDIS']. "</a></td>";
			echo "<td>" .$data['BBNET']. "</td>";
          
            echo "</tr>";
            $gpro= $gpro+$data['BBPROV'];
            $dis= $dis+$data['BBDIS'];
			$bbnet= $bbnet+ $data['BBNET'];
        }
		echo '<tfoot>';
        echo "<th>Total</th>";
        echo "<th>". $gpro ."</th>";
        echo "<th>". $dis ."</th>";
        echo "<th>". $bbnet ."</th>";
        echo '</tfoot>';
		echo "</table>";
}

else if($service=="FTTH") // If the Service is BROADBAND
{
	echo "<h3 style='text-align: center;color: blue'>FTTH Gross and Net from" . $fdate ." to " . $tdate . " under " . $sde . "</h3><br>";
        
		
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
		
		$sql_duplicate= "SELECT PHONE_NO FROM CDR_CRM_ORDERS WHERE ORDER_TYPE='New' AND  TRUNC(ORDER_COMP_DATE) BETWEEN TO_DATE('" . $fdate . "')-30 AND TO_DATE('" . $fdate . "')-1  AND SERVICE_TYPE LIKE '%FTTH%' AND ORDER_STATUS='Complete'";
		$odbcexecduplicate = odbc_exec($conn,$sql_duplicate);
	while ($avoid_duplicate = odbc_fetch_array($odbcexecduplicate)){
			$avoid_duplicate_data .= "'" .$avoid_duplicate[PHONE_NO]. "',";	
		}
		
		
	$sql= "SELECT A.EXCHANGE_CODE EXCH,
COUNT(DISTINCT (CASE WHEN ORDER_TYPE='New' AND ORDER_SUB_TYPE='Provision' AND CUST_ACCNT_NO NOT IN (" . $data2 . "'123') AND PHONE_NO NOT IN (" . $avoid_duplicate_data . "'123') THEN PHONE_NO END)) LLONLYPROV,
COUNT(DISTINCT (CASE WHEN ORDER_TYPE='New' AND ORDER_SUB_TYPE='Broadband Provision' AND CUST_ACCNT_NO NOT IN (" . $data2 . "'123') AND PHONE_NO NOT IN (" . $avoid_duplicate_data . "'123') THEN PHONE_NO END)) BBONLYPROV,
COUNT(DISTINCT (CASE WHEN ORDER_TYPE='New' AND CUST_ACCNT_NO  IN (" . $data2 . "'123') AND PHONE_NO NOT IN (" . $avoid_duplicate_data . "'123') THEN PHONE_NO END)) PROV,
COUNT(DISTINCT (CASE WHEN ORDER_TYPE='Disconnect' AND SERVICE_SUB_TYPE='FTTH Voice' AND CUST_ACCNT_NO NOT IN (" . $data4 . "'123') THEN PHONE_NO END)) LLONLYDIS,
COUNT(DISTINCT (CASE WHEN ORDER_TYPE='Disconnect' AND SERVICE_SUB_TYPE='FTTH BroadBand' AND CUST_ACCNT_NO NOT IN (" . $data4 . "'123') THEN PHONE_NO END)) BBONLYDIS,
COUNT(DISTINCT (CASE WHEN ORDER_TYPE='Disconnect' AND CUST_ACCNT_NO  IN (" . $data4 . "'123') THEN PHONE_NO END)) DIS
FROM EXCHANGE_CODE A,CDR_CRM_ORDERS B 
WHERE A.EXCHANGE_CODE=B.EXCHANGE_CODE(+) 
AND TRUNC(B.ORDER_COMP_DATE) BETWEEN '" . $fdate . "' AND '" . $tdate . "'
AND B.SERVICE_TYPE LIKE '%FTTH%' 
AND b.ORDER_STATUS='Complete'
AND substr(A.SDE,-3)='" . $sde ."'
GROUP BY A.EXCHANGE_CODE";
	//echo $sql;
	echo "<table id='datagrid' class='ftthtable' border='1' width='50%'>";
		echo '<thead>';
		echo "<tr>";
		echo "<th rowspan='2'>Sl No</th>";
		echo "<th rowspan='2'>Exchange</th>";
		echo "<th colspan='3'>Provisions</th>";
		echo "<th colspan='3'>Disconnetions</th>";
		echo "<th rowspan='2'>Net<br>Achievement</th>";
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
		$sno=1;
		$odbcexec = odbc_exec($conn,$sql); 
		while($data = odbc_fetch_array($odbcexec))
		{
			 $net_result= ($data['LLONLYPROV']+$data['BBONLYPROV']+$data['PROV'])-($data['LLONLYDIS']+$data['BBONLYDIS']+$data['DIS']);
			echo "<tr>";
			echo "<td>" .$sno. "</td>";
			echo "<td>" .$data['EXCH']. "</td>";
			echo "<td><a href='gross_net_det.php?fdate=" .$fdate. "&tdate=" .$tdate. "&service=FTTH&order_type=PROV&order_Sub_type=LL&exch=" .$data['EXCH']. "' target='_blank'>" .$data['LLONLYPROV']. "</a></td>";
			echo "<td><a href='gross_net_det.php?fdate=" .$fdate. "&tdate=" .$tdate. "&service=FTTH&order_type=PROV&order_Sub_type=BB&exch=" .$data['EXCH']. "' target='_blank'>" .$data['BBONLYPROV']. "</a></td>";
			echo "<td><a href='gross_net_det.php?fdate=" .$fdate. "&tdate=" .$tdate. "&service=FTTH&order_type=PROV&order_Sub_type=LLBB&exch=" .$data['EXCH']. "' target='_blank'>" .$data['PROV']. "</a></td>";
			echo "<td><a href='gross_net_det.php?fdate=" .$fdate. "&tdate=" .$tdate. "&service=FTTH&order_type=DIS&order_Sub_type=LL&exch=" .$data['EXCH']. "' target='_blank'>" .$data['LLONLYDIS']. "</a></td>";
			echo "<td><a href='gross_net_det.php?fdate=" .$fdate. "&tdate=" .$tdate. "&service=FTTH&order_type=DIS&order_Sub_type=BB&exch=" .$data['EXCH']. "' target='_blank'>" .$data['BBONLYDIS']. "</a></td>";
			echo "<td><a href='gross_net_det.php?fdate=" .$fdate. "&tdate=" .$tdate. "&service=FTTH&order_type=DIS&order_Sub_type=LLBB&exch=" .$data['EXCH']. "' target='_blank'>" .$data['DIS']. "</a></td>";
			echo "<td>" .$net_result. "</td>";
			

			echo "</tr>";
			$llonlyprov= $llonlyprov + $data['LLONLYPROV']; 
			$bbonlyprov= $bbonlyprov + $data['BBONLYPROV']; 
			$prov= $prov+ $data['PROV'];
			$llonlydis= $llonlydis + $data['LLONLYDIS']; 
			$bbonlydis= $bbonlydis + $data['BBONLYDIS']; 
			$dis= $dis + $data['DIS'];
			$net= $net+ $net_result;
			$wkg= $wkg+ $data['WKG'];
			$sno= $sno+1;
		}
		echo '<tfoot>';
		echo '<tr>';
		echo "<th colspan='2'> SSA</th>";
		echo "<th>". $llonlyprov ."</a></th>";
		echo "<th>". $bbonlyprov ."</a></th>";
		echo "<th>". $prov ."</a></th>";
		echo "<th>". $llonlydis ."</a></th>";
		echo "<th>". $bbonlydis ."</a></th>";
		echo "<th>". $dis ."</a></th>";
		echo "<th>". $net ."</a></th>";
		echo '</tr>';
		echo '</tfoot>';
		echo "</table>";
}
			 
?>