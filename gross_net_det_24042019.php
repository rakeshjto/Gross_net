<?php
include('head.html');
set_time_limit(600);
$conn = odbc_connect("reports","reports","reports123");
$fdate=$_REQUEST['fdate'];
$tdate=$_REQUEST['tdate'];
$service=$_REQUEST['service'];
$order_type=$_REQUEST['order_type'];

if (isset($_REQUEST['sde']))  // If this page is arrived from gross_net_ssa.php
{

	if($service=="LL") // If the Service is Landline
	{
		switch ($_REQUEST['order_type'])
		{
			case "LLPROV":
				$heading= "Landline New Provsions between ". $fdate . " and " .$tdate. " under " .$_REQUEST['sde']. " ";
				$querystring = "ORDER_TYPE='New' AND ORDER_SUB_TYPE='Provision'";
				$plan_component= "Add";
				$product_cat = "('Plan','Combo Plan')";
				break;
			case "SHIFTPROV":
				$heading= "Landline Shift Provsions between ". $fdate . " and " .$tdate. " under " .$_REQUEST['sde']. " ";
				$querystring = "ORDER_TYPE='Shift' AND ORDER_SUB_TYPE IN('Shift Across exchanges','Shift within STD','Shift Within exch with NumChg','Shift Within exch w/o NumChg')";
				$plan_component= "Add";
				$product_cat = "('Plan','Combo Plan')";
				break;
			case "LLDISVO":
				$heading= "Landline Voluntary Disconnections between ". $fdate . " and " .$tdate. " under " .$_REQUEST['sde']. " ";
				$querystring = "ORDER_TYPE='Disconnect' AND ORDER_SUB_TYPE='Disconnect'";
				$plan_component= "Delete";
				$product_cat = "('Plan','Combo Plan')";
				break;
			case "LLDISNP":
				$heading= "Landline NP Disconnections between ". $fdate . " and " .$tdate. " under " .$_REQUEST['sde']. " ";
				$querystring = "ORDER_TYPE='Disconnect' AND ORDER_SUB_TYPE='Disconnect Due to NP'";
				$plan_component= "Delete";
				$product_cat = "('Plan','Combo Plan')";
				break;
			case "LLDISMU":
				$heading= "Landline Misusage Disconnections between ". $fdate . " and " .$tdate. " under " .$_REQUEST['sde']. " ";
				$querystring = "ORDER_TYPE='Disconnect' AND ORDER_SUB_TYPE='Disconnect Due to Misuse'";
				$plan_component= "Delete";
				$product_cat = "('Plan','Combo Plan')";
				break;
			case "SHIFTDIS":
				$heading= "Landline Shift Disconnections between ". $fdate . " and " .$tdate. " under " .$_REQUEST['sde']. " ";
				$querystring = "ORDER_TYPE='Shift' AND ORDER_SUB_TYPE IN ('Disconnect - Number Changed','Disconnect')";
				$plan_component= "Delete";
				$product_cat = "('Plan','Combo Plan')";
				break;
			case "LLDIS":
				$heading= "Landline Disconnections between ". $fdate . " and " .$tdate. " under " .$_REQUEST['sde']. " ";
				$querystring = "ORDER_TYPE='Disconnect' AND ORDER_SUB_TYPE IN ('Disconnect','Disconnect Due to NP','Disconnect Due to Misuse')";
				$plan_component= "Delete";
				$product_cat = "('Plan','Combo Plan')";
				break;
			case "LL49":
					$heading= "LL - EXPERIENCE LL 49 Provisions between ". $fdate . " and " .$tdate. " under " .$_REQUEST['sde']. " ";
					$querystring = "ORDER_TYPE='New' AND ORDER_SUB_TYPE='Provision'";
					$plan_component= "Add";
					$product_cat = "('Plan','Combo Plan')";
					break;
		}
		if($_REQUEST['order_type']!='LL49'){
			if($_REQUEST['sde']!='SSA'){
				$sql= "SELECT a.EXCHANGE_CODE,MOBILE_NO,PHONE_NO, A.ORDER_NO,TO_CHAR(ORDER_CREATED_DATE,'YYYY-MM-DD') ORDER_CREATED_DATE, TO_CHAR(A.ORDER_COMP_DATE,'YYYY-MM-DD') ORDER_COMP_DATE,ORDER_TYPE, ORDER_SUB_TYPE, ORDER_STATUS,
					SERVICE_SUB_TYPE,CUSTOMER_NAME,BILL_ACCNT_TYPE, BILL_ACCNT_SUB_TYPE,HOUSE_NO,VILLAGE_NAME,ADDITIONAL_DETAILS , MOBILE_NO,RECONNECTION_ORDER,C.PRODUCT_NAME FROM CDR_CRM_ORDERS A, EXCHANGE_CODE B,ORDER_ITEMS C
					WHERE B.SDE='" .$_REQUEST['sde']. "' AND A.EXCHANGE_CODE=B.EXCHANGE_CODE  AND C.PROD_CATG_CD in " .$product_cat. " AND C.PRODUCT_STATUS='" .$plan_component. "' AND A.ORDER_NO=C.ORDER_NO AND TRUNC(ORDER_COMP_DATE)  BETWEEN '" . $fdate . "' AND '" . $tdate . "' AND SERVICE_TYPE in ('Landline', 'ISDN', 'DSPT', 'LL PCO')
					AND ORDER_STATUS='Complete' AND ". $querystring ;
			}
			else {
				$sql= "SELECT a.EXCHANGE_CODE,MOBILE_NO,PHONE_NO, A.ORDER_NO,TO_CHAR(ORDER_CREATED_DATE,'YYYY-MM-DD') ORDER_CREATED_DATE, TO_CHAR(A.ORDER_COMP_DATE,'YYYY-MM-DD') ORDER_COMP_DATE,ORDER_TYPE, ORDER_SUB_TYPE, ORDER_STATUS,
					SERVICE_SUB_TYPE,CUSTOMER_NAME,BILL_ACCNT_TYPE, BILL_ACCNT_SUB_TYPE,HOUSE_NO,VILLAGE_NAME,ADDITIONAL_DETAILS , MOBILE_NO,RECONNECTION_ORDER,C.PRODUCT_NAME FROM CDR_CRM_ORDERS A, EXCHANGE_CODE B,ORDER_ITEMS C
					WHERE A.EXCHANGE_CODE=B.EXCHANGE_CODE AND A.ORDER_NO=C.ORDER_NO AND C.PROD_CATG_CD in " .$product_cat. " AND C.PRODUCT_STATUS='" .$plan_component. "' AND TRUNC(ORDER_COMP_DATE)  BETWEEN '" . $fdate . "' AND '" . $tdate . "' AND SERVICE_TYPE in ('Landline', 'ISDN', 'DSPT', 'LL PCO')
					AND ORDER_STATUS='Complete' AND ". $querystring ;
			}
		}
		else if($_REQUEST['order_type'] =='LL49'){
			if($_REQUEST['sde']!='SSA'){
				$sql= "SELECT a.EXCHANGE_CODE,MOBILE_NO,A.PHONE_NO, A.ORDER_NO, TO_CHAR(ORDER_CREATED_DATE,'YYYY-MM-DD') ORDER_CREATED_DATE, TO_CHAR(A.ORDER_COMP_DATE,'YYYY-MM-DD') ORDER_COMP_DATE,ORDER_TYPE, ORDER_SUB_TYPE, ORDER_STATUS,
					SERVICE_SUB_TYPE,CUSTOMER_NAME,BILL_ACCNT_TYPE, BILL_ACCNT_SUB_TYPE,HOUSE_NO,VILLAGE_NAME,ADDITIONAL_DETAILS , MOBILE_NO,RECONNECTION_ORDER,D.PRODUCT_NAME FROM CDR_CRM_ORDERS A, EXCHANGE_CODE B, LL49_ORDERS C,ORDER_ITEMS D
					WHERE B.SDE='" .$_REQUEST['sde']. "' AND A.EXCHANGE_CODE=B.EXCHANGE_CODE AND A.ORDER_NO=C.ORDER_NO AND A.ORDER_NO=D.ORDER_NO AND D.PROD_CATG_CD in " .$product_cat. " AND D.PRODUCT_STATUS='" .$plan_component. "' AND TRUNC(A.ORDER_COMP_DATE)  BETWEEN '" . $fdate . "' AND '" . $tdate . "' AND SERVICE_TYPE in ('Landline', 'ISDN', 'DSPT', 'LL PCO')
					AND ORDER_STATUS='Complete' AND ". $querystring ;
			}
			else {
				$sql= "SELECT a.EXCHANGE_CODE,MOBILE_NO,A.PHONE_NO, A.ORDER_NO, TO_CHAR(ORDER_CREATED_DATE,'YYYY-MM-DD') ORDER_CREATED_DATE, TO_CHAR(A.ORDER_COMP_DATE,'YYYY-MM-DD') ORDER_COMP_DATE,ORDER_TYPE, ORDER_SUB_TYPE, ORDER_STATUS,
					SERVICE_SUB_TYPE,CUSTOMER_NAME,BILL_ACCNT_TYPE, BILL_ACCNT_SUB_TYPE,HOUSE_NO,VILLAGE_NAME,ADDITIONAL_DETAILS , MOBILE_NO,RECONNECTION_ORDER,D.PRODUCT_NAME FROM CDR_CRM_ORDERS A, EXCHANGE_CODE B, LL49_ORDERS C,ORDER_ITEMS D
					WHERE A.EXCHANGE_CODE=B.EXCHANGE_CODE AND A.ORDER_NO=C.ORDER_NO AND A.ORDER_NO=D.ORDER_NO AND D.PROD_CATG_CD in " .$product_cat. " AND D.PRODUCT_STATUS='" .$plan_component. "' AND TRUNC(A.ORDER_COMP_DATE)  BETWEEN '" . $fdate . "' AND '" . $tdate . "' AND SERVICE_TYPE in ('Landline', 'ISDN', 'DSPT', 'LL PCO')
					AND ORDER_STATUS='Complete' AND ". $querystring ;
			}
		}
		
			
	}
	
	// If the Service is BROADBAND
	else if($service=="BB") // If the Service is BROADBAND
	{
		switch ($_REQUEST['order_type'])
		{
			case "BBPROV":
				$heading= "Broadband New Provsions between ". $fdate . " and " .$tdate. " under " .$_REQUEST['sde']. " ";
				$querystring = "ORDER_TYPE='Modify' AND ORDER_sub_type='Broadband Provision' ";
				$plan_component= "Add";
				$product_cat = "('BB Plan','Combo Plan','VPN Plan')";
				break;
			case "SHIFTPROV":
				$heading= "Broadband Shift Provsions between ". $fdate . " and " .$tdate. " under " .$_REQUEST['sde']. " ";
				$querystring = "ORDER_TYPE='Shift' AND ORDER_SUB_TYPE IN('Shift Across exchanges','Shift within STD','Shift Within exch with NumChg','Shift Within exch w/o NumChg') AND BB_CONN_TYPE='POSTPAID' AND BB_USER_ID IS NOT NULL ";
				$plan_component= "Add";
				$product_cat = "('BB Plan','Combo Plan','VPN Plan')";
				break;
			case "BBDISWITHLL":
				$heading= "Broadband Disconnections with LL between ". $fdate . " and " .$tdate. " under " .$_REQUEST['sde']. " ";
				$querystring = " ORDER_TYPE='Disconnect' AND ORDER_SUB_TYPE IN ('Disconnect','Disconnect Due to Misuse') AND BB_CONN_TYPE='POSTPAID' AND BB_USER_ID IS NOT NULL";
				$plan_component= "Delete";
				$product_cat = "('BB Plan','Combo Plan','VPN Plan')";
				break;
			case "BBDISNPWITHLL":
				$heading= "Broadband NP Disconnections with LL between ". $fdate . " and " .$tdate. " under " .$_REQUEST['sde']. " ";
				$querystring = "ORDER_TYPE='Disconnect' AND ORDER_SUB_TYPE='Disconnect Due to NP' AND BB_CONN_TYPE='POSTPAID' AND BB_USER_ID IS NOT NULL";
				$plan_component= "Delete";
				$product_cat = "('BB Plan','Combo Plan','VPN Plan')";
				break;
			case "BBONLYDIS":
				$heading= "Broadband Only Disconnections between ". $fdate . " and " .$tdate. " under " .$_REQUEST['sde']. " ";
				$querystring = "ORDER_TYPE='Modify' AND ORDER_SUB_TYPE='Broadband Disconnection'";
				$plan_component= "Delete";
				$product_cat = "('BB Plan','Combo Plan','VPN Plan')";
				break;
			case "SHIFTDIS":
				$heading= "Broadband Shift Disconnections between ". $fdate . " and " .$tdate. " under " .$_REQUEST['sde']. " ";
				$querystring = "ORDER_TYPE='Shift' AND ORDER_SUB_TYPE IN ('Disconnect - Number Changed','Disconnect') AND BB_CONN_TYPE='POSTPAID' AND BB_USER_ID IS NOT NULL ";
				$plan_component= "Delete";
				$product_cat = "('BB Plan','Combo Plan','VPN Plan')";
				break;
			case "BBDIS":
				$heading= "Broadband Disconnections between ". $fdate . " and " .$tdate. " under " .$_REQUEST['sde']. " ";
				$querystring = "((ORDER_TYPE='Disconnect' AND ORDER_SUB_TYPE IN ('Disconnect','Disconnect Due to Misuse','Disconnect Due to NP') AND BB_CONN_TYPE='POSTPAID' AND BB_USER_ID IS NOT NULL) OR (ORDER_TYPE='Modify' AND ORDER_SUB_TYPE='Broadband Disconnection'))";
				$plan_component= "Delete";
				$product_cat = "('BB Plan','Combo Plan','VPN Plan')";
				break;
			case "BB249":
					$heading= "BB EXPERIENCE CULD249 Provisions between ". $fdate . " and " .$tdate. " under " .$_REQUEST['sde']. " ";
					$querystring = "ORDER_TYPE='Modify' AND ORDER_sub_type='Broadband Provision' ";
					$plan_component= "Add";
					$product_cat = "('BB Plan','Combo Plan','VPN Plan')";
					break;
			case "BB1199":
					$heading= "BB EXPERIENCE CULD 11999 Provisions between ". $fdate . " and " .$tdate. " under " .$_REQUEST['sde']. " ";
					$querystring = "ORDER_TYPE='Modify' AND ORDER_sub_type='Broadband Provision' ";
					$plan_component= "Add";
					$product_cat = "('BB Plan','Combo Plan','VPN Plan')";
					break;
		}
		//Query for Other than BB249& BB1199 Plans
		if($_REQUEST['order_type']!='BB249' && $_REQUEST['order_type']!='BB1199'){
			if($_REQUEST['sde']!='SSA'){
				$sql= "SELECT a.EXCHANGE_CODE,MOBILE_NO,PHONE_NO, PAR_ORDER_ID,A.ORDER_NO,TO_CHAR(ORDER_CREATED_DATE,'YYYY-MM-DD') ORDER_CREATED_DATE, TO_CHAR(ORDER_COMP_DATE,'YYYY-MM-DD') ORDER_COMP_DATE,ORDER_TYPE, ORDER_SUB_TYPE, ORDER_STATUS,
					SERVICE_SUB_TYPE,CUSTOMER_NAME,BILL_ACCNT_TYPE, BILL_ACCNT_SUB_TYPE,HOUSE_NO,VILLAGE_NAME,ADDITIONAL_DETAILS , MOBILE_NO,RECONNECTION_ORDER,C.PRODUCT_NAME FROM CDR_CRM_ORDERS A, EXCHANGE_CODE B,ORDER_ITEMS C
					WHERE  B.SDE='" .$_REQUEST['sde']. "' AND A.EXCHANGE_CODE=B.EXCHANGE_CODE AND A.ORDER_NO=C.ORDER_NO AND C.PROD_CATG_CD in " .$product_cat. " AND C.PRODUCT_STATUS='" .$plan_component. "' AND TRUNC(ORDER_COMP_DATE)  BETWEEN '" . $fdate . "' AND '" . $tdate . "'  AND SERVICE_TYPE in ('Landline', 'ISDN', 'DSPT', 'LL PCO')
					AND ORDER_STATUS='Complete'  AND ". $querystring ;
			}
			else{
				$sql= "SELECT a.EXCHANGE_CODE,MOBILE_NO,PHONE_NO,PAR_ORDER_ID, A.ORDER_NO,TO_CHAR(ORDER_CREATED_DATE,'YYYY-MM-DD') ORDER_CREATED_DATE, TO_CHAR(ORDER_COMP_DATE,'YYYY-MM-DD') ORDER_COMP_DATE,ORDER_TYPE, ORDER_SUB_TYPE, ORDER_STATUS,
					SERVICE_SUB_TYPE,CUSTOMER_NAME,BILL_ACCNT_TYPE, BILL_ACCNT_SUB_TYPE,HOUSE_NO,VILLAGE_NAME,ADDITIONAL_DETAILS , MOBILE_NO,RECONNECTION_ORDER,C.PRODUCT_NAME FROM CDR_CRM_ORDERS A, EXCHANGE_CODE B,ORDER_ITEMS C
					WHERE  A.EXCHANGE_CODE=B.EXCHANGE_CODE AND A.ORDER_NO=C.ORDER_NO AND  TRUNC(ORDER_COMP_DATE)  BETWEEN '" . $fdate . "' AND '" . $tdate . "'  AND C.PROD_CATG_CD in " .$product_cat. " AND C.PRODUCT_STATUS='" .$plan_component. "' AND SERVICE_TYPE in ('Landline', 'ISDN', 'DSPT', 'LL PCO')
					AND ORDER_STATUS='Complete'  AND ". $querystring ;
			}
		}
		//Query for BB249 Plan
		else if($_REQUEST['order_type']=='BB249'){
			if($_REQUEST['sde']!='SSA'){
				$sql= "SELECT a.EXCHANGE_CODE,MOBILE_NO,A.PHONE_NO, PAR_ORDER_ID,A.ORDER_NO, TO_CHAR(ORDER_CREATED_DATE,'YYYY-MM-DD') ORDER_CREATED_DATE, TO_CHAR(A.ORDER_COMP_DATE,'YYYY-MM-DD') ORDER_COMP_DATE,ORDER_TYPE, ORDER_SUB_TYPE, ORDER_STATUS,
					SERVICE_SUB_TYPE,CUSTOMER_NAME,BILL_ACCNT_TYPE, BILL_ACCNT_SUB_TYPE,HOUSE_NO,VILLAGE_NAME,ADDITIONAL_DETAILS , MOBILE_NO,RECONNECTION_ORDER,D.PRODUCT_NAME FROM CDR_CRM_ORDERS A, EXCHANGE_CODE B,BB249_ORDERS C,ORDER_ITEMS D
					WHERE  B.SDE='" .$_REQUEST['sde']. "' AND A.EXCHANGE_CODE=B.EXCHANGE_CODE AND A.ORDER_NO=C.ORDER_NO AND A.ORDER_NO=D.ORDER_NO AND TRUNC(A.ORDER_COMP_DATE)  BETWEEN '" . $fdate . "' AND '" . $tdate . "'  AND D.PROD_CATG_CD in " .$product_cat. " AND D.PRODUCT_STATUS='" .$plan_component. "' AND SERVICE_TYPE in ('Landline', 'ISDN', 'DSPT', 'LL PCO')
					AND ORDER_STATUS='Complete'  AND ". $querystring ;
			}
			else{
				$sql= "SELECT a.EXCHANGE_CODE,MOBILE_NO,A.PHONE_NO, PAR_ORDER_ID,A.ORDER_NO, TO_CHAR(ORDER_CREATED_DATE,'YYYY-MM-DD') ORDER_CREATED_DATE, TO_CHAR(A.ORDER_COMP_DATE,'YYYY-MM-DD') ORDER_COMP_DATE,ORDER_TYPE, ORDER_SUB_TYPE, ORDER_STATUS,
					SERVICE_SUB_TYPE,CUSTOMER_NAME,BILL_ACCNT_TYPE, BILL_ACCNT_SUB_TYPE,HOUSE_NO,VILLAGE_NAME,ADDITIONAL_DETAILS , MOBILE_NO,RECONNECTION_ORDER,D.PRODUCT_NAME FROM CDR_CRM_ORDERS A, EXCHANGE_CODE B,BB249_ORDERS C,ORDER_ITEMS D
					WHERE  A.EXCHANGE_CODE=B.EXCHANGE_CODE AND A.ORDER_NO=C.ORDER_NO AND A.ORDER_NO=D.ORDER_NO AND TRUNC(A.ORDER_COMP_DATE)  BETWEEN '" . $fdate . "' AND '" . $tdate . "'  AND D.PROD_CATG_CD in " .$product_cat. " AND D.PRODUCT_STATUS='" .$plan_component. "' AND SERVICE_TYPE in ('Landline', 'ISDN', 'DSPT', 'LL PCO')
					AND ORDER_STATUS='Complete'  AND ". $querystring ;
			}
		}
		//Query for BB1199 Plan
		else if($_REQUEST['order_type']=='BB1199'){
			if($_REQUEST['sde']!='SSA'){
				$sql= "SELECT a.EXCHANGE_CODE,MOBILE_NO,A.PHONE_NO, PAR_ORDER_ID,A.ORDER_NO, TO_CHAR(ORDER_CREATED_DATE,'YYYY-MM-DD') ORDER_CREATED_DATE, TO_CHAR(A.ORDER_COMP_DATE,'YYYY-MM-DD') ORDER_COMP_DATE,ORDER_TYPE, ORDER_SUB_TYPE, ORDER_STATUS,
					SERVICE_SUB_TYPE,CUSTOMER_NAME,BILL_ACCNT_TYPE, BILL_ACCNT_SUB_TYPE,HOUSE_NO,VILLAGE_NAME,ADDITIONAL_DETAILS , MOBILE_NO,RECONNECTION_ORDER,D.PRODUCT_NAME FROM CDR_CRM_ORDERS A, EXCHANGE_CODE B,BB1199_ORDERS C,ORDER_ITEMS D
					WHERE  B.SDE='" .$_REQUEST['sde']. "' AND A.EXCHANGE_CODE=B.EXCHANGE_CODE AND A.ORDER_NO=C.ORDER_NO AND A.ORDER_NO=D.ORDER_NO AND TRUNC(A.ORDER_COMP_DATE)  BETWEEN '" . $fdate . "' AND '" . $tdate . "'  AND D.PROD_CATG_CD in " .$product_cat. " AND D.PRODUCT_STATUS='" .$plan_component. "' AND SERVICE_TYPE in ('Landline', 'ISDN', 'DSPT', 'LL PCO')
					AND ORDER_STATUS='Complete'  AND ". $querystring ;
			}
			else{
				$sql= "SELECT a.EXCHANGE_CODE,MOBILE_NO,A.PHONE_NO, PAR_ORDER_ID,A.ORDER_NO, TO_CHAR(ORDER_CREATED_DATE,'YYYY-MM-DD') ORDER_CREATED_DATE, TO_CHAR(A.ORDER_COMP_DATE,'YYYY-MM-DD') ORDER_COMP_DATE,ORDER_TYPE, ORDER_SUB_TYPE, ORDER_STATUS,
					SERVICE_SUB_TYPE,CUSTOMER_NAME,BILL_ACCNT_TYPE, BILL_ACCNT_SUB_TYPE,HOUSE_NO,VILLAGE_NAME,ADDITIONAL_DETAILS , MOBILE_NO,RECONNECTION_ORDER,D.PRODUCT_NAME FROM CDR_CRM_ORDERS A, EXCHANGE_CODE B,BB1199_ORDERS C,ORDER_ITEMS D
					WHERE  A.EXCHANGE_CODE=B.EXCHANGE_CODE AND A.ORDER_NO=C.ORDER_NO AND A.ORDER_NO=D.ORDER_NO AND TRUNC(A.ORDER_COMP_DATE)  BETWEEN '" . $fdate . "' AND '" . $tdate . "'  AND D.PROD_CATG_CD in " .$product_cat. " AND D.PRODUCT_STATUS='" .$plan_component. "' AND SERVICE_TYPE in ('Landline', 'ISDN', 'DSPT', 'LL PCO')
					AND ORDER_STATUS='Complete'  AND ". $querystring ;
			}
		}
		
		
	}

	// If the Service is FTTH
	else if($service=="FTTH") 
	{
		$sql_prov= "SELECT CUST_ACCNT_NO FROM CDR_CRM_ORDERS WHERE ORDER_TYPE='New' AND TRUNC(ORDER_COMP_DATE) BETWEEN '" . $fdate . "' AND '" . $tdate . "' AND SERVICE_TYPE LIKE '%Bharat Fiber%' AND ORDER_STATUS='Complete' GROUP BY CUST_ACCNT_NO HAVING COUNT(*)=2";
		$odbcexec1 = odbc_exec($conn,$sql_prov);
	while ($data1 = odbc_fetch_array($odbcexec1)){
			$data2 .= "'" .$data1[CUST_ACCNT_NO]. "',";	
		}
		
		$sql_dis= "SELECT CUST_ACCNT_NO FROM CDR_CRM_ORDERS WHERE ORDER_TYPE='Disconnect' AND TRUNC(ORDER_COMP_DATE) BETWEEN '" . $fdate . "' AND '" . $tdate . "' AND SERVICE_TYPE LIKE '%Bharat Fiber%' AND ORDER_STATUS='Complete' GROUP BY CUST_ACCNT_NO HAVING COUNT(*)=2";
		$odbcexec3 = odbc_exec($conn,$sql_dis);
	while ($data3 = odbc_fetch_array($odbcexec3)){
			$data4 .= "'" .$data3[CUST_ACCNT_NO]. "',";	
		}
		if($_REQUEST['sdca']=='SSA'){
			$olt_string="";
		}
		else{
			$olt_string="AND A.EXCHANGE_CODE in(SELECT EXCHANGE_CODE FROM EXCHANGE_CODE WHERE SUBSTR(SDE,-3)='".$_REQUEST['sdca']."')";
		}
		switch ($_REQUEST['order_Sub_type'])
		{
			case "LL":
				
				if($_REQUEST['order_type']=="PROV"){
					$heading= "Only LL FTTH Provsions between ". $fdate . " and " .$tdate . "under ".$_REQUEST['sdca']. " Station";
					$order_type = " ORDER_TYPE='New' AND ";
					$querystring1 = " ORDER_SUB_TYPE IN ('Broadband Provision','Broadband VPN Provision') AND ";
					$customerids=$data2;
					 $plan_component= "Add";
					//$product_cat = "('FTTH Combo Plan','FTTH BB Plans','Plan','VPN Plan')";
					$product_cat = "('Bharat Fiber Combo Plan','Bharat Fiber BB Plans','Plan','VPN Plan')";
					}
				elseif($_REQUEST['order_type']=="DIS"){
					$heading= "Only LL FTTH Disconnetions between ". $fdate . " and " .$tdate . "under ".$_REQUEST['sdca']. " Station";
					$order_type = "ORDER_TYPE='Disconnect' AND ";
					//$querystring1 = "  SERVICE_SUB_TYPE='FTTH Voice' AND ";
					$querystring1 = "  SERVICE_SUB_TYPE in('FTTH Voice','Bharat Fiber Voice') AND ";
					$customerids=$data4;
					$plan_component= "Delete";
					$product_cat = "('FTTH Combo Plan','FTTH BB Plans','Plan','VPN Plan')";
					}
				$querystring2 = " NOT IN ";
				break;
			case "BB":
				if($_REQUEST['order_type']=="PROV"){
					$heading= "Only BB FTTH Provsions between ". $fdate . " and " .$tdate . "under ".$_REQUEST['sdca']. " Station";
					$order_type = " ORDER_TYPE='New' AND ";
					$querystring1 = " ORDER_SUB_TYPE IN ('Broadband Provision','Broadband VPN Provision') AND ";
					$customerids=$data2;
					$plan_component= "Add";
					//$product_cat = "('FTTH Combo Plan','FTTH BB Plans','Plan','VPN Plan')";
					$product_cat = "('Bharat Fiber Combo Plan','Bharat Fiber BB Plans','Plan','VPN Plan')";
					}
				elseif($_REQUEST['order_type']=="DIS"){
					$heading= "Only BB FTTH Disconnetions between ". $fdate . " and " .$tdate . "under ".$_REQUEST['sdca']. " Station";
					$order_type = "ORDER_TYPE='Disconnect' AND ";
					//$querystring1 = " SERVICE_SUB_TYPE ='FTTH BroadBand' AND ";
					$querystring1 = " SERVICE_SUB_TYPE in('FTTH BroadBand','Bharat Fiber BB') AND ";
					$customerids=$data4;
					$plan_component= "Delete";
					//$product_cat = "('FTTH Combo Plan','FTTH BB Plans','Plan','VPN Plan')";
					$product_cat = "('Bharat Fiber Combo Plan','Bharat Fiber BB Plans','Plan','VPN Plan')";
					}
				$querystring2 = " NOT IN ";
				break;
			case "LLBB":
				if($_REQUEST['order_type']=="PROV"){
				$heading= "FTTH Provsions (LL & BB) between ". $fdate . " and " .$tdate . "under ".$_REQUEST['sdca']. " Station";
				$order_type = " ORDER_TYPE='New' AND ";
				$querystring1 = "";$customerids=$data2;
				$plan_component= "Add";
				//$product_cat = "('FTTH Combo Plan','FTTH BB Plans','Plan','VPN Plan')";
				$product_cat = "('Bharat Fiber Combo Plan','Bharat Fiber BB Plans','Plan','VPN Plan')";
				}
				elseif($_REQUEST['order_type']=="DIS"){
				$heading= "FTTH Disconnetions (LL & BB) between ". $fdate . " and " .$tdate . "under ".$_REQUEST['sdca']. " Station";
				$order_type = " ORDER_TYPE='Disconnect' AND ";
				$querystring1 = "";
				$customerids=$data4;
				$plan_component= "Delete";
				//$product_cat = "('FTTH Combo Plan','FTTH BB Plans','Plan','VPN Plan')";
				$product_cat = "('Bharat Fiber Combo Plan','Bharat Fiber BB Plans','Plan','VPN Plan')";
				}
				$querystring2 = " IN ";
				break;		
		}
		
		$sql="SELECT EXCHANGE_CODE,MOBILE_NO,PHONE_NO, A.ORDER_NO,TO_CHAR(ORDER_CREATED_DATE,'YYYY-MM-DD') ORDER_CREATED_DATE, TO_CHAR(ORDER_COMP_DATE,'YYYY-MM-DD') ORDER_COMP_DATE,ORDER_TYPE, ORDER_SUB_TYPE, ORDER_STATUS,
					SERVICE_SUB_TYPE,CUSTOMER_NAME,BILL_ACCNT_TYPE, BILL_ACCNT_SUB_TYPE,HOUSE_NO,VILLAGE_NAME,ADDITIONAL_DETAILS , MOBILE_NO,RECONNECTION_ORDER,B.PRODUCT_NAME  FROM CDR_CRM_ORDERS A, ORDER_ITEMS B WHERE A.ORDER_NO=B.ORDER_NO AND ". $order_type . " ". $querystring1 . "  TRUNC(ORDER_COMP_DATE) BETWEEN '" . $fdate . "' AND '" . $tdate . "' AND SERVICE_TYPE LIKE '%Bharat Fiber%' AND ORDER_STATUS='Complete' AND CUST_ACCNT_NO ". $querystring2 . " (" . $customerids . "'123') " .$olt_string . "  AND B.PROD_CATG_CD in " .$product_cat. " AND B.PRODUCT_STATUS='" .$plan_component. "' ORDER BY PHONE_NO";
		
		//$sql="SELECT EXCHANGE_CODE,MOBILE_NO,PHONE_NO, A.ORDER_NO,TO_CHAR(ORDER_CREATED_DATE,'YYYY-MM-DD') ORDER_CREATED_DATE, TO_CHAR(ORDER_COMP_DATE,'YYYY-MM-DD') ORDER_COMP_DATE,ORDER_TYPE, ORDER_SUB_TYPE, ORDER_STATUS,
					//SERVICE_SUB_TYPE,CUSTOMER_NAME,BILL_ACCNT_TYPE, BILL_ACCNT_SUB_TYPE,HOUSE_NO,VILLAGE_NAME,ADDITIONAL_DETAILS , MOBILE_NO,RECONNECTION_ORDER,B.PRODUCT_NAME  FROM CDR_CRM_ORDERS A, ORDER_ITEMS B WHERE A.ORDER_NO=B.ORDER_NO AND ". $order_type . " ". $querystring1 . "  TRUNC(ORDER_COMP_DATE) BETWEEN '" . $fdate . "' AND '" . $tdate . "' AND SERVICE_TYPE LIKE '%FTTH%' AND ORDER_STATUS='Complete' AND CUST_ACCNT_NO ". $querystring2 . " (" . $customerids . "'123') AND A.EXCHANGE_CODE in(SELECT EXCHANGE_CODE FROM EXCHANGE_CODE WHERE SUBSTR(SDE,-3)='".$_REQUEST['sdca']."')   AND B.PROD_CATG_CD in " .$product_cat. " AND B.PRODUCT_STATUS='" .$plan_component. "'";
		
		/*$sql_old= "SELECT EXCHANGE_CODE,MOBILE_NO,PHONE_NO, ORDER_NO,TO_CHAR(ORDER_CREATED_DATE,'YYYY-MM-DD') ORDER_CREATED_DATE, TO_CHAR(ORDER_COMP_DATE,'YYYY-MM-DD') ORDER_COMP_DATE,ORDER_TYPE, ORDER_SUB_TYPE, ORDER_STATUS,
					SERVICE_SUB_TYPE,CUSTOMER_NAME,BILL_ACCNT_TYPE, BILL_ACCNT_SUB_TYPE,HOUSE_NO,VILLAGE_NAME,ADDITIONAL_DETAILS , MOBILE_NO,RECONNECTION_ORDER FROM CDR_CRM_ORDERS
					WHERE  TRUNC(ORDER_COMP_DATE)  BETWEEN '" . $fdate . "' AND '" . $tdate . "'  AND SERVICE_TYPE LIKE '%FTTH%' AND ORDER_STATUS='Complete' AND PHONE_NO NOT IN (SELECT PHONE_NO FROM CDR_CRM_ORDERS WHERE ORDER_TYPE='New' AND  TRUNC(ORDER_COMP_DATE) BETWEEN TO_DATE('" . $fdate . "')-30 AND TO_DATE('" . $fdate . "')-1 AND SERVICE_TYPE LIKE '%FTTH%' AND ORDER_STATUS='Complete')
					AND ". $querystring . " AND PHONE_NO LIKE '" .$std_code. "%' ORDER BY PHONE_NO";*/

	}

	else if($service=="IN") // If the Service is IN
	{
		switch ($_REQUEST['order_type'])
		{
			case "PROV":
				$heading= "IN New Provsions between ". $fdate . " and " .$tdate ;
				$querystring = " ORDER_TYPE='New' ";
				break;
			case "DIS":
				$heading= "IN Disconnetions between ". $fdate . " and " .$tdate;
				$querystring = "ORDER_TYPE='Disconnect'";
				break;
		}
		$sql= "SELECT MOBILE_NO,PHONE_NO, A.ORDER_NO,TO_CHAR(ORDER_CREATED_DATE,'YYYY-MM-DD') ORDER_CREATED_DATE, TO_CHAR(ORDER_COMP_DATE,'YYYY-MM-DD') ORDER_COMP_DATE,ORDER_TYPE, ORDER_SUB_TYPE, ORDER_STATUS,
					SERVICE_SUB_TYPE,CUSTOMER_NAME,BILL_ACCNT_TYPE, BILL_ACCNT_SUB_TYPE, HOUSE_NO,VILLAGE_NAME,ADDITIONAL_DETAILS , MOBILE_NO,RECONNECTION_ORDER,PRODUCT_NAME FROM CDR_CRM_ORDERS A, ORDER_ITEMS B
					WHERE A.ORDER_NO=B.ORDER_NO AND  TRUNC(ORDER_COMP_DATE)  BETWEEN '" . $fdate . "' AND '" . $tdate . "'  AND B.PROD_CATG_CD='IN VPN Plan' AND SERVICE_TYPE LIKE '%paid IN%' AND ORDER_STATUS='Complete'
					AND ". $querystring ;

	}
	else if ($service=="FMT" || $service=="MMVC Postpaid" )// If the Service is FMT/MMVC Postpaid
	{
		switch ($_REQUEST['order_type'])
		{
			case "PROV":
				$heading= $service. " New Provsions between ". $fdate . " and " .$tdate ;
				$querystring = " ORDER_TYPE='New' ";
				break;
			case "DIS":
				$heading= $service. " Disconnetions between ". $fdate . " and " .$tdate;
				$querystring = "ORDER_TYPE='Disconnect'";
				break;
		}
		$sql= "SELECT MOBILE_NO,PHONE_NO, A.ORDER_NO,TO_CHAR(ORDER_CREATED_DATE,'YYYY-MM-DD') ORDER_CREATED_DATE, TO_CHAR(ORDER_COMP_DATE,'YYYY-MM-DD') ORDER_COMP_DATE,ORDER_TYPE, ORDER_SUB_TYPE, ORDER_STATUS,
					SERVICE_SUB_TYPE,CUSTOMER_NAME,BILL_ACCNT_TYPE, BILL_ACCNT_SUB_TYPE,HOUSE_NO,VILLAGE_NAME,ADDITIONAL_DETAILS , MOBILE_NO,RECONNECTION_ORDER,PRODUCT_NAME FROM CDR_CRM_ORDERS A, ORDER_ITEMS B
					WHERE  A.ORDER_NO=B.ORDER_NO AND TRUNC(ORDER_COMP_DATE)  BETWEEN '" . $fdate . "' AND '" . $tdate . "'  AND B.PROD_CATG_CD='Plan'  AND SERVICE_TYPE='".$service."' AND ORDER_STATUS='Complete'
					AND ". $querystring ;

	}

}

else if(isset($_REQUEST['exch'])) // If this page is arrived from gross_net_exch.php
{
	$exch= $_REQUEST['exch'];
	if($service=="LL") // If the Service is Landline
	{
		switch ($_REQUEST['order_type'])
		{
			case "LLPROV":
				$heading= "Landline New Provsions between ". $fdate . " and " .$tdate. " under " .$exch. " Exchange";
				$querystring = "ORDER_TYPE='New' AND ORDER_SUB_TYPE='Provision'";
				$plan_component= "Add";
				$product_cat = "('Plan','Combo Plan')";
				break;
			case "SHIFTPROV":
				$heading= "Landline Shift Provsions between ". $fdate . " and " .$tdate. " under " .$exch. " Exchange";
				$querystring = "ORDER_TYPE='Shift' AND ORDER_SUB_TYPE IN('Shift Across exchanges','Shift within STD','Shift Within exch with NumChg','Shift Within exch w/o NumChg')";
				$plan_component= "Add";
				$product_cat = "('Plan','Combo Plan')";
				break;
			case "LLDISVO":
				$heading= "Landline Voluntary Disconnections between ". $fdate . " and " .$tdate. " under " .$exch. " Exchange";
				$querystring = "ORDER_TYPE='Disconnect' AND ORDER_SUB_TYPE='Disconnect'";
				$plan_component= "Delete";
				$product_cat = "('Plan','Combo Plan')";
				break;
			case "LLDISNP":
				$heading= "Landline NP Disconnections between ". $fdate . " and " .$tdate. " under " .$exch. " Exchange";
				$querystring = "ORDER_TYPE='Disconnect' AND ORDER_SUB_TYPE='Disconnect Due to NP'";
				$plan_component= "Delete";
				$product_cat = "('Plan','Combo Plan')";
				break;
			case "LLDISMU":
				$heading= "Landline Misusage Disconnections between ". $fdate . " and " .$tdate. " under " .$exch. " Exchange";
				$querystring = "ORDER_TYPE='Disconnect' AND ORDER_SUB_TYPE='Disconnect Due to Misuse'";
				$plan_component= "Delete";
				$product_cat = "('Plan','Combo Plan')";
				break;
			case "SHIFTDIS":
				$heading= "Landline Shift Disconnections between ". $fdate . " and " .$tdate. " under " .$exch. " Exchange";
				$querystring = "ORDER_TYPE='Shift' AND ORDER_SUB_TYPE IN ('Disconnect - Number Changed','Disconnect')";
				$plan_component= "Delete";
				$product_cat = "('Plan','Combo Plan')";
				break;
			case "LLDIS":
				$heading= "Landline Disconnections between ". $fdate . " and " .$tdate. " under  " .$exch. " Exchange";
				$querystring = "ORDER_TYPE='Disconnect' AND ORDER_SUB_TYPE IN ('Disconnect','Disconnect Due to NP','Disconnect Due to Misuse')";
				$plan_component= "Delete";
				$product_cat = "('Plan','Combo Plan')";
				break;
		}
		$sql= "SELECT EXCHANGE_CODE,MOBILE_NO,PHONE_NO, A.ORDER_NO,TO_CHAR(ORDER_CREATED_DATE,'YYYY-MM-DD') ORDER_CREATED_DATE, TO_CHAR(ORDER_COMP_DATE,'YYYY-MM-DD') ORDER_COMP_DATE,ORDER_TYPE, ORDER_SUB_TYPE, ORDER_STATUS,
					SERVICE_SUB_TYPE,CUSTOMER_NAME,BILL_ACCNT_TYPE, BILL_ACCNT_SUB_TYPE,HOUSE_NO,VILLAGE_NAME,ADDITIONAL_DETAILS , MOBILE_NO,RECONNECTION_ORDER,PRODUCT_NAME  FROM CDR_CRM_ORDERS A, ORDER_ITEMS B 
					WHERE A.ORDER_NO=B.ORDER_NO AND EXCHANGE_CODE='" .$exch. "' AND TRUNC(ORDER_COMP_DATE)  BETWEEN '" . $fdate . "' AND '" . $tdate . "'  AND SERVICE_TYPE in ('Landline', 'ISDN', 'DSPT', 'LL PCO')
					AND PROD_CATG_CD in " .$product_cat. " AND PRODUCT_STATUS='" .$plan_component. "'
					AND ORDER_STATUS='Complete'  AND ". $querystring ;
			
	}
	else if($service=="BB") // If the Service is BROADBAND
	{
		switch ($_REQUEST['order_type'])
		{
			case "BBPROV":
				$heading= "Broadband New Provsions between ". $fdate . " and " .$tdate. " under ".$exch. " Exchange";
				$querystring = "ORDER_TYPE='Modify' AND ORDER_sub_type='Broadband Provision' ";
				$plan_component= "Add";
				$product_cat = "('BB Plan','Combo Plan','VPN Plan')";
				break;
			case "BBSHIFTPROV":
				$heading= "Broadband Shift Provsions between ". $fdate . " and " .$tdate. " under ".$exch. " Exchange";
				$querystring = "ORDER_TYPE='Shift' AND ORDER_SUB_TYPE IN('Shift Across exchanges','Shift within STD','Shift Within exch with NumChg','Shift Within exch w/o NumChg') AND BB_CONN_TYPE='POSTPAID' AND BB_USER_ID IS NOT NULL ";
				$plan_component= "Add";
				$product_cat = "('BB Plan','Combo Plan','VPN Plan')";
				break;
			case "BBDISWITHLL":
				$heading= "Broadband Disconnections with LL between ". $fdate . " and " .$tdate. " under ".$exch. " Exchange";
				$querystring = " ORDER_TYPE='Disconnect' AND ORDER_SUB_TYPE IN ('Disconnect','Disconnect Due to Misuse') AND BB_CONN_TYPE='POSTPAID' AND BB_USER_ID IS NOT NULL";
				$plan_component= "Delete";
				$product_cat = "('BB Plan','Combo Plan','VPN Plan')";
				break;
			case "BBDISNPWITHLL":
				$heading= "Broadband NP Disconnections with LL between ". $fdate . " and " .$tdate. " under ".$exch. " Exchange";
				$querystring = "ORDER_TYPE='Disconnect' AND ORDER_SUB_TYPE='Disconnect Due to NP' AND BB_CONN_TYPE='POSTPAID' AND BB_USER_ID IS NOT NULL";
				$plan_component= "Delete";
				$product_cat = "('BB Plan','Combo Plan','VPN Plan')";
				break;
			case "BBONLYDIS":
				$heading= "Broadband Only Disconnections between ". $fdate . " and " .$tdate. " under ".$exch. " Exchange";
				$querystring = "ORDER_TYPE='Modify' AND ORDER_SUB_TYPE='Broadband Disconnection'";
				$plan_component= "Delete";
				$product_cat = "('BB Plan','Combo Plan','VPN Plan')";
				break;
			case "BBSHIFTDIS":
				$heading= "Broadband Shift Disconnections between ". $fdate . " and " .$tdate. " under ".$exch. " Exchange";
				$querystring = "ORDER_TYPE='Shift' AND ORDER_SUB_TYPE IN ('Disconnect - Number Changed','Disconnect') AND BB_CONN_TYPE='POSTPAID' AND BB_USER_ID IS NOT NULL ";
				$plan_component= "Delete";
				$product_cat = "('BB Plan','Combo Plan','VPN Plan')";
				break;
			case "BBDIS":
				$heading= "Broadband Disconnections between ". $fdate . " and " .$tdate. " under ".$exch. " Exchange";
				$querystring = "((ORDER_TYPE='Disconnect' AND ORDER_SUB_TYPE IN ('Disconnect','Disconnect Due to Misuse','Disconnect Due to NP') AND BB_CONN_TYPE='POSTPAID' AND BB_USER_ID IS NOT NULL) OR (ORDER_TYPE='Modify' AND ORDER_SUB_TYPE='Broadband Disconnection'))";
				$plan_component= "Delete";
				$product_cat = "('BB Plan','Combo Plan','VPN Plan')";
				break;
		}
		$sql= "SELECT EXCHANGE_CODE,MOBILE_NO,PHONE_NO, A.ORDER_NO, PAR_ORDER_ID,TO_CHAR(ORDER_COMP_DATE,'YYYY-MM-DD') ORDER_COMP_DATE,ORDER_TYPE, ORDER_SUB_TYPE, ORDER_STATUS,
					SERVICE_SUB_TYPE,CUSTOMER_NAME,BILL_ACCNT_TYPE, BILL_ACCNT_SUB_TYPE,HOUSE_NO,VILLAGE_NAME,ADDITIONAL_DETAILS , MOBILE_NO,RECONNECTION_ORDER,PRODUCT_NAME  FROM CDR_CRM_ORDERS A, ORDER_ITEMS B
					WHERE EXCHANGE_CODE='" .$exch. "' AND TRUNC(ORDER_COMP_DATE)  BETWEEN '" . $fdate . "' AND '" . $tdate . "' AND SERVICE_TYPE in ('Landline', 'ISDN', 'DSPT', 'LL PCO')
					AND PROD_CATG_CD in " .$product_cat. " AND PRODUCT_STATUS='" .$plan_component. "'
					AND ORDER_STATUS='Complete'  AND A.ORDER_NO=B.ORDER_NO AND ". $querystring ;
	}
	else if($service=="FTTH")
	{
		
		$sql_prov= "SELECT CUST_ACCNT_NO FROM CDR_CRM_ORDERS WHERE ORDER_TYPE='New' AND TRUNC(ORDER_COMP_DATE) BETWEEN '" . $fdate . "' AND '" . $tdate . "' AND SERVICE_TYPE LIKE '%Bharat Fiber%' AND ORDER_STATUS='Complete' GROUP BY CUST_ACCNT_NO HAVING COUNT(*)=2";
		$odbcexec1 = odbc_exec($conn,$sql_prov);
	 while ($data1 = odbc_fetch_array($odbcexec1)){
			$data2 .= "'" .$data1[CUST_ACCNT_NO]. "',";	
		}
		
		$sql_dis= "SELECT CUST_ACCNT_NO FROM CDR_CRM_ORDERS WHERE ORDER_TYPE='Disconnect' AND TRUNC(ORDER_COMP_DATE) BETWEEN '" . $fdate . "' AND '" . $tdate . "' AND SERVICE_TYPE LIKE '%Bharat Fiber%' AND ORDER_STATUS='Complete' GROUP BY CUST_ACCNT_NO HAVING COUNT(*)=2";
		$odbcexec3 = odbc_exec($conn,$sql_dis);
	 while ($data3 = odbc_fetch_array($odbcexec3)){
			$data4 .= "'" .$data3[CUST_ACCNT_NO]. "',";	
		}
		switch ($_REQUEST['order_Sub_type'])
		{
			case "LL":
				
				if($_REQUEST['order_type']=="PROV"){
					$heading= "Only LL FTTH Provsions between ". $fdate . " and " .$tdate . "";
					$order_type = " ORDER_TYPE='New' AND ";
					$querystring1 = " ORDER_SUB_TYPE IN ('Broadband Provision',ORDER_SUB_TYPE IN ('Broadband Provision','Broadband VPN Provision')) AND ";
					$customerids=$data2;
					$plan_component= "Add";
					$product_cat = "('Bharat Fiber Combo Plan','Bharat Fiber BB Plans','Plan','VPN Plan')";
					}
				elseif($_REQUEST['order_type']=="DIS"){
					$heading= "Only LL FTTH Disconnetions between ". $fdate . " and " .$tdate . "";
					$order_type = "ORDER_TYPE='Disconnect' AND ";
					$querystring1 = "  SERVICE_SUB_TYPE in('FTTH Voice','Bharat Fiber Voice') AND ";
					$customerids=$data4;
					$plan_component= "Delete";
					$product_cat = "ORDER_SUB_TYPE IN ('Broadband Provision','Broadband VPN Provision')";
					}
				$querystring2 = " NOT IN ";
				break;
			case "BB":
				if($_REQUEST['order_type']=="PROV"){
					$heading= "Only BB FTTH Provsions between ". $fdate . " and " .$tdate . "";
					$order_type = " ORDER_TYPE='New' AND ";
					$querystring1 = " ORDER_SUB_TYPE IN ('Broadband Provision','Broadband VPN Provision') AND ";
					$customerids=$data2;
					$plan_component= "Add";
					$product_cat = "('Bharat Fiber Combo Plan','Bharat Fiber BB Plans','Plan','VPN Plan')";
					}
				elseif($_REQUEST['order_type']=="DIS"){
					$heading= "Only BB FTTH Disconnetions between ". $fdate . " and " .$tdate . "";
					$order_type = "ORDER_TYPE='Disconnect' AND ";
					$querystring1 = " SERVICE_SUB_TYPE in('FTTH BroadBand','Bharat Fiber BB') AND ";
					$customerids=$data4;
					$plan_component= "Delete";
					$product_cat = "('Bharat Fiber Combo Plan','Bharat Fiber BB Plans','Plan','VPN Plan')";
					}
				$querystring2 = " NOT IN ";
				break;
			case "LLBB":
				if($_REQUEST['order_type']=="PROV"){
				$heading= "FTTH Provsions (LL & BB) between ". $fdate . " and " .$tdate . "";
				$order_type = " ORDER_TYPE='New' AND ";
				$querystring1 = "";$customerids=$data2;
				$plan_component= "Add";
				$product_cat = "('Bharat Fiber Combo Plan','Bharat Fiber BB Plans','Plan','VPN Plan')";
				}
				elseif($_REQUEST['order_type']=="DIS"){
				$heading= "FTTH Disconnetions (LL & BB) between ". $fdate . " and " .$tdate . "";
				$order_type = " ORDER_TYPE='Disconnect' AND ";
				$querystring1 = "";$customerids=$data4;
				$plan_component= "Delete";
				$product_cat = "('Bharat Fiber Combo Plan','Bharat Fiber BB Plans','Plan','VPN Plan')";
				}
				$querystring2 = " IN ";
				break;		
		}
		
		$sql="SELECT EXCHANGE_CODE,MOBILE_NO,PHONE_NO, A.ORDER_NO,TO_CHAR(ORDER_CREATED_DATE,'YYYY-MM-DD') ORDER_CREATED_DATE, TO_CHAR(ORDER_COMP_DATE,'YYYY-MM-DD') ORDER_COMP_DATE,ORDER_TYPE, ORDER_SUB_TYPE, ORDER_STATUS,
					SERVICE_SUB_TYPE,CUSTOMER_NAME,BILL_ACCNT_TYPE, BILL_ACCNT_SUB_TYPE,HOUSE_NO,VILLAGE_NAME,ADDITIONAL_DETAILS , MOBILE_NO,RECONNECTION_ORDER,B.PRODUCT_NAME  FROM CDR_CRM_ORDERS A, ORDER_ITEMS B WHERE A.ORDER_NO=B.ORDER_NO AND ". $order_type . " ". $querystring1 . "  TRUNC(ORDER_COMP_DATE) BETWEEN '" . $fdate . "' AND '" . $tdate . "' AND SERVICE_TYPE LIKE '%Bharat Fiber%' AND ORDER_STATUS='Complete' AND CUST_ACCNT_NO ". $querystring2 . " (" . $customerids . "'123') AND A.EXCHANGE_CODE ='".$_REQUEST['exch']."'   AND B.PROD_CATG_CD in " .$product_cat. " AND B.PRODUCT_STATUS='" .$plan_component. "'";
	}
}

//echo $sql;
$odbcexec = odbc_exec($conn,$sql);
echo "<h3>" .$heading. "</h3><br>";
echo "<table class='datagrid' border='1' style='width:80%'>";
echo '<thead>';
echo "<tr>";
echo "<th>SNo</th>";
echo "<th>PHONE_NO</th>";
echo "<th>ORDER_NO</th>";
echo "<th>EXCHANGE<br>CODE</th>";
echo "<th>ORDER<br>TYPE</th>";
echo "<th>ORDER<br>SUB_TYPE</th>";
echo "<th>SERVICE<br>SUB_TYPE</th>";
echo "<th>ORDER_COMP_DATE</th>";
echo "<th>CUSTOMER_NAME</th>";
echo "<th>MOBILE_NO</th>";
echo "<th>HOUSE_NO</th>";
echo "<th>VILLAGE_NAME</th>";
echo "<th>PARENT_ORDER</th>";
echo "<th>RC(Y/N)</th>";
echo "<th>Plan</th>";
echo "</tr>";
echo '</thead>';
$sn=1;
while($data = odbc_fetch_array($odbcexec))
{
	echo "<tr>";
	echo "<td>" .$sn. "</td>";
	echo "<td>" .$data['PHONE_NO']. "</td>";
	echo "<td>" .$data['ORDER_NO']. "</td>";
	echo "<td>" .$data['EXCHANGE_CODE']. "</td>";
	echo "<td>" .$data['ORDER_TYPE']. "</td>";
	echo "<td>" .$data['ORDER_SUB_TYPE']. "</td>";
	echo "<td>" .$data['SERVICE_SUB_TYPE']. "</td>";
	echo "<td>" .$data['ORDER_COMP_DATE']. "</td>";
	echo "<td>" .$data['CUSTOMER_NAME']. "</td>";
	echo "<td>" .$data['MOBILE_NO']. "</td>";
	echo "<td>" .$data['HOUSE_NO']. "</td>";
	echo "<td>" .$data['VILLAGE_NAME']. "</td>";
	echo "<td>" .$data['PAR_ORDER_ID']. "</td>";
	if($service=="BB" && $data['PAR_ORDER_ID'] != ""){ // To Check whether BB Order is Reconnection/ New One since for all BB Provisions RECONNECTION_ORDER flag='N'
		$sql1="SELECT ORDER_NO, ROW_ID, RECONNECTION_ORDER FROM CDR_CRM_ORDERS WHERE ROW_ID='" .$data['PAR_ORDER_ID']. "'";
		//echo $sql1 . "<br>";
		$odbcexec1 = odbc_exec($conn,$sql1);
		$data1 = odbc_fetch_array($odbcexec1);
			if($data1['RECONNECTION_ORDER']=='Y')
			{
				echo "<td>Reconnection</td>";
			}
			else
			{
				echo "<td>" .$data1['RECONNECTION_ORDER']. "</td>";
			}
	}
	else{
		if($data['RECONNECTION_ORDER']=='Y')
		{
			echo "<td>Reconnection</td>";
		}
		else
		{
			echo "<td>" .$data['RECONNECTION_ORDER']. "</td>";
		}
	}
	echo "<td>" .$data['PRODUCT_NAME']. "</td>";	
	echo "</tr>";
	$sn++;
}

echo "</table>";
//echo $sql;
?>