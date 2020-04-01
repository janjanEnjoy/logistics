### 测试方法
* 将config上的 env函数打开注释
```php
function env($a,$b=null){
    return $b;
}
```
* 将主控制器的配置文件读取方式切换为include模式

* 执行命令
php example.php
