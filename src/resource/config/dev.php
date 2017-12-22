<?php 
return array (
  'timezone' => 'PRC',
  'use-language' => true,
  'view.compiled' => 'resource/blade-cache',
  'view.paths' => 'resource/views',
  'view.version' => 'primary',
  'view.namespaces' => 
  array (
    'admin' => 'resource/views/admin/primary/html-creator',
  ),
  'response-console-log' => true,
  'response-trace-log' => true,
  'session' => 
  array (
    'auto-start' => true,
  ),
  'cookie' => 
  array (
    'expire' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => false,
    'httponly' => '',
    'setcookie' => true,
  ),
  'add-config' => 
  array (
    0 => 'dev/db/config',
    1 => 'dev/app',
  ),
  'add-config-name' => 
  array (
    0 => 'dev/client-config',
    1 => 'mail',
  ),
  'modules' => 
  array (
    0 => 'Admin',
    1 => 'Client',
  ),
  'domain-deploy' => true,
  'domain-deploy-config' => 
  array (
    'dev.lxh.com' => 'admin',
    'www.lxh.com' => 'client',
    '119.23.229.90' => 'client',
  ),
  'record-error-info-level' => 
  array (
    0 => 1,
    1 => 4096,
    2 => 2,
    3 => 4,
    4 => 8,
    5 => 2048,
    6 => 8192,
    7 => 16,
    8 => 32,
    9 => 64,
    10 => 128,
    11 => 256,
    12 => 512,
    13 => 1024,
    14 => 16384,
  ),
  'events' => 
  array (
    'route.dispatch.after' => 
    array (
    ),
    'response.send.after' => 
    array (
      0 => 'track',
    ),
    'exception.report' => 
    array (
    ),
    'route.dispatch.before' => 
    array (
    ),
  ),
  'middlewares' => 
  array (
    '*' => 
    array (
    ),
    'Admin' => 
    array (
    ),
  ),
  'logger' => 
  array (
    'primary' => 
    array (
      'path' => '../data/logs/record.log',
      'handlers' => 
      array (
        0 => 
        array (
          'handler' => 'DaysFileHandler',
          'formatter' => 'TextFormatter',
          'level' => '100',
        ),
      ),
      'maxFiles' => 180,
      'filenameDateFormat' => 'Y-m-d',
    ),
    'exception' => 
    array (
      'path' => '../data/logs/record.log',
      'handlers' => 
      array (
        0 => 
        array (
          'handler' => 'DaysFileHandler',
          'formatter' => 'TextFormatter',
          'level' => '100',
        ),
      ),
      'maxFiles' => 180,
      'filenameDateFormat' => 'Y-m-d',
    ),
    'pdo' => 
    array (
      'path' => '../data/logs/record.log',
      'handlers' => 
      array (
        0 => 
        array (
          'handler' => 'DaysFileHandler',
          'formatter' => 'TextFormatter',
          'level' => '100',
        ),
      ),
      'maxFiles' => 180,
      'filenameDateFormat' => 'Y-m-d',
    ),
    'redis' => 
    array (
      'path' => '../data/logs/record.log',
      'handlers' => 
      array (
        0 => 
        array (
          'handler' => 'DaysFileHandler',
          'formatter' => 'TextFormatter',
          'level' => '100',
        ),
      ),
      'maxFiles' => 180,
      'filenameDateFormat' => 'Y-m-d',
    ),
  ),
  'db' => 
  array (
    'primary' => 
    array (
      'usepool' => false,
      'type' => 'mysql',
      'host' => 'localhost',
      'port' => 3306,
      'user' => 'root',
      'pwd' => '',
      'charset' => 'utf8',
      'name' => 'lxh',
    ),
    'local' => 
    array (
      'usepool' => false,
      'type' => 'mysql',
      'host' => 'localhost',
      'port' => 3306,
      'user' => 'root',
      'pwd' => '',
      'charset' => 'utf8',
      'name' => 'lxh',
    ),
    'she' => 
    array (
      'usepool' => false,
      'type' => 'mysql',
      'host' => '192.168.0.207',
      'port' => 3306,
      'user' => 'suitshe',
      'pwd' => 'suitshe',
      'charset' => 'utf8',
      'name' => 'suitshe',
    ),
  ),
  'easy-wechat' => 
  array (
    'debug' => true,
    'app_id' => 'your-app-id',
    'secret' => 'your-app-secret',
    'token' => 'your-token',
    'aes_key' => '',
    'log' => 
    array (
      'level' => 'debug',
      'permission' => 511,
      'file' => '/tmp/easywechat.log',
    ),
    'oauth' => 
    array (
      'scopes' => 
      array (
        0 => 'snsapi_userinfo',
      ),
      'callback' => '/examples/oauth_callback.php',
    ),
    'payment' => 
    array (
      'merchant_id' => 'your-mch-id',
      'key' => 'key-for-signature',
      'cert_path' => 'path/to/your/cert.pem',
      'key_path' => 'path/to/your/key',
    ),
    'guzzle' => 
    array (
      'timeout' => 3.0,
    ),
  ),
  'client-config' => 
  array (
    'resource-server' => '',
    'resource-version' => 'primary',
    'sea-config' => 
    array (
      'paths' => 
      array (
        's' => '/assets/primary/Admin',
        'lib' => '/assets/primary/Admin/lib',
        'api' => '/api/js',
        'view' => '/assets/primary/Admin/view',
        'module' => '/assets/primary/Admin/view/module',
        'css' => '/assets/primary/Admin/css',
      ),
      'alias' => 
      array (
        'parsley' => 'lib/plugins/parsleyjs/dist/parsley.min',
        'container' => 'lib/js/container',
        'toastr' => 'lib/plugins/toastr/toastr.min',
        'core' => 'lib/js/jquery.core',
        'blade' => 'lib/js/blade',
        'validate' => 'lib/js/validate',
        'router' => 'lib/js/router',
      ),
    ),
    'public-css' => 
    array (
      0 => 's/css/responsive.css',
    ),
    'public-js' => 
    array (
      0 => 'validate',
      1 => 'container',
      2 => 'core',
      3 => 'lib/js/jquery.app',
    ),
  ),
  'mail' => 
  array (
    'driver' => 'smtp',
    'host' => 'smtp.qq.com',
    'port' => 587,
    'from' => 
    array (
      'address' => '841324345@qq.com',
      'name' => '江',
    ),
    'encryption' => 'tls',
    'username' => '841324345@qq.com',
    'password' => 'ucztnsvzapvwbcge',
    'sendmail' => '/usr/sbin/sendmail -bs',
    'markdown' => 
    array (
      'theme' => 'default',
      'paths' => 
      array (
        0 => 'mail/views/vendor/mail',
      ),
    ),
  ),
  'js-version' => 1504001755,
  'css-version' => 1501549539,
  'language' => 'zh',
  'replica-client-config' => 
  array (
    'use-cache' => false,
    'cache-expire' => 259200000,
    'lang-package-expire' => 259200000,
  ),
);
