<?php

//微信信息配置文件

return[
    'app_id' => '',
    'app_secret' => '',
    'login_url' => 'https://api.weixin.qq.com/sns/jscode2session?'.
        'appid=%s&secret=%s&js_code=%s&grant_type=authorization_code',
    'access_token_url' => 'https://api.weixin.qq.com/cgi-bin/token?'.
        'grant_type=client_credential&appid=%s&secret=%s',
];