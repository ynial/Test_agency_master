<?php
$this->breadcrumbs = array('分销商', '合作记录');
?>

<div class="contentpanel">

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">合作记录</h4>
                </div><!-- panel-heading -->
                        <div style="margin:10px;width:600px">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th>分销商名称</th>
                                        <th>合作时间</th>
                                        <th>解除合作时间</th>
                                    </tr>
                                    <?php if(isset($lists)): foreach ($lists as $list):?>
                                    <tr>
                                        <td><?php echo $list['agency_name']?></td>
                                        <td><?php echo date('y年m月d日',$list['created_time'])?></td>
                                        <td><?php if($list['is_bind'] == 1):?>未解除绑定<?php else:?><?php echo date('y年m月d日',$list['delete_time']) ?><?php endif;?></td>
                                    </tr>
                                <?php endforeach;?>
                            <?php else:?>
                            	<tr><td colspan="3">暂无合作的分销商</td></tr>
                            <?php endif;?>
                                </tbody>
                            </table>
                       			
					                <div class="panel-footer pagenumQu" style="height:50px;width:600px;text-align:right;border:1px solid #ddd;border-top:0" <?php if(empty($lists)){ echo 'hidden';}?>>
										<a href="/agency/manager/"  style="margin-top:-5px;float:left" class="btn btn-default">返回</a>
										<div style="margin-top:-15px">
										<?php
										if (isset($lists)) {
											$this->widget('CLinkPager', array(
											'cssFile' => '',
											'header' => '',
											'prevPageLabel' => '上一页',
											'nextPageLabel' => '下一页',
											'firstPageLabel' => '',
											'lastPageLabel' => '',
											'pages' => $pages,
											'maxButtonCount' => 5, //分页数量
										));
									}
									?>
										</div>
									</div>
								
                        </div>


                    </div><!-- panel-body --> 

            </div><!-- panel -->
        </div><!-- col-md-6 -->
    </div><!-- row -->
</div><!-- contentpanel -->
<script>
	
</script>
