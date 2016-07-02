<?php
return array(
    //加载网站设置配置文件
    'LOAD_EXT_CONFIG' => 'country',
    //显示页面调试TRACE
    'SHOW_PAGE_TRACE' => false,
    'URL_CASE_INSENSITIVE' => true,

    //默认访问控制器
    'DEFAULT_CONTROLLER' => 'Index',

    //数据库连接信息
    'DB_HOST' => '127.0.0.1',
    'DB_TYPE' => 'mysql',
    'DB_USER' => 'fzstore',
    'DB_PWD' => 'fzstore123',
    'DB_PORT' => '3306',
    'DB_NAME' => 'fzstore',
    'DB_PREFIX' => 'fz_',

    //设置模板标识符
    'TMPL_L_DELIM' => '<{',
    'TMPL_R_DELIM' => '}>',

    //图片路径
    'imgHost' => 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'] . '/../upload/',


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

    //比赛结果
    'MatchResult' => array(
        '0' => '未开始',
        '1' => '主胜',
        '2' => '客胜',
        '3' => '平局',
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
        '0' => '待揭晓',
        '1' => '失利',
        '2' => '获胜',
    ),

);