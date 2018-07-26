<?php
include("include/db.php");
include("include/utility.php");
include("include/session.php");
include("include/paging.php");
define("APP_START", 1);
$filename = 'sales_manage.php';
include("include/admin_type_access.php");
$tab_array=array("daily", "general_journal","balance_sheet","general_journal_print", "income", "income_print", "trial_balance", "trial_balance_print",);
if(isset($_REQUEST["tab"]) && in_array($_REQUEST["tab"], $tab_array)){
	$tab=$_REQUEST["tab"];
}
else{
	$tab="daily";
}

switch($tab){
	case 'general_journal':
		include("modules/reports/general_journal_do.php");
	break;
	case 'general_journal_print':
		include("modules/reports/general_journal_print.php");
	break;
	case 'income_print':
		include("modules/reports/income_print.php");
	break;
	case 'trial_balance_print':
		include("modules/reports/trial_balance_print.php");
	break;
}
?>
<?php include("include/header.php");?>
  <div class="container-widget row">
    <div class="col-md-12">
      <?php
		switch($tab){
			case 'daily':
				include("modules/reports/daily.php");
			break;
			case 'income':
				include("modules/reports/income.php");
			break;
			case 'general_journal':
				include("modules/reports/general_journal.php");
			break;
			case 'balance_sheet': 
				include("modules/reports/balance_sheet.php");
			break;
			case 'income':
				include("modules/reports/income.php");
			break;
			case 'trial_balance':
				include("modules/reports/trial_balance.php");
			break;
		}
      ?>
    </div>
  </div>
</div>
<?php include("include/footer.php");?>