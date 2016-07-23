Yii2-gallery
==========
Это модуль был создан, чтобы дать возможность быстро загружать в админке картинки, добавлять титульник, описание, альтернативный текст, а также задать положение (чем выше значение тем выше в списке будет изображение).

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

Миграция

```
php yii migrate/up --migrationPath=@vendor/pistol88/yii2-gallery/migrations
```

Подключение и настройка
---------------------------------
В конфигурационный файл приложения добавить модуль gallery
```php
    'modules' => [
        'gallery' => [
            'class' => 'pistol88\gallery\Module',
            'imagesStorePath' => dirname(dirname(__DIR__)).'/frontend/web/images/store', //path to origin images
            'imagesCachePath' => dirname(dirname(__DIR__)).'/frontend/web/images/cache', //path to resized copies
            'graphicsLibrary' => 'GD',
            'placeHolderPath' => '@webroot/images/placeHolder.png',
        ],
        //...
    ]
```

К модели, к которой необходимо аттачить загружаемые картинки, добавляем поведение:

```php
    function behaviors()
    {
        return [
            'images' => [
                'class' => 'pistol88\gallery\behaviors\AttachImages',
                'mode' => 'gallery',
            ],
        ];
    }
```

*mode - тип загрузки. gallery - массовая загрузка, single - одиночное поле.

Использование
---------------------------------
Использовать можно также, как напрямую:

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

    //return alt text to image
    $img->alt

    //return title to image
    $img->title
    
    //return description image
    $img->description
}

Виджеты
---------------------------------
Загрузка картинок осуществляется через виджет. Добавьте в _form.php внутри формы CRUDа вашей модели:

```php
<?=\pistol88\gallery\widgets\Gallery::widget(['model' => $model]); ?>
```