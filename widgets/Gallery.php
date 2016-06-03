<?php

namespace pistol88\gallery\widgets;

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\file\FileInput;
use Yii;

class Gallery extends \yii\base\Widget
{
    public $model = null;
    public $form = null;
    public $mode = 'gallery';
    public $inAttribute = 'images';
    public $previewSize = '50x50';

    public function init()
    {
        \pistol88\gallery\assets\GalleryAsset::register($this->getView());
    }

    public function run()
    {
        if($this->model->getGalleryMode() == 'single') {
            if($image = $this->model->getImage()->getUrl($this->previewSize)) {
                $img = Html::img($image, ['width' => current(explode('x', $this->previewSize))]);
            } else {
                $img = '';
            }
            return $img.$this->form->field($this->model, $this->inAttribute)->fileInput();
        }
        $elements = $this->model->getImages();

        $cart = Html::ul($elements, ['item' => function($item) {
                    return $this->row($item);
                },
                'class' => 'pistol88-gallery']
            );

        return Html::tag('div',
           $cart.
           '<br style="clear: both;" />'.
           $this->form->field($this->model, $this->inAttribute.'[]')->widget(FileInput::classname(), ['options' => ['accept' => 'image/*', 'multiple' => true]])
        );
    }

    private function row($image)
    {
        if($image instanceof \rico\yii2images\models\PlaceHolder) {
            return '';
        }

        $delete = Html::a('✖', '#', ['data-action' => Url::toRoute(['/yii2images/default/delete']), 'class' => 'delete']);
        $img = Html::img($image->getUrl('150x150'), ['data-action' => Url::toRoute(['/yii2images/default/setmain']), 'width' => 150, 'height' => 150, 'class' => 'thumb']);
        $a = Html::a($img, $image->getUrl());
        $class = 'pistol88-gallery-row';
        if($image->isMain) {
            $class .= ' main';
        }
        $model = $this->model;
        $liParams = ['class' => $class,  'data-model' => $model::className(), 'data-id' => $this->model->id, 'data-image' => $image->id];
        
        return Html::tag('li', $delete.$a, $liParams);
    }
}
