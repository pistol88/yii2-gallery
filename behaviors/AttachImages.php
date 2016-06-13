<?php
namespace pistol88\gallery\behaviors;

use yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;

class AttachImages extends \rico\yii2images\behaviors\ImageBehave
{
    public $inAttribute = 'images';
    public $uploadsPath = '';
    public $mode = 'gallery';
    public $webUploadsPath = '/uploads';
    public $allowExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    public $sizes = ['thumb' => '50x50', 'preview' => '100x100', 'medium' => '300x300', 'big' => '500x500'];
    private $doResetImages = true; 
    private $lastUploadedImage = false;
    
    public function init()
    {
        if (empty($this->uploadsPath)) {
            $this->uploadsPath = yii::$app->getModule('yii2images')->imagesStorePath;
        }
    }

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_UPDATE => 'setImages',
            ActiveRecord::EVENT_AFTER_INSERT => 'setImages',
        ];
    }
    
    public function getGalleryMode()
    {
        return $this->mode;
    }
    
    public function setImages($event)
    {
        if($this->doResetImages) {
            $userImages = UploadedFile::getInstances($this->owner, $this->inAttribute);

            if ($userImages) {
                foreach ($userImages as $file) {
                    if(in_array(strtolower($file->extension), $this->allowExtensions)) {
                        $file->saveAs("{$this->uploadsPath}/{$file->baseName}.{$file->extension}");
                        if($this->owner->getGalleryMode() == 'single') {
                            foreach($this->owner->getImages() as $image) {
                                $image->delete();
                            }
                        }
                        $attach = $this->owner->attachImage("{$this->uploadsPath}/{$file->baseName}.{$file->extension}");
                        $this->lastUploadedImage = $attach;
                    }
                }
            }

            $this->reSetImages();
        }
        
        return $this;
    }

    public function reSetImages()
    {
        $this->doResetImages = false;
        $images = [];
        $image = false;
        $haveMain = false;

        foreach($this->owner->getImages() as $image) {
            if($image->isMain) {
                $haveMain = true;
            }
            
            $size = ['image' => $image->getUrl()];

            foreach($this->sizes as $name => $wh) {
                $size[$name] = $image->getUrl($wh);
            }
            
            $images[] = $size;
        }

        if($this->owner->getGalleryMode() == 'single' && $this->lastUploadedImage) {
            $haveMain = true;
            $this->lastUploadedImage->setMain(true);
            $this->lastUploadedImage->save();
        } elseif(!$haveMain && $image && !$image instanceof \rico\yii2images\models\PlaceHolder) {
            $image->setMain(true);
            $image->save();
        }
        
        $this->owner->{$this->inAttribute} = serialize($images);
        $this->owner->save(false);
        
        return $this;
    }

    public function getThumbs($size = 'full') {
        if(empty($this->owner->{$this->inAttribute})) {
            return null;
        }
        
        $return = [];
        
        if($images = unserialize($this->owner->{$this->inAttribute})) {
            foreach($images as $image) {
                if(isset($image[$size])) {
                    $return[] = $image[$size];
                }
            }
        }
        
        return $return;
    }
    
    public function getThumb($size = 'full')
    {
        if(empty($this->owner->{$this->inAttribute})) {
            return null;
        }
        
        $image = $this->owner->getImage();
        if($image instanceof \rico\yii2images\models\PlaceHolder) {
            return false;
        }
        
        if($size == 'full') {
            return $image->getUrl();
        }
        
        if($images = unserialize($this->owner->{$this->inAttribute})) {
            $image = current($images);
            return $image[$size];
        }
        
        return null;
    }
}