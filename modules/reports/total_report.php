<?php
if(!defined("APP_START")) die("No Direct Access");
?>
<div class="page-header">
	<h1 class="title">Reports</h1>
  	<ol class="breadcrumb">
    	<li class="active">Total Report</li>
  	</ol>
  	<div class="right">
    	<div class="btn-group" role="group" aria-label="..."> 
        	<a id="topstats" class="btn btn-light" href="#"><i class="fa fa-search"></i></a> 
            <a class="btn print-btn" href="sales_manage.php?tab=report"><i class="fa fa-print" aria-hidden="true"></i></a>  
        </div>
  	</div>
</div>
<div class="panel-body table-responsive">
	<table class="table table-hover list">
    	<thead>
            <tr>
                <th width="5%" class="text-center">S.no</th>
                <th>Accounts </th>
                <th class="text-right">Debit</th>
                <th class="text-right" >Credit</th>
                <th class="text-right" >Balance</th>
            </tr>
    	</thead>
    	<tbody>
			<?php 
			$sql="select * from account where status=1";
            $rs=show_page($rows, $pageNum, $sql);
			$dt = get_last_closing_date();
            if(numrows($rs)>0){
                $sn=1;
				while($r=dofetch($rs)){             
                    ?>
                    <tr>
                        <td class="text-center"><?php echo $sn;?></td>
                        <td><?php echo unslash($r["title"] ); ?></td>
                        <td class="text-right"><?php echo get_account_balance( $r[ "id" ], $dt ) ?></td>
                        <td class="text-right"></td>
                        <td class="text-right"></td>
                    </tr>
                    <?php 
                    $sn++;
                }
                ?>
                <?php	
            }
            else{	
                ?>
                <tr>
                    <td colspan="6"  class="no-record">No Result Found</td>
                </tr>
                <?php
            }
            ?>
    	</tbody>
  	</table>
</div>
