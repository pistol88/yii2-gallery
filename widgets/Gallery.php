<?php
namespace pistol88\gallery\widgets;

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\file\FileInput;

class Gallery extends \yii\base\Widget
{
    public $model = null;
    public $mode = 'gallery';
    public $inAttribute = null;
    public $previewSize = '50x50';

    public function init()
    {
        $view = $this->getView();
        $view->on($view::EVENT_END_BODY, function($event) {
            echo $this->render('modal');
        });

        \pistol88\gallery\assets\GalleryAsset::register($this->getView());
    }

    public function run()
    {
        if($this->model->getGalleryMode() == 'single') {
            if($this->model->hasImage()) {
                $image = $this->model->getImage();
                $img = Html::img($image->getUrl($this->previewSize), ['width' => current(explode('x', $this->previewSize))]);
                $img .= Html::tag('div', Html::a('Удалить', '#', ['data-action' => Url::toRoute(['/gallery/default/delete']), 'class' => 'delete']));
                $imageId = $image->id;
            } else {
                $img = '';
                $imageId = 0;
            }
            $model = $this->model;
            
            return Html::tag('div', 
                $img.
                FileInput::widget([
                'name' => 'galleryFiles',
                'options' => [
                    'accept' => 'image/*', 
                    'multiple' => false,
                    ]
                ]) ,
                [
                    'class' => 'pistol88-gallery-item',  
                    'data-model' => $model::className(), 
                    'data-id' => $this->model->id, 
                    'data-image' => $imageId
                ]
            );
        }
        $elements = $this->model->getImages();

        $cart = Html::ul($elements, ['item' => function($item) {
                    return $this->row($item);
                },
                'class' => 'pistol88-gallery']
            );

        return Html::tag('div',
           $cart .
           '<br style="clear: both;" />' .
            FileInput::widget([
                'name' => 'galleryFiles[]',
                'options' => [
                    'accept' => 'image/*', 
                    'multiple' => true,
                    ]
                ])
        );
    }

    private function row($image)
    {
        if($image instanceof \pistol88\gallery\models\PlaceHolder) {
            return '';
        }

        $delete = Html::a('✖', '#', ['data-action' => Url::toRoute(['/gallery/default/delete']), 'class' => 'delete']);
        $write = Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>', '#', ['data-action' => Url::toRoute(['/gallery/default/modal']), 'class' => 'write']);
        $img = Html::img($image->getUrl('150x150'), ['data-action' => Url::toRoute(['/gallery/default/setmain']), 'width' => 150, 'height' => 150, 'class' => 'thumb']);
        $a = Html::a($img, $image->getUrl());
        $class = 'pistol88-gallery-row';
        if($image->isMain) {
            $class .= ' main';
        }
        $model = $this->model;
        $liParams = ['class' => $class.' pistol88-gallery-item',  'data-model' => $model::className(), 'data-id' => $this->model->id, 'data-image' => $image->id];
        
        return Html::tag('li', $delete.$write.$a, $liParams);
    }
}
