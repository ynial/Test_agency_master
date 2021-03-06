<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h4 class="modal-title">新增子景点</h4>
        </div>
        <form action="#" id="form">
            <div class="modal-body">
                <div class="form-group">
                    <label class="col-sm-2 control-label">景点名称:</label>
                    <div class="col-sm-10">
                        <input maxlength="100" type="text" tag="景点名称" name="name" class="validate[required]  form-control" id="name">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">介绍:</label>
                    <div class="col-sm-10">
                        <textarea maxlength="255" rows="5" tag="介绍" placeholder="" class="validate[required] form-control" style="word-break: break-all; word-wrap:break-word;" name="description" id="description"></textarea>
                    </div>
                </div>
            </div>
      
            <div class="modal-footer">
                <button class="btn btn-success" type="button" id="buttonsub">保存</button>
            </div>
        </form>
    </div>
</div>

<script type="text/javascript">
    //提示设置
         $('#form').validationEngine({
            autoHidePrompt: false,
            scroll: false,
            autoHideDelay: 3000,
            maxErrorsPerField: 1,
            showOneMessage: true
        });
    $("#buttonsub").click(function(){
        if ($('#form').validationEngine('validate') !== true) {
                return false;
        }
        var name =$('#name').val();
        var description =$('#description').val();
        if(name !='' && description !=''){
           $.post('/scenic/scenic/add/', {'name': $('#name').val(), "description": $('#description').val(),'landscape_id':$('#landscape_id').val()}, function(data) {
                if (data.error) {
                    var tmp_errors = '';
                    $.each(data.error, function(i, n) {
                        tmp_errors += n;
                    });
                    var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button><i class="icon-warning-sign"></i>' + tmp_errors + '</div>';
                    $('#verify_return').html(warn_msg);
                } else{
                    var succss_msg = '<div class="alert alert-success"><button data-dismiss="alert" class="close" type="button">×</button><strong>操作成功!</strong></div>';
                    $('#verify_return').html(succss_msg);
                    setTimeout(function(){window.location.href= '/#/scenic/scenic/view?id='+$('#landscape_id').val()+'&time='+ Date.parse(new Date());}, '2000');
                }
            }, "json"); 
        }else{
            alert('相关数据不可以为空！');
        }
        return false; 
      });

</script>