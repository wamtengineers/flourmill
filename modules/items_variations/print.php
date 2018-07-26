<?php
if(!defined("APP_START")) die("No Direct Access");
if(isset($_GET["id"]) && !empty($_GET["id"])){
	$id = $_GET["id"];
	$attributes = dofetch( doquery( "select group_concat( `value` SEPARATOR ' / ') as attributes from items_variations_attributes where items_variations_id = '".slash($id)."' ", $dblink ) );
	$rs=doquery("select b.* from items_variations a inner join items b on a.item_id = b.id where a.id='".slash($id)."'",$dblink);
	if(numrows($rs)>0){
		$r=dofetch($rs);
		$barcode = "1".str_repeat('0', 4-strlen($id)).$id;
		?>
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link type="text/css" rel="stylesheet" href="css/barcode.css" />
        <script type="text/javascript" src="js/jquery.js"></script>
        <script>
		function print_page(){
			printer = '<?php echo get_config( 'barcode_printer_title' );?>';
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
				jsPrintSetup.setOption('numCopies', <?php if( isset($_GET["copies"]) && is_integer((int)$_GET["copies"]) && (int)$_GET["copies"] > 0 ) echo $_GET["copies"]; else echo "1";?>);
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
		$(window).load(function(){
			print_page();
		});
        </script>
		</head>
		<body>
		<div class="wrapper">
			<span class="barcode"><img src="barcode.php?text=<?php echo $barcode?>&size=30" /></span>
			<span class="number"><?php echo $barcode?></span>
			<span class="item_name" style="font-size: 10px;"  ><?php echo substr( unslash( $r[ "title" ] ), 0, 50 ); echo $attributes[ "attributes" ]!=""?"<br />( ".$attributes[ "attributes" ]." )":""?></span>
        	<span class="item_name price">Price: <?php echo curr_format( $r[ "unit_price" ] )?></span>
		</div>
       <!--<div class="wrapper">
			<span class="barcode"><img src="barcode.php?text=<?php echo $barcode?>&size=20" /></span>
			<span class="number"><?php echo $barcode?></span>
			<span class="item_name"><?php echo substr( unslash( $r[ "title" ] ), 0, 50 )?></span>
        	<span class="item_name price">Price: <?php echo curr_format( $r[ "unit_price" ] )?></span>
            <span class="item_name quantity">Pcs: <?php echo $itemQuantity ?></span>
		</div>-->
		</body>
		</html>
		<?php
	}
}
?>