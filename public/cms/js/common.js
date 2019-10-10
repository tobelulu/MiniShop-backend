window.base={
    g_restUrl:'http://z.cn/api/v1/',
    // g_restUrl:'https://shop.houdeyunchuang.com/index.php/api/v1/',

    getData:function(params){
        if(!params.hasOwnProperty('type')){
            params.type ='get';
        }
        if(!params.hasOwnProperty('cache')){
            params.cache = true;
        }
        if(!params.hasOwnProperty('processData')){
            params.processData = true;
        }
        if(!params.hasOwnProperty('contentType')){
            params.contentType ='application/x-www-form-unlencoded';
        }
        if(!params.hasOwnProperty('data')){
            params.data = '';
        }
        var that=this;
        $.ajax({
            type:params.type,
            url:this.g_restUrl+params.url,
            data:params.data,
            cache:params.cache,
            processData:params.processData,
            contentType:params.contentType,
            beforeSend: function (XMLHttpRequest) {
                if (params.tokenFlag) {
                    XMLHttpRequest.setRequestHeader('token', that.getLocalStorage('token'));
                }
            },
            success:function(res){
                params.sCallback && params.sCallback(res);
            },
            error:function(res){

                params.eCallback && params.eCallback(res);
            }
        });
    },

    setLocalStorage:function(key,val){
        var exp=new Date().getTime()+2*24*60*60*100;  //令牌过期时间
        var obj={
            val:val,
            exp:exp
        };
        localStorage.setItem(key,JSON.stringify(obj));
    },

    getLocalStorage:function(key){
        var info= localStorage.getItem(key);
        if(info) {
            info = JSON.parse(info);
            if (info.exp > new Date().getTime()) {
                return info.val;
            }
            else{
                this.deleteLocalStorage('token');
            }
        }
        return '';
    },

    deleteLocalStorage:function(key){
        return localStorage.removeItem(key);
    },

}
