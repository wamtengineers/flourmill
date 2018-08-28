<?php
if(!defined("APP_START")) die("No Direct Access");
?>
<style>
h1, h2, h3, p {
    margin: 0 0 10px;
}
h1,h2{
	font-size:14px;
	margin:5px 0;
}
body {
    margin:  0;
    font-family:  Arial;
    font-size:  10px;
}
.head th, .head td{ border:0;}
th, td {
    border: solid 1px #000;
    padding: 2px 5px;
    font-size: 10px;
	vertical-align:top;
}
table table th, table table td{
	padding:2px;
}
table {
    border-collapse:  collapse;
	max-width:1200px;
	margin:0 auto;
}
</style>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr class="head">
        <th colspan="10">
            <h1><?php echo get_config( 'site_title' )?></h1>
            <h2>SALES LIST</h2>
            <p>
                <?php
                if( !empty( $date ) ){
                    echo " Date ".$date;
                }
                ?>
            </p>
        </th>
    </tr>
    <tr>
        <th width="5%" style="text-align:center">S#</th>
        <th width="15%">Date</th>
        <th width="10%">Token Number</th>
        <th width="15%">Customer Name</th>
        <th width="15%">Items</th>
        <th width="8%">Packing</th>
        <th width="10%" style="text-align:right;">Total Items</th>
        <th width="10%" style="text-align:right;">Total Price</th>
        <th width="10%" style="text-align:right;">Payment Amount</th>
        <th style="text-align:center">Status</th>
    </tr>
    <tr>
        <td style="text-align:center"></td>
        <td style="text-align:left;"></td>
        <td></td>
        <td style="text-align:left;"></td>
        <td></td>
        <td style="text-align:right;">
        </td>
        <td style="text-align:right;"></td>
        <td style="text-align:right;"></td>
        <td style="text-align:right;"></td> 
        <td style="text-align:right;"></td>
    </tr>
    <tr>
        <th colspan="6" style="text-align:right;">Total</th>
        <th style="text-align:right;"></th>
        <th style="text-align:right;"></th>
        <th style="text-align:right;"></th>
        <th></th>
    </tr>
</table>
<?php
die;
