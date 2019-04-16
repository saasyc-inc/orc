# 安装
- 在项目`composer.json`中添加
```json
"repositories": [
        {
            "type": "vcs",
            "url": "https://code.lrwanche.com/yiche/ocr"
        }
    ]
```
- 执行安装
 
`composer require yiche/ocr:dev-master`
- 后期更新

`composer update yiche/ocr`

- 安装项目数据库

`php artisan yiche:ocr-install`

- 发布扩展包

`php artisan vendor:publish --provider="Yiche\Ocr\OcrServiceProvider"`

# 使用
- 默认安装好之后会有个路由

- `http://xxx/yiche/ocr/idcard`，身份证识别接口
