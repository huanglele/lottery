<?php
return array(
    //加载网站设置配置文件
    'LOAD_EXT_CONFIG' => 'country',
    //显示页面调试TRACE
    'SHOW_PAGE_TRACE' => true,
    'URL_CASE_INSENSITIVE' => true,

    //默认访问控制器
    'DEFAULT_CONTROLLER' => 'Index',

    //数据库连接信息
    'DB_HOST' => '127.0.0.1',
    'DB_TYPE' => 'mysql',
    'DB_USER' => 'lottery',
    'DB_PWD' => 'lottery123',
    'DB_PORT' => '3306',
    'DB_NAME' => 'lottery',
    'DB_PREFIX' => '',

    //设置模板标识符
    'TMPL_L_DELIM' => '<{',
    'TMPL_R_DELIM' => '}>',

    //网站名称
    'SITE_NAME' => '体育竞猜',

    //微信参数
    'Wx' => array(
        'AppID' => 'wxc2b751b051a1c873',
        'AppSecret' => '31b946e6ffab1208e19bed9c2b8c4063',
        'Token' => 'Z60z6Z6Q1aavK30K0GVv460t30bnA606',       //微信Token(令牌)
        'EncodingAESKey' => 'HdJJKSjx0kqcheREd1zYqJnSy4OCcRHeKdJyj2hECSH',//微信消息加解密密钥
//        'key' => '123456789012345678901234567890rz',
//        'mch_id' => '1267553601', //商户号
//        'notify_url' => 'http://' . $_SERVER['HTTP_HOST'] . '/index.php/wechat/notify',
//        'SSLCERT_PATH' => LIB_PATH . "Org/Wxpay/apiclient_cert.pem",
//        'SSLKEY_PATH' => LIB_PATH . "Org/Wxpay/apiclient_key.pem",
//        'CURL_PROXY_HOST' => "0.0.0.0",
//        'CURL_PROXY_PORT' => 0,
//        'REPORT_LEVENL' => 1,
    ),

    'Wechat' => array(
        'welcome' => '点击开始开始吧',
    ),


    //文件上传配置
    'UploadConfig' => array(
        'maxSize' => 0, //上传的文件大小限制 (0-不做限制)
        'exts' => array('jpg', 'gif', 'png', 'jpeg'),// 设置附件上传类型
        'autoSub' => true, //自动子目录保存文件
        'subName' => array('date', 'Ymd'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
        'rootPath' => './upload/', //保存根路径
        'savePath' => '', //保存路径
        'saveName' => array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
        'saveExt' => '', //文件保存后缀，空则使用原后缀
        'replace' => false, //存在同名是否覆盖
        'hash' => true, //是否生成hash编码
        'callback' => false, //检测文件是否存在回调，如果存在返回文件信息数组
        'driver' => '', // 文件上传驱动
    ),

    //跳转模板
    'TMPL_ACTION_SUCCESS' => 'Public:dispatch_jump',
    'TMPL_ACTION_ERROR' => 'Public:dispatch_jump',


    //比赛结果, MatchRecord
    'MatchResult' => array(
        '1' => '下注中',
        '2' => '待揭晓',
        '3' => '主胜',
        '4' => '平局',
        '5' => '客胜',
    ),

    //比赛下注项
    'MatchRecordOption' => array(
        'win' => 3,
        'draw' => 4,
        'lose' => 5,
    ),

    //球队类型 应该和比赛类型 保持一致
    'TeamType' => array(
        '1' => '篮球队',
        '2' => '足球队',
    ),

    //比赛类型
    'MatchType' => array(
        '1' => '篮球',
        '2' => '足球',
    ),

    'UserSex' => array(
        '0' => '保密',
        '1' => '男',
        '2' => '女',
    ),

    //用户是否关注平台
    'UserSub' => array(
        '0' => '未关注',
        '1' => '已关注',
    ),

    //积分变换原因
    'CoinType' => array(
        '0' => '注册',
        '1' => '签到',
        '2' => '猜中',
        '3' => '奖励',

        '-1' => '下注',
        '-2' => '兑换',
        '-3' => '扣除',
    ),

    //下注结果
    'RecordStatus' => array(
        '1' => '待揭晓',
        '2' => '失利',
        '3' => '获胜',
    ),

    //礼物状态
    'GiftStatus' => array(
        '1' => '上架',
        '2' => '下换'
    ),

);