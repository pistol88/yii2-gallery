<?php
namespace pistol88\gallery\widgets;

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\file\FileInput;

class Gallery extends \yii\base\Widget
{
    public $model = null;
    public $previewSize = '140x140';
    public $fileInputPluginLoading = true;
    public $fileInputPluginOptions = [];
    public $label = 'Изображение';
 
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
        $model = $this->model;
        $params = [];
        $img = '';
        $label = '<label class="control-label">'. $this->label .'</label>';
        $cart = '';
        
        if($model->getGalleryMode() == 'single') {
            if($model->hasImage()) {
                $image = $this->model->getImage();
                $img = $this->getImagePreview($image);
                $params = $this->getParams($image->id);

            }

            return $label . '<br style="clear: both;" />' . Html::tag('div', $img, $params) . '<br style="clear: both;" />' . $this->getFileInput();
        }

        if (  $this->model->hasImage() ){
            $elements = $this->model->getImages();
            $cart = Html::ul(
                $elements,
                [
                    'item' => function($item) {
                        return $this->row($item);
                    },
                    'class' => 'pistol88-gallery'
                ]);
        }

        return Html::tag( 'div', $label . $cart . '<br style="clear: both;" />' . $this->getFileInput() );
    }

    private function row($image)
    {
        if($image instanceof \pistol88\gallery\models\PlaceHolder) {
            return '';
        }

        $class = ' pistol88-gallery-row';

        if($image->isMain) {
            $class .= ' main';
        }

        $liParams = $this->getParams((string)$image->_id);
        $liParams['class'] .=  $class;

        return Html::tag('li', $this->getImagePreview($image), $liParams);
    }

    private function getFileInput()
    {
        return FileInput::widget([
            'name' => $this->model->getInputName() . '[]',
            'options' => [
                'accept' => 'image/*',
                'multiple' => $this->model->getGalleryMode() == 'gallery',
            ],
            'pluginOptions' => $this->fileInputPluginOptions,
            'pluginLoading' => $this->fileInputPluginLoading
        ]);
    }

    private function getParams($id)
    {
        $model = $this->model;

        return  [
            'class' => 'pistol88-gallery-item',
            'data-model' => $model::className(),
            'data-id' => (string)$model->_id,
            'data-image' => $id
        ];
    }

    private function getImagePreview($image)
    {
        $size = (explode('x', $this->previewSize));

        $delete = Html::a('✖', '#', ['data-action' => Url::toRoute(['/gallery/default/delete']), 'class' => 'delete']);
        $write = Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>', '#', ['data-action' => Url::toRoute(['/gallery/default/modal']), 'class' => 'write']);
        $img = Html::img($image->getUrl($this->previewSize), ['data-action' => Url::toRoute(['/gallery/default/setmain']), 'width' => $size[0], 'height' => $size[1], 'class' => 'thumb']);
        $a = Html::a($img, $image->getUrl());

        return $delete.$write.$a;
    }
}
