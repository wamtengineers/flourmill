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
						$dt = get_last_closing_date();
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
							?>
							<?php	
						}
						
						$sql="select * from customer where status=1";
						$rs=doquery($sql, $dblink);
						if( numrows($rs) > 0){
							$sn=1;
							while($r=dofetch($rs)){             
								$balance = get_customer_balance( $r[ "id" ] );
								if( $balance > 0 ) {
									$customers_receivable[] = array(
										"name" =>  unslash( $r["customer_name"] ),
										"balance" => $balance
									);
								}
								else {
									$customers_payable[] = array(
										"name" =>  unslash( $r["customer_name"] ),
										"balance" => $balance
									);
								}
							}
						}
						
						if( count( $customers_receivable ) > 0 ) {
							?>
							<thead>
                                <tr>
                                    <th colspan="2">Account Receivable ( Customers )</th>
                                </tr>
                            </thead>
							<?php
							foreach( $customers_receivable as $customer ) {
								$total += $balance;
								?>
								<tr>
									<td><?php echo $customer["name"]; ?></td>
									<td class="text-right"><?php echo curr_format( $customer[ "balance" ] ) ?></td>
								</tr>
								<?php 
								$sn++;
							}
						}
						$sql="select * from supplier where status=1";
						$rs=doquery($sql, $dblink);
						if( numrows($rs) > 0){
							$sn=1;
							while($r=dofetch($rs)){             
								$balance = get_supplier_balance( $r[ "id" ] );
								if( $balance > 0 ) {
									$suppliers_receivable[] = array(
										"name" =>  unslash( $r["supplier_name"] ),
										"balance" => $balance
									);
								}
								else {
									$suppliers_payable[] = array(
										"name" =>  unslash( $r["supplier_name"] ),
										"balance" => $balance
									);
								}
							}
						}
						
						if( count( $suppliers_receivable ) > 0 ) {
							?>
							<thead>
                                <tr>
                                    <th colspan="2">Account Receivable ( Suppliers )</th>
                                </tr>
                            </thead>
							<?php
							foreach( $suppliers_receivable as $supplier ) {
								$total += $balance;
								?>
								<tr>
									<td><?php echo $supplier["name"]; ?></td>
									<td class="text-right"><?php echo curr_format( $supplier[ "balance" ] ) ?></td>
								</tr>
								<?php 
								$sn++;
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
						if( count($customers_payable) > 0){
							?>
							<thead>
                                <tr>
                                    <th colspan="2">Account Payable (Customers)</th>
                                </tr>
                            </thead>
							<?php
							$sn=1;
							foreach( $customers_payable as $customer ){
								?>
								<tr>
									<td><?php echo $customer["name"]; ?></td>
									<td class="text-right"><?php echo curr_format( $customer[ "balance" ] ) ?></td>
								</tr>
								<?php 
								$sn++;
							}
							?>
							<?php	
						}
						if( count($suppliers_payable) > 0){
							?>
							<thead>
                                <tr>
                                    <th colspan="2">Account Payable (Suppliers)</th>
                                </tr>
                            </thead>
							<?php
							$sn=1;
							foreach( $suppliers_payable as $supplier ){
								?>
								<tr>
									<td><?php echo $supplier["name"]; ?></td>
									<td class="text-right"><?php echo curr_format( $supplier[ "balance" ] ) ?></td>
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
