<?php
if(!defined("APP_START")) die("No Direct Access");
$rs = doquery( $sql, $dblink );
?>
<style>
h1, h2, h3, p {
    margin: 0 0 10px;
}

body {
    margin:  0;
    font-family:  Arial;
    font-size:  11px;
}
.head th, .head td{ border:0;}
th, td {
    border: solid 1px #000;
    padding: 5px 5px;
    font-size: 11px;
	vertical-align:top;
}
table table th, table table td{
	padding:3px;
}
table {
    border-collapse:  collapse;
	max-width:1200px;
	margin:0 auto;
}
.head{
}
</style>
<table width="100%" cellspacing="0" cellpadding="0">
<tr class="head">
	<th colspan="7">
    	<h1><?php echo get_config( 'site_title' )?></h1>
    	<h2>Items List</h2>
        <p>
        	<?php
			echo "List of";
			if( !empty( $category ) ){
				echo " Category: ".get_field($category, "item_category","title");
			}
			?>
        </p>
    </th>
    
</tr>
<tr>
    <th width="5%" align="center">S.no</th>
    <th width="10%">Item Category</th>
    <th width="20%">Title</th>
</tr>
<?php
if( numrows( $rs ) > 0 ) {
	$sn = 1;
	while( $r = dofetch( $rs ) ) {
		?>
		<tr>
        	<td align="center"><?php echo $sn;?></td>
            <td><?php echo unslash($r["title"]); ?></td>
        </tr>
		<?php
	}
}
?>
</table>
<?php
die;