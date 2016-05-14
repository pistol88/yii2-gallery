Yii2-gallery
==========
Это модуль был создан, чтобы дать возможность быстро загружать в админке картинки и аттачить их к [CostaRico/yii2-images](https://github.com/CostaRico/yii2-images).

Установка
---------------------------------
Выполнить команду

```
php composer require pistol88/yii2-gallery "*"
```

Или добавить в composer.json

```
"pistol88/yii2-gallery": "*",
```

И выполнить

```
php composer update
```

Миграция от CostaRico/yii2-images

```
php yii migrate/up --migrationPath=@vendor/costa-rico/yii2-images/migrations
```

Подключение и настройка
---------------------------------
В конфигурационный файл приложения добавить модуль yii2images
```php
    'modules' => [
        'yii2images' => [
            'class' => 'pistol88\gallery\Module',
            'imagesStorePath' => dirname(dirname(__DIR__)).'/frontend/web/images/store', //path to origin images
            'imagesCachePath' => dirname(dirname(__DIR__)).'/frontend/web/images/cache', //path to resized copies
            'graphicsLibrary' => 'GD',
            'placeHolderPath' => '@webroot/images/placeHolder.png',
        ],
        //...
    ]
```

Модуль наследует модуль [CostaRico/yii2-images](https://github.com/CostaRico/yii2-images).

К модели, к которой необходимо аттачить загружаемые картинки, добавляем поведение:

```php
    function behaviors()
    {
        return [
            'images' => [
                'class' => 'pistol88\gallery\behaviors\AttachImages',
                'inAttribute' => 'image',
                'sizes' => ['thumb' => '50x50', 'medium' => '300x300', 'big' => '500x500'],
            ],
        ];
    }
```

*inAttribute - название поля модели, где будет храниться PHP serialize (кеш превьюшек), рекомендую типа text
*sizes - перечень размеров, которые будут кешироваться в inAttribute

Использование
---------------------------------
Использовать можно также, как напрямую из [CostaRico/yii2-images](https://github.com/CostaRico/yii2-images)

```php
$images = $model->getImages();
foreach($images as $img) {
    //retun url to full image
    echo $img->getUrl();

    //return url to proportionally resized image by width
    echo $img->getUrl('300x');

    //return url to proportionally resized image by height
    echo $img->getUrl('x300');

    //return url to resized and cropped (center) image by width and height
    echo $img->getUrl('200x300');
}
```

Мой модуль кеширует картинки в поле (для экономии ресурсов), потому рекомендую для вывода картинок использовать методы getThumb('thumb') и getThumbs('thumb'), единственный аргумент - это нужный размер, перечень размеров задается при присоединении поведения атрибутом sizes.

```php
foreach($model->getThumbs('thumb') as $image) {
    echo '<img src="'.$image.'">';
}
```

Виджеты
---------------------------------
Загрузка картинок осуществляется через виджет. Добавьте в _form.php внутри формы CRUDа вашей модели:

<?=\pistol88\gallery\widgets\Gallery::widget(['model' => $model, 'form' => $form, 'inAttribute' => 'image']); ?>

* inAttribute - название поля таблицы, связанной с $model, где необходимо хранить кеш превьюшек
* mode - тип загрузки. gallery - массовая загрузка, single - одиночное поле.
* previewSize - размер превьюшки рядом с полем, по умолчанию '50x50'

Внимание! Христа ради, не забудьте добавить ['options' => ['enctype' => 'multipart/form-data']] для вашей формы.