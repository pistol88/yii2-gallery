<?php
namespace pistol88\gallery;

use yii;

class Module extends \rico\yii2images\Module
{
    public $adminRoles = ['admin', 'superadmin'];
    public $controllerNamespace = 'pistol88\gallery\controllers';
    public $controllerMap = ['images' => 'rico\yii2images\controllers\ImagesController'];
    
    public function init()
    {
        parent::init();
    }
}
