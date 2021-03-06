<?php
$this->breadcrumbs = array('门票管理', '购物车');
?>
<div class="contentpanel">

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">我的购物车</h4>
        </div>
        <style>
            .table-responsive img{
                max-width:100px
            }
            .table-responsive th,.table-responsive td{
                vertical-align:middle!important
            }
            .panel-footer b{
                font-size:22px;
                padding:0 5px;
            }
        </style>
        <div class="table-responsive">
            <table class="table table-bordered mb30" id="take-ticket">
                <thead>
                <tr>
                    <th><button type="button" class="btn btn-success btn-xs" id="all-btn">全选</button></th>
                    <th>门票名称</th>
                    <th>取票人</th>
                    <th>取票人手机号</th>
                    <th>游玩日期</th>
                    <th>门票单价</th>
                    <th>票数</th>
                    <th>小计</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                <?php if(isset($cartList)&&!empty($cartList)): ?>
                	<tr id="ele">
                		<td colspan="9" style="text-align:left">
                            电子票
                		</td>
                	</tr>
                    <?php foreach($cartList as $cart): ?>
                        <tr>
                            <td style="text-align:center">
	                            <input type="checkbox" class="all-btn" value="<?php echo $cart['id']; ?>" data-food="<?php echo $cart['id']; ?>">
                            </td>
                            <td><?php echo $cart['ticket_name'] ?></td>
                            <td>
                                <input type="text" class="form-control" name="name" value="<?php echo $cart['name']; ?>">
                            </td>
                            <td><input type="text" class="form-control" name="phone" value="<?php echo $cart['phone'] ?>"></td>
                            <td><?php echo $cart['date']; ?></td>
                            <td class="text-success">
                                <?php echo number_format($cart['price'],2) ?>
                            </td>
                            <td><input type="text" class="form-control num" name="num" price="<?php echo $cart['price'] ?>" value="<?php echo $cart['num'] ?>"></td>
                            <td class="text-success"><?php echo sprintf('%.2f',$cart['price']*$cart['num']) ?></td>
                            <td>
                                <a class="btn btn-success btn-xs del-btn" data-id="<?php echo $cart['id']; ?>" href="">删除</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif;?>

                <?php if(isset($renwu)&&!empty($renwu)): ?>
                	<tr id="ren">
                		<td colspan="9" style="text-align:left">
                            任务单  <span style="margin-left:15px"class="text-danger">任务单无需支付，生成订单后前往任务单管理进行确认</span>
                		</td>
                	</tr>
                    <?php foreach($renwu as $cart): ?>
                        <tr>
                            <td style="text-align: center">
                                    <input type="checkbox" class="all-btn" value="<?php echo $cart['id']; ?>" data-food="<?php echo $cart['id']; ?>">
                            </td>
                            <td><?php echo $cart['ticket_name'] ?></td>
                            <td>
                                <input type="text" class="form-control" name="name" value="<?php echo $cart['name']; ?>">
                            </td>
                            <td><input type="text" class="form-control" name="phone" value="<?php echo $cart['phone'] ?>"></td>
                            <td><?php echo $cart['date']; ?></td>
                            <td class="text-success">
                                <?php echo number_format($cart['price'],2) ?>
                            </td>
                            <td><input type="text" class="form-control num" name="num" price="<?php echo $cart['price'] ?>" value="<?php echo $cart['num'] ?>"></td>
                            <td class="text-success"><?php echo sprintf('%.2f',$cart['price']*$cart['num']) ?></td>
                            <td>
                                <a class="btn btn-success btn-xs del-btn" data-id="<?php echo $cart['id']; ?>" href="">删除</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif;?>
                <?php if(empty($cartList)&&empty($renwu)):?>
                    <tr class="empty">
                        <td colspan="10" style="text-align: center;">
                            购物车空空如也,点<a href="/ticket/sale">这里</a>购买门票!</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="panel-footer" style="text-align:right" id="take-ticket-footer">
            <span style="margin-right:30px">共计门票:<b class="text-danger">0</b>张</span>
            <span style="margin-right:30px">共计票数:<b class="text-danger">0</b>张</span>
            <span style="margin-right:30px">总金额:<b class="text-danger">0</b>元</span>

            <button class="btn btn-primary" type="submit" disabled>生成订单</button>
        </div>
    </div>
</div><!-- contentpanel -->
<script>
    jQuery(document).ready(function(){
        
        $('.btn-primary').live('click',function(){
            var data = "";
            $('tr[class="selected"]').each(function(){
                if($(this).find('input[type="checkbox"]').val()){
                	data += '{"id":"'+parseInt($(this).find('input[type="checkbox"]').val())+'",';
                }
                if($(this).find('input[name="name"]').val()){
                	data += '"name":"'+$(this).find('input[name="name"]').val()+'",';
                }               
                if($(this).find('input[name="phone"]').val()){
                	data += '"phone":"'+$(this).find('input[name="phone"]').val()+'",';
                }               
                if($(this).find('input[name="num"]').val()){
                	data += '"num":"'+$(this).find('input[name="num"]').val()+'"},';
                }               
                
            });
            data = "["+ data.replace(/,$/gi,"")+"]";
            $.post('/ticket/cart/createOrders/',{data:data},function(data){
                if(data.error)
                    alert(data.error);
                if(data.ids)
                    window.location.href = "/order/payments/method/combine/"+data.ids;
            },'json') ;
        });

        !function(){
            var takeTicketFooter = $('#take-ticket-footer'),
                allBtn = $('#all-btn')

            //全选
            allBtn.click(function(){
                var obj = $(this).parents('table')
                if($(this).text() == '全选'){
                    obj.find('input').prop('checked', true)
                    obj.find('tbody tr[class!="empty"]').addClass('selected')
                    $(this).text('全不选')
                }else{
                    obj.find('input').prop('checked', false)
                    obj.find('tbody tr').removeClass('selected')
                    $(this).text('全选')
                }
                total()
            })

            $('.del-btn').click(function(){
                var obj = $(this);
                var id = obj.attr('data-id');
                if(id!=""&&id!=undefined){
                    $.post('/ticket/cart/delCart/',{ids:id},function(data){
                        if(data.error==0){
                            if(obj.parents('tr').siblings().length<=0){//如果购物车删除后为空
                                var tbodyObj = obj.closest('tbody');
                                obj.parents('tr').remove();
                                tbodyObj.append('<tr class="empty"><td colspan="10" style="text-align: center;">'+
                                '购物车空空如也,点<a href="/ticket/sale">这里</a>购买门票!</td></tr>');
                            }else{
                                obj.parents('tr').remove();
                                var ele = $('#ele').nextAll('tr[id != "ren"]').length;
                                var ren = $('#ren').nextAll().length;
                                if(ele <= 0){
                                	$('#ele').remove();
                                	parent.location.reload();
                                }
                                if(ren <= 0){
                                	$('#ren').remove();
                                	parent.location.reload();
                                }
                            }
                            total()
                        }else{
                            alert(data.msg);
                        }
                    },'json') ;
                $(".all-btn"+attr('data-food')).atrr('checked',false);
                parent.location.reload();
                }else{
                    alert("删除失败!");
                }
                return false
            })

            $('.all-btn').click(function(){
                $(this).parents('tr').toggleClass('selected')
                total()
            })


            $('.num').keyup(function(){
                $(this).val($(this).val()==""?0:$(this).val().replace(/\D/g,''));
                var price = parseFloat(parseInt($(this).val())*parseFloat($(this).attr('price')));
                $(this).closest('td').next('td').html(price.toFixed(2));

                total()
            })


            function total(){
                var tickets,
                    takeTicketNum= 0,
                    total_pay = 0
                tickets = $('#take-ticket tbody').find('.selected').length
                $('tr[class="selected"]').each(function(){
                	if(typeof($(this).find('input[type="checkbox"]').val()) == 'undefined'){
                		tickets = tickets - 1;
                	}     	                
                })
                $('.selected .num').each(function(){
                    takeTicketNum+=parseInt($(this).val())
                    total_pay += parseInt($(this).val()) * parseFloat($(this).attr('price'));
                })
                takeTicketFooter.html('<span style="margin-right:30px">共计门票:<b class="text-danger">'+tickets+
                    '</b>张</span><span style="margin-right:30px">	共计票数:<b class="text-danger">'+
                    takeTicketNum+'</b>张</span><span style="margin-right:30px">总金额:<b class="text-danger">'+
                    total_pay.toFixed(2)+'</b>元</span><button type="submit" class="btn btn-primary" '+
                    (tickets>0?"":"disabled")+'>生成订单</button>')
            }


        }()










        // Tags Input
        jQuery('#tags').tagsInput({width:'auto'});

        // Textarea Autogrow
        jQuery('#autoResizeTA').autogrow();



        // Form Toggles
        jQuery('.toggle').toggles({on: true});


        // Date Picker
        jQuery('#datepicker').datepicker();
        jQuery('#datepicker-inline').datepicker();
        jQuery('#datepicker-multiple').datepicker({
            numberOfMonths: 3,
            showButtonPanel: true
        });

        // Input Masks
        jQuery("#date").mask("99/99/9999");
        jQuery("#phone").mask("(999) 999-9999");
        jQuery("#ssn").mask("999-99-9999");

        // Select2
        jQuery("#select-basic, #select-multi").select2();
        jQuery('.select2').select2({
            minimumResultsForSearch: -1
        });

        function format(item) {
            return '<i class="fa ' + ((item.element[0].getAttribute('rel') === undefined)?"":item.element[0].getAttribute('rel') ) + ' mr10"></i>' + item.text;
        }

        // This will empty first option in select to enable placeholder
        jQuery('select option:first-child').text('');

        jQuery("#select-templating").select2({
            formatResult: format,
            formatSelection: format,
            escapeMarkup: function(m) { return m; }
        });

        // Color Picker
        if(jQuery('#colorpicker').length > 0) {
            jQuery('#colorSelector').ColorPicker({
                onShow: function (colpkr) {
                    jQuery(colpkr).fadeIn(500);
                    return false;
                },
                onHide: function (colpkr) {
                    jQuery(colpkr).fadeOut(500);
                    return false;
                },
                onChange: function (hsb, hex, rgb) {
                    jQuery('#colorSelector span').css('backgroundColor', '#' + hex);
                    jQuery('#colorpicker').val('#'+hex);
                }
            });
        }

        // Color Picker Flat Mode
        jQuery('#colorpickerholder').ColorPicker({
            flat: true,
            onChange: function (hsb, hex, rgb) {
                jQuery('#colorpicker3').val('#'+hex);
            }
        });


    });

</script>
