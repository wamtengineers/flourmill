<?php
if(!defined("APP_START")) die("No Direct Access");
if(isset($_GET["id"]) && !empty($_GET["id"])){
	$sale=dofetch(doquery("select * from sales where id='".slash($_GET["id"])."'", $dblink));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Invoice</title>
<link type="text/css" rel="stylesheet" href="css/barcode.css" />
<style>
@font-face {
    font-family: 'NafeesRegular';
    src: url('fonts/NafeesRegular.ttf') format('truetype');
    font-weight: normal;
    font-style: normal;

}
.clearfix:after {
	content: "";
	display: table;
	clear: both;
}
#main {
width:71mm;
border:0;
}
a {
	color: #5D6975;
	text-decoration: underline;
}
body {
	position: relative;
	margin: 0;
	color: #000;
	font-size: 12px;
	font-family: Arial, Helvetica, sans-serif;
	padding: 0px
}
p{margin:0 0 5px 0}
#logo {
	text-align: center;
	margin-bottom: 10px;
}
#logo img {
    width: 100%;
	margin-bottom: 0.7em;
}

#company {
	float: right;
	text-align: right;
}
table {
	width: 100%;
	border-collapse: collapse;
	border-spacing: 0;
	margin-bottom: 0px;
}
table tr:nth-child(2n-1) td {
	background: #F5F5F5;
}
table th, table td {
	text-align: left;
}
table th {
    border: 1px solid #fff;
    color: #fff;
    font-weight: bold;
    line-height: 0.9em;
    padding: 10px 0;
    text-align: center;
	background-color:#000;
    white-space: nowrap;
}

table td {
	text-align: right;
	padding-top: 6px;
	padding-right: 2px;
	padding-bottom: 6px;
	padding-left: 2px;
	font-size: 20px;
}
table tr{ font-size:10px}
table td.service, table td.desc {
	vertical-align: top;
}
table td.unit, table td.qty, table td.total {
	font-size: 1.2em;
}
table td.grand {
	border-top: 1px solid #5D6975;
	;
}

#signcompny {
    border-top: thin solid #000;
    margin: 10px 0 0;
    padding-top: 10px;
    text-align: center;
}
footer {
	color: #5D6975;
	width: 100%;
	height: 30px;
	position: absolute;
	bottom: 0;
	border-top: 1px solid #C1CED9;
	padding: 8px 0;
	text-align: center;
}
.contentbox{display:block}

#logo {
    border-radius: 3px;
    display: block;
    font-size: 30px;
    font-weight: bold;
    margin: 0px auto;
    padding: 6px 15px;
	font-size:1.2em
}

#order {
    border: 1px solid #000000;
    border-radius: 5px;
    color: #000000;
    display: block;
    font-size: 18px;
    font-weight: bold;
    line-height: 16px;
    margin: 0px auto 0px;
    padding: 5px;
    text-align: center;
    width: 180px;
}
#logo span {
    line-height: 12px;
	font-size:10px;
}
#logo h3{
	margin:0;}
.qty{
	border: 1px solid;
	padding: 10px 5px;
	border-radius: 100%;
	display: inline-block;
	width: 38px;
	height: 28px;
	text-align: center;
	line-height: 28px;
}
.barcode img{ width:145px;}
.item_name {
    width: 78px;
    display: inline-block;
}
</style>
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
<body onload="print_page();">
<div id="main">
   
    <?php
	$order_id = get_token_number( $sale );
	$barcode = str_repeat('0', 7-strlen($sale[ "id" ])).$sale[ "id" ];
	?>
    <div id="order">Token Number: <strong><?php echo $order_id; ?></strong></div>
    <div class="barcode_num">
        <span class="barcode"><img src="barcode.php?text=<?php echo $barcode?>&size=30" /></span>
        <span class="number"><?php echo $barcode?></span>
    </div>
    <div class="contentbox">
        <p>Date/Time: <strong style="float:right"><?php echo datetime_convert($sale["datetime_added"]); ?></strong></p>
        <table cellpadding="0" cellspacing="0" align="center" width="800" border="0" class="items">
            <?php
            $items=doquery("select a.*, b.title from sales_items a left join items b on a.item_id=b.id where sales_id='".$sale["id"]."' order by b.sortorder desc", $dblink);
			$total_discount = 0;
            if(numrows($items)>0){
                $sn=1;
                while($item=dofetch($items)){
                    ?>
                    <tr>
                    	<td style="text-align:left;"><span class="item_name"><?php echo unslash($item["title"])?></span> &times; <span class="qty"><?php echo $item["quantity"]?></span> <?php echo $item["packing"]?>KG
                        	
                        </td>
                    </tr>
                    <?php
					$total_discount += $sale["discount"];
                }
            }
            ?>
        </table>
    </div>
    <div id="signcompny">Software developed by wamtSol http://wamtsol.com/ - 0346 3891 662</div> 
</div>
</body>
</html>
<?php
die;
}