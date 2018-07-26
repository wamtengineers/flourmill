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
<style>
@font-face {
    font-family: 'NafeesRegular';
    src: url('fonts/NafeesRegular.ttf') format('truetype');
    font-weight: normal;
    font-style: normal;

}
.nastaleeq, #name_in_urdu_text{font-family: 'NafeesRegular'; direction:rtl; unicode-bidi: embed;   }
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
#right_title {
	font-size: 18px;
	font-style: italic;
	font-weight: bolder;
	float: right;
	margin-right: 5px;
	text-decoration: underline;
}
#center_title {
	font-size: 22px;
	font-style: normal;
	font-weight: bold;
	float: right;
	padding-top: 45px;
	text-transform: uppercase
}
#inv_status {
	margin-bottom: 30px;
	font-size: 14px;
}
#inv_status_alrt {
	font-size: 16px;
	font-weight: bold;
	text-align: center;
	border: thin solid #666;
	float: right;
	margin-right: 5px;
	position: relative;
	padding-top: 5px;
	padding-right: 30px;
	padding-bottom: 5px;
	padding-left: 30px;
}
#project {
	float: left;
	font-size: 14px;
}
#project div {
	margin-bottom: 5px;
}
#customer {
	float: right;
	text-align: center;
	line-height: 1em;
}
#jbnum {
	width: 200px;
	padding: 5px;
	line-height: 1em;
	margin-bottom: 5px;
	background-color: #444;
	color: #fff;
}
#customer span {
	color: #000000;
	text-align: left;
	width: 52px;
	margin-right: 10px;
	display: inline-block;
	font-size: 13px;
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
.data-table td{border:1px solid #afafaf;}
.data-table td strong{text-align:right;display:block}
#th_center {
	text-align: center;
	border-bottom-width: thin;
	border-bottom-style: solid;
	border-bottom-color: #666666;
}
#cinfo_table {
	height: auto;
	width: 49%;
	float: left;
}
#cinfo_table_cntr {
	height: auto;
	width: 260px;
	margin-left: 266px;
}
#cinfo_table_rgt {
	height: auto;
	width: 49%;
	float: right;
}
#inchk_table {
	float: left;
	width: 393px;
}
#inchk_table td {
	border: thin solid #CCCCCC;
	padding-top: 1px;
	padding-bottom: 1px;
	line-height: 1.5em;
}
#othrd_table {
	float: right;
	width: 393px;
}
#othrd_table td {
	border: thin solid #CCC;
	padding-top: 1px;
	padding-bottom: 1px;
	line-height: 1.5em;
}
.tableamount {
	text-align: right;
}
#acc {
	border: thin solid #000;
	padding-right: 15px;
	display: block;
	line-height: 20px;
}
#rbr {
	border-right-width: thin;
	border-right-style: solid;
	border-right-color: #000;
	background-color: #ccc;
	width: 100px;
	white-space: nowrap;
	float: left;
	padding-left: 10px;
}
#acc span {
	margin-left: 15px;
}
table .service, table .desc {
	text-align: left;
}
table td {
	text-align: right;
	padding-top: 10px;
	padding-right: 2px;
	padding-bottom: 10px;
	padding-left: 2px;
	font-size: 26px;
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
#notices {
	margin-top: 20px;
	float: left;
	clear: both;
	width: 100%;
}

#signcompny {
    border-top: thin solid #000;
    margin: 10px 0 0;
    padding-top: 10px;
    text-align: center;
}
#signcus {
	text-align: center;
	border-top-width: thin;
	border-top-style: solid;
	border-top-color: #000;
	margin-right: 5px;
	margin-top: 100px;
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
.comnme {
	font-size: 22px;
	font-weight: bold;
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
#receipt {
    border: 1px solid;
    border-radius: 3px;
    display: block;
    font-size: 18px;
    font-weight: bold;
    line-height: 13px;
    margin: 10px auto 16px;
    padding: 5px;
    text-align: center;
    width: 82px;
}
#order {
    border: 1px solid #000000;
    border-radius: 5px;
    color: #000000;
    display: block;
    font-size: 18px;
    font-weight: bold;
    line-height: 16px;
    margin: 0px auto 10px;
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
padding: 6px 14px;
border-radius: 100%;
}
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
	?>
    <div id="order">Token Number: <strong><?php echo $order_id; ?></strong></div>
    <div class="contentbox">
        <p>Date/Time: <strong style="float:right"><?php echo datetime_convert($sale["datetime_added"]); ?></strong></p>
        <table cellpadding="0" cellspacing="0" align="center" width="800" border="0" class="items">
            <?php
            $items=doquery("select a.*, b.title, c.title as category from sales_items a left join items b on a.item_id=b.id left join item_category c on b.item_category_id = c.id where sales_id='".$sale["id"]."' order by c.id, b.sortorder desc", $dblink);
			$total_discount = 0;
            if(numrows($items)>0){
                $sn=1;
                while($item=dofetch($items)){
                    ?>
                    <tr>
                    	<td style="text-align:left;"><span class="item_name"><?php echo unslash($item["title"])?></span> &times; <span class="qty"><?php echo $item["quantity"]?></span> <?php echo $item["category"]?>
                        	<?php
                            if( $item[ "items_variations_id" ] > 0 ) {
								$attributes = dofetch( doquery( "select group_concat( `value` SEPARATOR ' / ') as attributes from items_variations_attributes where items_variations_id = '".$item[ "items_variations_id" ]."' ", $dblink ) );
								echo $attributes[ "attributes" ]!=""?" ( ".$attributes[ "attributes" ]." )":"";	
							}
							?>
                        </td>
                    </tr>
                    <?php
					$total_discount += $item["discount"];
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