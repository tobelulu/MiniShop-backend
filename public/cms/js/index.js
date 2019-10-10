
$(function () {

    if (!window.base.getLocalStorage('token')) {
        window.location.href = 'login.html';
    }

    var orderPageIndex = 1,           //订单分页
        productsPageIndex = 1,        //商品分页
        orderMoreDataFlag = true,     //是否有更多订单
        productMoreDataFlag = true;   //是否有更多产品
    getOrders(orderPageIndex);

    /*
    * 获取订单列表(分页)
    * params:
    * orderPageIndex - {int} 分页下表  1开始
    */

    function getOrders(orderPageIndex) {
        var params = {
            url: 'order/paginate',
            data: {page: orderPageIndex, size: 20},
            tokenFlag: true,
            sCallback: function (res) {
                var str = getOrderHtmlStr(res);
                $('#order-table').append(str);
            }
        };
        window.base.getData(params);
    }

    /*拼接html字符串(订单列表)*/
    function getOrderHtmlStr(res) {
        var data = res.data;
        if (data) {
            var len = data.length,
                str = '', item;
            if (len > 0) {
                for (var i = 0; i < len; i++) {
                    item = data[i];
                    str += '<tr>' +
                        '<td>' + item.order_no + '</td>' +
                        '<td>' + getSnapItem(item.snap_items) + '</td>' +
                        '<td>' + item.total_count + '</td>' +
                        '<td>￥' + item.total_price + '</td>' +
                        '<td>' + getOrderStatus(item.status) + '</td>' +
                        '<td>' + item.create_time + '</td>' +
                        '<td>' + getSnapAddress(item.snap_address) + '</td>' +
                        '<td data-id="' + item.id + '">'+getExpressNumber(item.status,item.express_number)+'</td>'+
                        '<td data-id="' + item.id + '">' + getBtns(item.status) + '</td>' +
                        '</tr>';
                }
            } else {
                orderMoreDataFlag = false;
                ctrlLoadMoreBtn();
            }
            return str;
        }
        return '';
    }

    /*根据订单状态获得标志*/
    function getOrderStatus(status) {
        var arr = [{
            cName: 'unpay',
            txt: '未付款'
        }, {
            cName: 'payed',
            txt: '已付款'
        }, {
            cName: 'done',
            txt: '已发货'
        }, {
            cName: 'unstock',
            txt: '缺货'
        }];
        return '<span class="order-status-txt ' + arr[status - 1].cName + '">' + arr[status - 1].txt + '</span>';
    }

    /*根据订单状态获得 订单操作按钮*/
    function getBtns(status) {
        var arr = [{
            cName: 'done',
            txt: '发货'
        }, {
            cName: 'unstock',
            txt: '缺货'
        }];
        if (status == 2 || status == 4) {
            var index = 0;
            // if (status == 4) {
            //     index = 1;
            // }
            return '<span class="order-btn ' + arr[index].cName + '">' + arr[index].txt + '</span>';
        } else {
            return '';
        }
    }

    //根据订单状态，获得快递单号或者快递单号输入框
    function getExpressNumber(status,expressNumber) {
        if(status ==2 || status ==4){
            return '<input type="text" autocomplete="off">';
        }
        else if(status == 3){
            return expressNumber;
        }
        else
            return '';
    }

    //获取下单商品
    function getSnapItem(items) {
        var str='';
        for(var i=0;i<items.length;i++){
            str += items[i].name +'&emsp;&emsp;数量:&nbsp;'+items[i].counts+'<br/>'
        }
        return str;
    }

    //获取收货地址
    function getSnapAddress(address) {
        return address.name+address.mobile+address.province+address.city+address.country+address.detail
    }

    /*控制加载更多按钮的显示(订单列表)*/
    function ctrlLoadMoreBtn() {
        if (!orderMoreDataFlag) {
            $('#load-more-order').hide().next().show();
        }
        else{
            $('#load-more-order').show().next().hide();
        }
    }

    /*加载更多(订单列表)*/
    $(document).on('click', '#load-more-order', function () {
        if (orderMoreDataFlag) {
            orderPageIndex++;
            getOrders(orderPageIndex);
        }
    });
    /*发货*/
    $(document).on('click', '.order-btn.done', function () {
        var $this = $(this),
            $td = $this.closest('td'),
            $tr = $this.closest('tr'),
            id = $td.attr('data-id'),
            expressNumber = $td.prev().children().val(),
            $tips = $('.global-tips'),
            $p = $tips.find('p');

        if(expressNumber==''){
            $p.text('请填写订单号');
            $tips.show().delay(2000).hide(0);
            return
        }

        var params = {
            url: 'order/delivery',
            type: 'put',
            data: {id : id , expressNumber : expressNumber},
            tokenFlag: true,
            sCallback: function (res) {
                if (res.code.toString().indexOf('2') == 0) {
                    $tr.find('.order-status-txt')
                        .removeClass('pay').addClass('done')
                        .text('已发货');
                    $td.prev().children().remove();
                    $td.prev().text(expressNumber);
                    $this.remove();
                    $p.text('操作成功');
                } else {
                    $p.text('操作失败');
                }
                $tips.show().delay(2000).hide(0);
            },
            eCallback: function () {
                $p.text('模板消息发送失败');
                $tips.show().delay(2000).hide(0);
            }
        };
        window.base.getData(params);
    });

    /*退出*/
    $(document).on('click', '#login-out', function () {
        window.base.deleteLocalStorage('token');
        window.location.href = 'login.html';
    });

    /*导航条按钮事件*/
    $(document).on('click', '#order-list', function () {
        orderPageIndex = 1;
        orderMoreDataFlag = true;
        ctrlLoadMoreBtn();
        $('#order-table').empty();
        getOrders(orderPageIndex);
        $('#order-box').show();
        $('#products-box').hide();
        $('#add-product-box').hide();
        $('#edit-category-box').hide();
    });
    $(document).on('click', '#all-products', function () {
        productsPageIndex = 1;
        productMoreDataFlag = true;
        ctrlLoadMoreBtnProduct();
        $('#products-table').empty();
        getProducts(productsPageIndex);
        $('#order-box').hide();
        $('#products-box').show();
        $('#add-product-box').hide();
        $('#edit-category-box').hide();
    });
    $(document).on('click', '#add-product', function () {
        getCategorySelect();
        $('#order-box').hide();
        $('#products-box').hide();
        $('#add-product-box').show();
        $('#edit-category-box').hide();
    });
    $(document).on('click', '#edit-category', function () {
        $('#category-table').empty();
        getCategory();
        $('#order-box').hide();
        $('#products-box').hide();
        $('#add-product-box').hide();
        $('#edit-category-box').show();
    });

    //获取商品列表(分页)
    function getProducts(productsPageIndex) {
        var params = {
            url: 'product/all',
            data: {page: productsPageIndex, size: 15},
            tokenFlag: true,
            sCallback: function (res) {
                var str = getProductsHtmlStr(res);
                $('#products-table').append(str);
            }
        };
        window.base.getData(params);
    }

    // 拼接商品列表字符串
    function getProductsHtmlStr(res) {
        var data = res.data;
        if (data) {
            var len = data.length,
                str = '', item;
            if (len > 0) {
                for (var i = 0; i < len; i++) {
                    item = data[i];
                    str += '<tr>' +
                        '<td class="img-td"><img src="' + item.main_img_url + '"/></td>' +
                        '<td>' + item.name + '</td>' +
                        '<td>￥' + item.price + '</td>' +
                        '<td>' + item.stock + '</td>' +
                        '<td>' + getProductStatus(item.delete_time) + '</td>' +
                        '<td data-id="' + item.id + '">' + getProductBtns(item.delete_time) + '</td>' +
                        '</tr>';
                }
            } else {
                productMoreDataFlag = false;
                ctrlLoadMoreBtnProduct();
            }
            return str;
        }

        return '';
    }

    /*根据订单状态获得标志*/
    function getProductStatus(status) {
        if(status == null){
            return '<span class="product-status-txt on-sale">上架</span>';
        }
        else {
            return '<span class="product-status-txt not-sale">下架</span>';
        }
    }

    //设置商品操作按钮
    function getProductBtns(status) {
        var arr = [{
            cName: 'on-sale',
            txt: '上架'
        }, {
            cName: 'not-sale',
            txt: '下架'
        },{
            cName: 'delete',
            txt: '删除'
        }];
        if (status == null) {
            var index = 1;
        } else {
            var index = 0;
        }
        return '<span class="product-btn ' + arr[index].cName + '">' + arr[index].txt + '</span>'+'&nbsp;'+
            '<span class="product-btn ' + arr[2].cName + '">' + arr[2].txt + '</span>';
    }

    //下架商品
    $(document).on('click', '.product-btn.not-sale', function () {
        var $this = $(this),
            $td = $this.closest('td'),
            $tr = $this.closest('tr'),
            id = $td.attr('data-id'),
            $tips = $('.global-tips'),
            $p = $tips.find('p');
        var params = {
            url: 'product/delete',
            type: 'put',
            data: {id: id},
            tokenFlag: true,
            sCallback: function (res) {
                if (res.code.toString().indexOf('2') == 0) {
                    $tr.find('.product-status-txt')
                        .removeClass('on-sale').addClass('not-sale')
                        .text('下架');
                    $this.replaceWith('<span class="product-btn on-sale">上架</span>');
                    $p.text('操作成功');
                } else {
                    $p.text('操作失败');
                }
                $tips.show().delay(2000).hide(0);
            },
            eCallback: function () {
                $p.text('操作失败');
                $tips.show().delay(2000).hide(0);
            }
        };
        window.base.getData(params);
    });

    //上架商品
    $(document).on('click', '.product-btn.on-sale', function (){
        var $tips = $('.global-tips'),
            $p = $tips.find('p');
        $p.text('暂未开放此功能');
        $tips.show().delay(2000).hide(0);
    });

    //删除商品
    $(document).on('click', '.product-btn.delete', function (){
        var $tips = $('.global-tips'),
            $p = $tips.find('p');
        $p.text('暂未开放此功能');
        $tips.show().delay(2000).hide(0);
    });

    /*加载更多(商品列表)*/
    $(document).on('click', '#load-more-products', function () {
        if (productMoreDataFlag) {
            productsPageIndex++;
            getProducts(productsPageIndex);
        }
    });

    /*控制加载更多按钮的显示(商品列表)*/
    function ctrlLoadMoreBtnProduct() {
        if (!productMoreDataFlag) {
            $('#load-more-products').hide().next().show();
        }
        else{
            $('#load-more-products').show().next().hide();
        }
    }


    //获取分类下拉列表
    function getCategorySelect(){
        var params = {
            url: 'category/all',
            tokenFlag: true,
            sCallback: function (res) {
                var str = '';
                for (var i = 0; i < res.length; i++){
                    str += '<option value="'+res[i].id+'">'+res[i].name+'</option>';
                }
                $('#category_id').empty();
                $('#category_id').append(str);
            }
        };
        window.base.getData(params);
    }

    //添加商品
    $('#submit').click(function () {
        var form = new FormData();
        var name = $('#name').val(),
            category_id = $('#category_id').val(),
            price = $('#price').val(),
            stock = $('#stock').val(),
            summary = $('#summary').val(),
            property = {},
            main_img = $('#main_img').get(0).files[0],
            product_image = $('#product_image').get(0).files,
            $tips = $('.global-tips'),
            $p = $tips.find('p');
        $('.propertyTableDetail').each(function () {
            var name = $(this).find('.propertyName').val();
            var detail = $(this).find('.propertyDetail').val();
            if(name!=''&&detail!=''){
                property[name]=detail;
            }
        });
        property = JSON.stringify(property);
        if (!checkImg(main_img)||!checkImgs(product_image)||!checkInputText(name)||!checkInputText(price)||!checkInputText(stock)||!checkInputText(property)) {
            return;
        }
        form.append('name', name);
        form.append('category_id', category_id);
        form.append('price', price);
        form.append('stock', stock);
        form.append('summary', summary);
        form.append('property',property);
        form.append('imgLength', product_image.length);
        form.append('main_img', main_img);
        for (var i = 0; i < product_image.length; i++) {
            form.append('product_image' + i, product_image[i]);
        }
        var params = {
            url: 'product/upload',
            data: form,
            type: 'POST',
            tokenFlag: true,
            cache: false,
            processData: false,
            contentType: false,
            sCallback: function (res) {
                if (res.code.toString().indexOf('2') == 0) {
                    $p.text('操作成功');
                } else {
                    $p.text('操作失败');
                }
                $tips.show().delay(2000).hide(0);
            },
            eCallback: function () {
                $p.text('操作失败');
                $tips.show().delay(2000).hide(0);
            }
        };
        window.base.getData(params);
    });

    //判断图片的格式和大小
    function checkImg(image) {
        if (!image) {
            alert("请上传图片");
            return false;
        } else {
            if (image.size > 8388608)//文件小于8M
            {
                alert("图片最大8M");
                return false;
            }
        }
        return true;
    }

    //判断图片的格式和大小
    function checkImgs(images) {
        if (images.length < 1) {
            alert("请上传详情图，最少1张");
            return false;
        } else {
            for (var i = 0; i < images.length; i++) {
                img = images[i];
                if (img.size > 8388608)//文件小于8M
                {
                    alert("每张图片最大8M");
                    return false;
                }
            }
        }
        return true;
    }

    function checkInputText(text){
        if(text == ''||!text||text == '{}'){
            alert("请检查必填项");
            return false;
        }
        else return true;
    }

    //清除选中的图片文件
    $('#clearMain_img').click(function () {
        var file = $('#main_img')[0];
        if (file.outerHTML) {
            file.outerHTML = file.outerHTML;
        } else {
            file.value = '';
        }
    });
    //清除选中的图片文件
    $('#clearProduct_image').click(function () {
        var file = $('#product_image')[0];
        if (file.outerHTML) {
            file.outerHTML = file.outerHTML;
        } else {
            file.value = '';
        }
    });


    //获取分类
    function getCategory() {
        var params = {
            url: 'category/all',
            tokenFlag: true,
            sCallback: function (res) {
                var str = getCategoryHtmlStr(res);
                $('#category-table').append(str);
            }
        };
        window.base.getData(params);
    }

    /*拼接html字符串(分类列表)*/
    function getCategoryHtmlStr(res) {
        var data = res;
        if (data) {
            var len = data.length,
                str = '', item;
            if (len > 0) {
                for (var i = 0; i < len; i++) {
                    item = data[i];
                    str += '<tr>' +
                        '<td>' + item.name + '</td>' +
                        '<td class="img-td"><img src="' + item.img.url + '"/></td>' +
                        '<td data-id="' + item.id + '"><span class="category-btn">编辑</span></td>' +
                        '</tr>';
                }
            }
            str += getAddCategoryHtmlStr();
            return str;
        }
        return '';
    }

    //添加分类的输入框
    function getAddCategoryHtmlStr(){
        return '<tr>' +
            '<td><input type="text" autocomplete="off" class="add-category-name"></td>' +
            '<td><input type="file" accept="image/*" class="add-category-img"></td>' +
            '<td><button type="button" id="add-category-submit">提交</button></td>' +
            '</tr>';
    }

    //编辑分类
    $(document).on('click', '.category-btn', function (){
        var $tips = $('.global-tips'),
            $p = $tips.find('p');
        $p.text('暂未开放此功能');
        $tips.show().delay(2000).hide(0);
    });

    //添加分类
    $(document).on('click', '#add-category-submit', function (){
        var form = new FormData(),
            name = $('.add-category-name').val(),
            img = $('.add-category-img').get(0).files[0],
            $tips = $('.global-tips'),
            $p = $tips.find('p');
        if(!checkImg(img)||!checkInputText(name)){
            return;
        }
        form.append('name',name);
        form.append('img',img);
        var params = {
            url: 'category/add',
            data: form,
            type: 'POST',
            tokenFlag: true,
            cache: false,
            processData: false,
            contentType: false,
            sCallback: function (res) {
                if (res.code.toString().indexOf('2') == 0) {
                    $p.text('操作成功');
                } else {
                    $p.text('操作失败');
                }
                $tips.show().delay(2000).hide(0);
                $('#category-table').empty();
                getCategory();
            },
            eCallback: function () {
                $p.text('操作失败');
                $tips.show().delay(2000).hide(0);
            }
        };
        window.base.getData(params);
    });

});

//添加商品参数输入框
function addProperty() {
    var str = '<tr class="propertyTableDetail">\n' +
                '<td>\n' +
                    '<input type="text" autocomplete="off" class="propertyName">\n' +
                '</td>\n' +
                '<td>\n' +
                    '<input type="text" autocomplete="off" class="propertyDetail">\n' +
                '</td>\n' +
                '<td>\n' +
                    '<button type="button" class="deleteProperty" onclick="deleteProperty(this)">删除</button>\n' +
                '</td>\n' +
            '</tr>';
    $('.propertyBox').append(str);
}
//删除商品参数输入框
function deleteProperty(e) {
    $(e).parent().parent().remove();
}