<?php 
include("include/db.php");
include("include/utility.php");
include("include/session.php");
define("APP_START", 1);
$date = date( "Y-m-d 00:00:00" );
$from = get_last_closing_dt( $date );
$to = get_next_closing_dt( $date );
if( isset( $_GET[ "id" ] ) ) {
	$id = slash( $_GET[ "id" ] );
	if( $_SESSION[ "logged_in_admin" ][ "admin_type_id" ] == 1 ) {
		$sale = doquery( "select * from sales where id = '".$id."'", $dblink );
		if( numrows( $sale ) > 0 ) {
			header( "Location: sales_manage.php?tab=addedit&id=".$id );
			die;
		}
	}
	$sale = doquery( "select * from sales where id = '".$id."' and datetime_added > '".$from."' and datetime_added <= '".$to."'", $dblink );
	if( numrows( $sale ) == 0 ) {
		$sale = doquery( "select a.* from sales a inner join sales_revalidate b on a.id = b.sales_id where b.ts > '".$from."' and b.ts <= '".$to."'", $dblink );
	}
	$class = 'danger';
	if( numrows( $sale ) > 0 ) {
		$sale = dofetch( $sale );
		$packer_id = get_config( 'packer_id' );
		$gate_keeper_id = get_config( 'gate_keeper_id' );
		if( $_SESSION[ "logged_in_admin" ][ "admin_type_id" ] == $packer_id ) {
			if( $sale[ "status" ] == 2 ) {
				$class = 'success';
				$status = 1;
				doquery( "update sales set status = '3' where id = '".$id."'", $dblink );
			} else {
				if( $sale[ "status" ] == 3 ) {
					$class = 'info';
				} else {
					$class = 'danger';
				}
				doquery( "insert into sales_history(sales_id, checking_point, log_by) values( '".$id."', '1', '".$_SESSION[ "logged_in_admin" ][ "id" ]."' )", $dblink );
			}
		}
		else if( $_SESSION[ "logged_in_admin" ][ "admin_type_id" ] == $gate_keeper_id ) {
			if( $sale[ "status" ] == 3 ) {
				$class = 'success';
				doquery( "update sales set status = '1' where id = '".$id."'", $dblink );
			} else {
				$class = 'danger';
				doquery( "insert into sales_history(sales_id, checking_point, log_by) values( '".$id."', '2', '".$_SESSION[ "logged_in_admin" ][ "id" ]."' )", $dblink );
			}
		}
	}
}
?>
<style>
.coupon_status.danger {
    background-color:  red;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}
.coupon_status.info {
    background-color:  orange;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}
.coupon_status.success {
    background-color:  green;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}
</style>
<div class="coupon_status <?php echo $class?>">
	<?php
    print_r($sale);
	?>
</div>