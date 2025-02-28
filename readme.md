# xhgui-header 中文版 精简依赖版
- 目标兼容php5 php7 php8
## 使用方式 
### 设置环境变量
- XHGUI_CONFIG_DEBUG 
  - 开启错误模式
- XHGUI_CONFIG_FILTER_PATH
  - 过滤某些路径,例如  111/11.html,222/22.html
- XHGUI_CONFIG_SHOULD_RUN
  - 是否开启
- XHGUI_CONFIG_EXTENSION
  - 使用的扩展 ，不能以点so结尾
    - tideways_xhprof
- XHGUI_CONFIG_SAVER_URL
  - 数据采集地址
- XHGUI_CONFIG_PERCENT
  - 采集百分比
### 添加appdend file

- auto_prepend_file=src/main/header.php
如果要自定义保存器，在auto_prepend_file之前 在auto_prepend_file 一个文件，定义一个全局函数 _XhguiHeader_SimpleUrl($data)
- ```nginx
    location ~ \.php$ {
        fastcgi_pass   php81:9000;
        fastcgi_index  index.php;
        include        fastcgi_params;
        fastcgi_param  PATH_INFO $fastcgi_path_info;
        fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;

        fastcgi_param  PHP_VALUE "auto_prepend_file=/var/www/html/xhgui-header/header.php"; 
    }
```
- ```php.ini
    auto_prepend_file = /var/www/html/xhgui-header/header.php
-
## 使用注意事项

- 项目代码需要跑在php-fpm模式下

- php-fpm 配置示例
```php
; env[XHGUI_CONFIG_DEBUG] = $XHGUI_CONFIG_DEBUG
; env[XHGUI_CONFIG_SHOULD_RUN] = $XHGUI_CONFIG_SHOULD_RUN
; env[XHGUI_CONFIG_EXTENSION] = $XHGUI_CONFIG_EXTENSION
; env[XHGUI_CONFIG_PERCENT] = $XHGUI_CONFIG_PERCENT
; env[XHGUI_CONFIG_SAVER_URL] = $XHGUI_CONFIG_SAVER_URL

env[XHGUI_CONFIG_DEBUG] = 1
env[XHGUI_CONFIG_SHOULD_RUN] = 1
env[XHGUI_CONFIG_EXTENSION] = tideways_xhprof
env[XHGUI_CONFIG_PERCENT] =  100
env[XHGUI_CONFIG_SAVER_URL] =   http://web-xhgui.dnmp-plus.orb.local/saver.php     
```
```nginx
fastcgi_param  XHGUI_CONFIG_DEBUG 1 
fastcgi_param  XHGUI_CONFIG_SHOULD_RUN 1 
fastcgi_param  XHGUI_CONFIG_EXTENSION 1 
fastcgi_param  XHGUI_CONFIG_PERCENT 1 
fastcgi_param  XHGUI_CONFIG_SAVER_URL http://web-xhgui.dnmp-plus.orb.local/saver.php     

···

```docker-compose
# 注意项目 需要销毁容器 重新创建
        restart: always
        environment:
        - XHGUI_CONFIG_DEBUG=0
        - XHGUI_CONFIG_SHOULD_RUN=1
        - XHGUI_CONFIG_EXTENSION=tideways_xhprof
        - XHGUI_CONFIG_PERCENT=100      
        - XHGUI_CONFIG_SAVER_URL=http://web-xhgui.dnmp-plus.orb.local/saver.php     
···
```php
<?php

$data = json_decode(file_get_contents('php://input'), true);

require dirname(__DIR__) . '/src/bootstrap.php';

$dir = dirname(__DIR__);
require_once $dir . '/src/Xhgui/Config.php';
Xhgui_Config::load($dir . '/config/config.default.php');
if (file_exists($dir . '/config/config.php')) {
    Xhgui_Config::load($dir . '/config/config.php');
}
unset($dir);

try {
            if(isset($data['meta']) && Xhgui_Config::read('save.handler') === 'mongodb'){
                $data['meta']['request_ts'] =     new MongoDate($data['meta']['request_ts']);
                $data['meta']['request_ts_micro'] =     new MongoDate($data['meta']['request_ts_micro']);
            }
            $config = Xhgui_Config::all();
            $config += array('db.options' => array());
            $saver = Xhgui_Saver::factory($config);
            $saver->save($data);
            echo json_encode( $data);
} catch (Exception $e) {
    error_log('xhgui - ' . $e->getMessage());
}

```
