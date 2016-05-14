<?php

namespace pistol88\gallery\controllers;

use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\Json;
use Yii;

class DefaultController extends \yii\web\Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'ajax' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => $this->module->adminRoles
                    ]
                ]
            ]
        ];
    }

    public function actionDelete()
    {
        $model = $this->findModel(yii::$app->request->post('model'), yii::$app->request->post('id'));
        foreach ($model->getImages() as $img) {
            if ($img->id == yii::$app->request->post('image')) {
                $model->removeImage($img);
                break;
            }
        }
        
        return $this->returnJson('success');
    }
    
    public function actionSetmain()
    {
        $model = $this->findModel(yii::$app->request->post('model'), yii::$app->request->post('id'));
        foreach ($model->getImages() as $img) {
            if ($img->id == yii::$app->request->post('image')) {
                $model->setMainImage($img);
                break;
            }
        }
        
        return $this->returnJson('success');
    }
    
    private function returnJson($result, $error = false)
    {
        $json = ['result' => $result, 'error' => $error];
        return Json::encode($json);
    }
    
    private function findModel($model, $id)
    {
        $model = '\\'.$model;
        $model = new $model();
        return $model::findOne($id);
    }
}
