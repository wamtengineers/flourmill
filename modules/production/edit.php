<?php
if(!defined("APP_START")) die("No Direct Access");
?>
<div class="page-header">
	<h1 class="title">Edit Production</h1>
  	<ol class="breadcrumb">
    	<li class="active">Manage Production</li>
  	</ol>
  	<div class="right">
    	<div class="btn-group" role="group" aria-label="..."> <a href="production_manage.php" class="btn btn-light editproject">Back to List</a> </div>
  	</div>
</div>
<form action="production_manage.php?tab=edit" method="post" enctype="multipart/form-data" name="frmAdd"  class="form-horizontal form-horizontal-left">
	<input type="hidden" name="id" value="<?php echo $id;?>">
    <div class="form-group">
    	<div class="row">
            <div class="col-sm-2 control-label">
                <label class="form-label" for="datetime_added">DateTime <span class="manadatory">*</span></label>
            </div>
            <div class="col-sm-10">
                <input type="text" title="Enter DateTime" value="<?php echo $datetime_added; ?>" name="datetime_added" id="datetime_added" class="form-control date-timepicker" >
            </div>
        </div>
  	</div>
    <div class="form-group">
    	<div class="row">
            <div class="col-sm-2 control-label">
                <label class="form-label">Items</label>
            </div>
            <div class="col-sm-10">
                <div class="panel-body table-responsive">
                    <table class="table table-hover list">
                        <thead>
                            <tr>
                                <th width="5%" class="text-center">S.no</th>
                                <th width="20%">Item</th>
                                <th class="text-right">Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
						$sql="select a.*,b.title as category from items a inner join item_category b on a.item_category_id = b.id where a.status=1 and type=0 order by b.sortorder, a.sortorder";
						$rs=show_page($rows, $pageNum, $sql);
						if(numrows($rs)>0){
							$sn=1;
							while($r=dofetch($rs)){
								?>
                                <tr>
                                    <td class="text-center serial_number"><?php echo $sn;?></td>
                                    <td><?php echo unslash( $r[ "title" ] )." ".unslash( $r[ "category" ] );?></td>
                                    <td class="text-right"><input type="text" class="quantity" name="quantities[<?php echo $r["id"]?>]" id="quantity_<?php echo $r["id"]?>" value="<?php echo isset( $quantities[$r["id"]] )?$quantities[$r["id"]]:0;?>" /></td> 
                                </tr>   
                        		<?php 
                    		$sn++;
                			}
						}
                		?>  
                        </tbody>
                    </table>
                </div>
            </div>
    	</div>
    </div>
  	<div class="form-group">
    	<div class="row">
            <div class="col-sm-2 control-label">
                <label for="company" class="form-label"></label>
            </div>
            <div class="col-sm-10">
                <input type="submit" value="UPDATE" class="btn btn-default btn-l" name="production_edit" title="Update Record" />
            </div>
        </div>
  	</div>
</form>