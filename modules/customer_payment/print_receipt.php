<?php
if(!defined("APP_START")) die("No Direct Access");
if(isset($_GET["id"]) && !empty($_GET["id"])){
	$customer_payment=dofetch(doquery("select * from customer_payment where id='".slash($_GET["id"])."'", $dblink));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Customer Payment</title>
<link type="text/css" rel="stylesheet" href="css/print.css" />
<script>
		function print_page(){
			printer = '<?php echo get_config( 'thermal_printer_title' );?>';
			printers = jsPrintSetup.getPrintersList().split(",");
			if( printers.indexOf( printer ) !== -1 ) {
				jsPrintSetup.setPrinter( printer );
				jsPrintSetup.setOption('orientation', jsPrintSetup.kPortraitOrientation);
				// set top margins in millimeters
				jsPrintSetup.setOption('marginTop', 0);
				jsPrintSetup.setOption('marginBottom', 0);
				jsPrintSetup.setOption('marginLeft', 0);
				jsPrintSetup.setOption('marginRight', 0);
				// set page header
				jsPrintSetup.setOption('headerStrLeft', '');
				jsPrintSetup.setOption('headerStrCenter', '');
				jsPrintSetup.setOption('headerStrRight', '');
				// set empty page footer
				jsPrintSetup.setOption('footerStrLeft', '');
				jsPrintSetup.setOption('footerStrCenter', '');
				jsPrintSetup.setOption('footerStrRight', '');
				jsPrintSetup.setOption('printBGColors', 1);
				// Suppress print dialog
				jsPrintSetup.setSilentPrint(true);
				// Do Print
				jsPrintSetup.printWindow(window);
				// Restore print dialog
				//jsPrintSetup.setSilentPrint(false);
			}
			else {
				alert( printer + " is not installed." );
			}
			
		}
</script>
</head>
<style>
#receipt {
    border: 1px solid;
    border-radius: 3px;
    display: block;
    font-size: 11px;
    font-weight: bold;
    line-height: 17px;
    margin: 0px auto 10px;
    padding: 5px;
    text-align: center;
    width: 168px;
    text-transform: uppercase;
}
#logo{margin: 10px auto 0; font-size:15px;}
</style>
<body onload="print_page();">
<div id="main">
    <div id="logo"><?php $reciept_logo=get_config("reciept_logo"); if(empty($reciept_logo)) echo $site_title; else { ?><img src="<?php echo $file_upload_root;?>config/<?php echo $reciept_logo?>" /><?php }?></span>
    <?php echo get_config("address_phone")?>
    </div>
    <div id="receipt">Customer Payment RECEIPT</div>
    <div class="contentbox">
    	<?php
		$ts = strtotime( $customer_payment["datetime_added"] );
		?>
        <p>Invoice ID: <strong style="float:right"><?php echo $customer_payment["id"]; ?>/<?php echo date("m/d/y", $ts)?></strong></p>
        <p>Date/Time: <strong style="float:right"><?php echo datetime_convert($customer_payment["datetime_added"]); ?></strong></p>
        <table cellpadding="0" cellspacing="0" align="center" width="800" border="0" class="items">
            <tr>
                <th width="65%">Customer</th>
                <th width="20%">Account</th>
                <th width="10%">Amount</th>
            </tr>
            <tr>
                <td style="text-align:left; font-size:11px;"><?php echo get_field($customer_payment["customer_id"], "customer","customer_name"); ?></td>
                <td style="text-align:left; font-size:11px;"><?php echo get_field($customer_payment["account_id"], "account","title"); ?></td>
                <td style="text-align:right; font-size:11px;"><?php echo curr_format($customer_payment["amount"])?></td>  
            </tr>
        </table>
    </div>
    <div id="signcompny">Software developed by wamtSol http://wamtsol.com/ - 0346 3891 662</div> 
</div>
</body>
</html>
<?php
die;
}