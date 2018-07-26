<?php 
include("include/db.php");
include("include/utility.php");
include("include/session.php");
define("APP_START", 1);
?>
<?php include("include/header.php");?>		
   	<div class="page-header">
        <h1 class="title">Cashbook</h1>
        <ol class="breadcrumb">
            <li class="active">Welcome to <?php echo $site_title?> Cashbook.</li>
        </ol>
    </div>
    <div id="item-row" class="cashbook">
        <div class="row clearfix">
            <div class="col-md-6">
                <div id="total-expense" class="expense-form">
                    <table width="100%" class="table table-hover list">
                        <thead>
                            <tr>
                            	<th colspan="7" class="bg-info padding-8">Supplier Payment</th>
                            </tr>
                            <tr class="head">
                                <th width="5%" class="text-center">S.N</th>
                                <th width="10%">Time</th>
                                <th width="15%">Supplier Name</th>
                                <th width="18%">Details</th>
                                <th width="12%" class="text-right">Amount</th>
                                <th width="15%">Paid By</th>
                                <th width="5%"></th>
                            </tr>
                        </thead>
                        <tr>
                        	<td class="text-center icon-row">1 </td>
                        	<td>09.35AM</td>
                            <td>Tariq</td>
                            <td>Test</td>
                            <td class="text-right">1200.00</td>
                            <td colspan="2">Cash in Hand</td>
                            
                        </tr>
                        <tr>
                            <td class="text-center icon-row">2</td>
                            <td colspan="2">
                                <select style="font-size: 12px; color:#000">
                                    <option value="">Select Supplier</option>
                                </select>
                            </td>
                            <td>
                                <textarea class="form-control" placeholder="Details"></textarea>
                            </td>
                            <td>
                                <input type="text" id="amount" class="form-control text-right" placeholder="Amount">
                            </td>
                            <td class="text-right" style="position:relative">
                                <select style="font-size: 12px; color:#000">
                                    <option value="">Select Account</option>
                                </select>
                            </td>
                            <td>
                            	<span class="remove-icon"><i class="fa fa-minus"></i></span>
                                <span class="add-icon"><i class="fa fa-plus"></i></span>
                            </td>
                        </tr>
                        <tr>
                        	<td colspan="7" class="text-right"><input type="button" class="btn btn-default btn-l" value="Save"></td>
                        </tr>
                    </table>
                </div>
                <div id="total-expense" class="expense-form">
                    <table width="100%" class="table table-hover list">
                        <thead>
                            <tr>
                            	<th colspan="7" class="bg-info padding-8">Customer Payment</th>
                            </tr>
                            <tr class="head">
                                <th width="5%" class="text-center">S.N</th>
                                <th width="10%">Time</th>
                                <th width="15%">Customer Name</th>
                                <th width="18%">Details</th>
                                <th width="12%" class="text-right">Amount</th>
                                <th width="15%">Paid By</th>
                                <th width="5%"></th>
                            </tr>
                        </thead>
                        <tr>
                        	<td class="text-center icon-row">1 </td>
                        	<td>09.35AM</td>
                            <td>Tariq</td>
                            <td>Test</td>
                            <td class="text-right">1200.00</td>
                            <td colspan="2">Cash in Hand</td>
                        </tr>
                        <tr>
                            <td class="text-center icon-row">2 </td>
                            <td colspan="2">
                                <select style="font-size: 12px; color:#000">
                                    <option value="">Select Customer</option>
                                </select>
                            </td>
                            <td>
                                <textarea class="form-control" placeholder="Details"></textarea>
                            </td>
                            <td>
                                <input type="text" id="amount" class="form-control text-right" placeholder="Amount">
                            </td>
                            <td class="text-right" style="position:relative">
                                <select style="font-size: 12px; color:#000">
                                    <option value="">Select Account</option>
                                </select>
                            </td>
                            <td>
                            	<span class="remove-icon"><i class="fa fa-minus"></i></span>
                                <span class="add-icon"><i class="fa fa-plus"></i></span>
                            </td>
                        </tr>
                    </table>
                </div>
                <div id="total-expense" class="expense-form">
                    <table width="100%" class="table table-hover list">
                        <thead>
                            <tr>
                            	<th colspan="7" class="bg-info padding-8">Fund Transfer</th>
                            </tr>
                            <tr class="head">
                                <th width="5%" class="text-center">S.N</th>
                                <th width="10%">Time</th>
                                <th width="15%">From Account</th>
                                <th width="15%">To Account</th>
                                <th width="12%" class="text-right">Amount</th>
                                <th width="18%">Details</th>
                                <th width="5%"></th>
                            </tr>
                        </thead>
                        <tr>
                        	<td class="text-center icon-row">1 </td>
                        	<td>09.35AM</td>
                            <td>Cash in Hand</td>
                            <td>Tariq</td>
                            <td class="text-right">1200.00</td>
                            <td colspan="2">Test</td>
                        </tr>
                        <tr>
                            <td class="text-center icon-row">2 </td>
                            <td colspan="2">
                                <select style="font-size: 12px; color:#000">
                                    <option value="">From Account</option>
                                </select>
                            </td>
                            <td>
                                <select style="font-size: 12px; color:#000">
                                    <option value="">To Account</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" id="amount" class="form-control text-right" placeholder="Amount">
                            </td>
                            <td class="text-right" style="position:relative">
                                <textarea class="form-control" placeholder="Details"></textarea>
                            </td>
                            <td>
                            	<span class="remove-icon"><i class="fa fa-minus"></i></span>
                                <span class="add-icon"><i class="fa fa-plus"></i></span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="col-md-6">
                <div id="total-expense" class="expense-form">
                    <table width="100%" class="table table-hover list">
                        <thead>
                            <tr>
                            	<th colspan="7" class="bg-info padding-8">Expense</th>
                            </tr>
                            <tr class="head">
                                <th width="5%" class="text-center">S.N</th>
                                <th width="10%">Time</th>
                                <th width="18%">Expense Category</th>
                                <th width="18%">Details</th>
                                <th width="12%" class="text-right">Amount</th>
                                <th width="15%">Paid By</th>
                                <th width="5%"></th>
                            </tr>
                        </thead>
                        <tr>
                        	<td class="text-center icon-row">1</td>
                        	<td>09.35AM</td>
                            <td>Tariq</td>
                            <td>Test</td>
                            <td class="text-right">1200.00</td>
                            <td colspan="2">Cash in Hand</td>
                        </tr>
                        <tr>
                            <td class="text-center icon-row">2</td>
                            <td colspan="2">
                                <select style="font-size: 12px; color:#000">
                                    <option value="">Select Expense Category</option>
                                </select>
                            </td>
                            <td>
                                <textarea class="form-control" placeholder="Details"></textarea>
                            </td>
                            <td>
                                <input type="text" id="amount" class="form-control text-right" placeholder="Amount">
                            </td>
                            <td class="text-right" style="position:relative">
                                <select style="font-size: 12px; color:#000">
                                    <option value="">Select Account</option>
                                </select>
                            </td>
                            <td>
                            	<span class="remove-icon"><i class="fa fa-minus"></i></span>
                                <span class="add-icon"><i class="fa fa-plus"></i></span>
                            </td>
                        </tr>
                    </table>
                </div>
                <div id="total-expense" class="expense-form">
                    <table width="100%" class="table table-hover list">
                        <thead>
                            <tr>
                            	<th colspan="7" class="bg-info padding-8">Customer Payment</th>
                            </tr>
                            <tr class="head">
                                <th width="5%" class="text-center">S.N</th>
                                <th width="10%">Time</th>
                                <th width="15%">Customer Name</th>
                                <th width="18%">Details</th>
                                <th width="12%" class="text-right">Amount</th>
                                <th width="15%">Paid By</th>
                                <th width="5%"></th>
                            </tr>
                        </thead>
                        <tr>
                        	<td class="text-center icon-row">1 </td>
                        	<td>09.35AM</td>
                            <td>Tariq</td>
                            <td>Test</td>
                            <td class="text-right">1200.00</td>
                            <td colspan="2">Cash in Hand</td>
                        </tr>
                        <tr>
                            <td class="text-center icon-row">2 </td>
                            <td colspan="2">
                                <select style="font-size: 12px; color:#000">
                                    <option value="">Select Customer</option>
                                </select>
                            </td>
                            <td>
                                <textarea class="form-control" placeholder="Details"></textarea>
                            </td>
                            <td>
                                <input type="text" id="amount" class="form-control text-right" placeholder="Amount">
                            </td>
                            <td class="text-right" style="position:relative">
                                <select style="font-size: 12px; color:#000">
                                    <option value="">Select Account</option>
                                </select>
                            </td>
                            <td>
                            	<span class="remove-icon"><i class="fa fa-minus"></i></span>
                                <span class="add-icon"><i class="fa fa-plus"></i></span>
                            </td>
                        </tr>
                    </table>
                </div>
                <div id="total-expense" class="expense-form">
                    <table width="100%" class="table table-hover list">
                        <thead>
                            <tr>
                            	<th colspan="7" class="bg-info padding-8">Fund Transfer</th>
                            </tr>
                            <tr class="head">
                                <th width="5%" class="text-center">S.N</th>
                                <th width="10%">Time</th>
                                <th width="15%">From Account</th>
                                <th width="15%">To Account</th>
                                <th width="12%" class="text-right">Amount</th>
                                <th width="18%">Details</th>
                                <th width="5%"></th>
                            </tr>
                        </thead>
                        <tr>
                        	<td class="text-center icon-row">1 </td>
                        	<td>09.35AM</td>
                            <td>Cash in Hand</td>
                            <td>Tariq</td>
                            <td class="text-right">1200.00</td>
                            <td colspan="2">Test</td>
                        </tr>
                        <tr>
                            <td class="text-center icon-row">2 </td>
                            <td colspan="2">
                                <select style="font-size: 12px; color:#000">
                                    <option value="">From Account</option>
                                </select>
                            </td>
                            <td>
                                <select style="font-size: 12px; color:#000">
                                    <option value="">To Account</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" id="amount" class="form-control text-right" placeholder="Amount">
                            </td>
                            <td class="text-right" style="position:relative">
                                <textarea class="form-control" placeholder="Details"></textarea>
                            </td>
                            <td>
                            	<span class="remove-icon"><i class="fa fa-minus"></i></span>
                                <span class="add-icon"><i class="fa fa-plus"></i></span>
                            </td>
                        </tr>
                        <tr>
                        	<td colspan="7" class="text-right"><input type="button" class="btn btn-default btn-l" value="Save"></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include("include/footer.php");?>