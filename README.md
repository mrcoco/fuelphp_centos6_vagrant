# Laplace

## 初期設定

### vagrant-boxをダウンロード

```
vagrant box add centos6.5 https://github.com/2creatives/vagrant-centos/releases/download/v6.5.3/centos65-x86_64-20140116.box
```

### chef-soloを利用するためプラグインをインストールする

```
vagrant plugin install vagrant-omnibus
```

### vagrantの起動

```
vagrant up
```

## ミドルウェア

```
php     => 5.5.26
mysql   => 5.6.25
apache  => 2.2.15
```

### PHPフレームワーク

[fuelphp](http://fuelphp.com/docs/) `1.7.3`

## Mysql接続情報

**user**: `laplace`
<br>
**password**: `laplace`
<br>
**database**: `laplace_dev`

## apache設定

```
<VirtualHost *:80>
    DocumentRoot /var/www/vagrant/fuelphp/public
    <Directory /var/www/vagrant/fuelphp/public>
        Options All
        AllowOverride All
        Order allow,deny
        Allow from all
   </Directory>
</VirtualHost>
```

## 検索を持った管理コンソールの作成

### Model の作成

```
$ oil generate admin -s post title:string[255] content:text img:string[255] category:string[255] view:int
```

### Search Controller の継承

`app/classes/controller/admin/post.php`

```
class Controller_Admin_Post extends Controller_Admin_Search
```

### Search Controller の設定

```
// 検索対象となるカラム
$search_for = 'title';
$filters = array(
    // label => column_name
    'category' => 'category',
);
$this->search_index('Post', '\Model_Post','admin/category', $search_for, $filters);
```

### Model_Base の継承

`app/model/post.php`

```
class Model_Post extends Model_Base
```

### Pagenation の route 設定

`app/config/route.php`

```
'admin/controller_name/(:num)'=> 'admin/controller_name/index/$1',
```