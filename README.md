# Xiaomi OAuth2 Provider for Laravel Socialite

1.安装

    composer require yusureabc/xiaomisocialite

2.添加 Service Provider    **文件 config/app.php**
```php
'providers' => [
    // Laravel\Socialite\SocialiteServiceProvider::class,
    SocialiteProviders\Manager\ServiceProvider::class,
],
```

3.添加 Facades Aliase    **文件 config/app.php**
```php
'aliases' => [
    'Socialite' => Laravel\Socialite\Facades\Socialite::class,
],
```

4.添加事件处理器    **文件 app/Providers/EventServiceProvider.php**
```php
protected $listen = [
    'SocialiteProviders\Manager\SocialiteWasCalled' => [
        'Yusureabc\XiaomiSocialite\XiaomiExtendSocialite@handle',
    ],
];
```

5.配置    **文件 .env**
```php
// XiaoMi oauth
XIAOMI_KEY=2882303761517596140
XIAOMI_SECRET=XXXXXXXXXXXXXXXXXXXXXXXXXXXX
XIAOMI_REDIRECT_URI=http://xxx.xxx.com/auth/callback?driver=xiaomi
```