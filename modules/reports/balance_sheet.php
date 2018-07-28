<?php
if(!defined("APP_START")) die("No Direct Access");
?>
<div class="page-header">
	<h1 class="title">Balance Sheet</h1>
  	<ol class="breadcrumb">
    	<li class="active">Balance Sheet</li>
  	</ol>
  	
</div>
<div class="panel-body table-responsive">
	<table class="table table-hover list">
    	<thead>
            <tr>
                <th width="50%">Assets</th>
                <th>Liabilities</th>
            </tr>
    	</thead>
    	<tbody>
        	<tr>
				<td>
					<table  class="table table-hover list">
						<thead>
                            <tr>
                                <th colspan="2">Current Assets</th>
                            </tr>
                        </thead>
						<?php 
						$sql="select * from account where status=1";
						$rs=doquery($sql, $dblink);
						$dt = get_last_closing_dt();
						$total = 0;
						$account_payable = array();
						$customers_receivable = array();
						$customers_payable = array();
						$suppliers_receivable = array();
						$suppliers_payable = array();
						if( numrows($rs) > 0){
							$sn=1;
							while($r=dofetch($rs)){             
								$balance = get_account_balance( $r[ "id" ], $dt );
								if( $balance >= 0 ) {
									$total += $balance;
									?>
									<tr>
										<td><?php echo unslash($r["title"] ); ?></td>
										<td class="text-right"><?php echo curr_format( $balance ) ?></td>
									</tr>
									<?php 
									$sn++;
								}
								else {
									$account_payable[] = array(
										"name" => unslash($r["title"] ),
										"balance" => $balance
									);
								}
							}
						}
						?>
                  	</table>
              	</td>
                <td>
					<table class="table table-hover list">
						<?php 
						if( count($account_payable) > 0){
							?>
							<thead>
                                <tr>
                                    <th colspan="2">Accounts</th>
                                </tr>
                            </thead>
							<?php
							$sn=1;
							foreach( $account_payable as $account ){
								?>
								<tr>
									<td><?php echo $account["name"]; ?></td>
									<td class="text-right"><?php echo curr_format( $account[ "balance" ] ) ?></td>
								</tr>
								<?php 
								$sn++;
							}
							?>
							<?php	
						}
						?>
                  	</table>
              	</td>
           	</tr>
    	</tbody>
  	</table>
</div>
