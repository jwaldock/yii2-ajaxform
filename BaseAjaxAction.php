<?php
/**
 * @link https://github.com/jwaldock/yii2-ajaxform/
 * @copyright Copyright (c) 2016 Joel Waldock
 */

namespace jwaldock\ajaxform;

use yii\base\Action;
use Yii;
use yii\db\ActiveRecord;
use yii\web\HttpException;
use yii\web\Response;
use yii\base\Model;

/**
 * BaseAjaxAction is an abstract class that is the base for [[AjaxSubmitAction]] and [[AjaxValidateAction]].
 * 
 * Implementing classes must override [[runTabular()]] and [[runSingle()]].
 * 
 * @author Joel Waldock <joel.c.waldock@gmail.com>
 */
abstract class BaseAjaxAction extends Action
{
    /**
     * @var string class name of the model which will be handled by this action.
     * The model class must be an instance of [[ActiveRecord]].
     */
    public $modelClass;

    /**
     * @var boolean Whether tabular input is being used by the form.
     */
    public $tabular = false;
    
    /**
     * @var string The scenario to use when creating a model or models.
     */
    public $scenario = ActiveRecord::SCENARIO_DEFAULT;

    /**
     * @inheritDoc
     */
    public function beforeRun()
    {

        $request = Yii::$app->request;
        if (!$request->isPost) {
            throw new HttpException(405, 'Only POST requests are allowed.');
        }
        
        if (!$request->isAjax) {
            throw new HttpException(403, 'Only AJAX requests are allowed.');
        }
        return true;
    }

    /**
     * @throws \yii\web\HttpException
     */
    public function run()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($this->tabular) {
            $models = $this->loadModels();
            $response = $this->runTabular($models);
        } else {
            $model = $this->loadModel();
            $response = $this->runSingle($model);
        }
        return $response;
    }

    /**
     * @throws HttpException
     * @return \yii\db\ActiveRecord
     */
    protected function loadModel()
    {
        $model = $this->createModel();
        if ($model->load(Yii::$app->request->post())) {
            return $model;
        }
        throw new HttpException(400, 'Bad POST data - model failed to load.');
    }

    /**
     * @throws HttpException
     * @return \yii\db\ActiveRecord[]
     */
    protected function loadModels()
    {
        $count = count(Yii::$app->request->post($this->createModel()->formName(), []));
        $models = [];
        for ($i = 0; $i < $count; $i++) {
            $models[] = $this->createModel();
        }
        if (Model::loadMultiple($models,  Yii::$app->request->post())) {
            return $models;
        }
        throw new HttpException(400, 'Bad POST data - models failed to load.');
    }
    

    /**
     * @return \yii\db\ActiveRecord
     */
    protected function createModel()
    {
        return Yii::createObject([
            'class' => $this->modelClass,
            'scenario' => $this->scenario,
        ]);
    }
    
    /**
     * Returns a JSON array generated from multiple models.
     * 
     * The resulting array called and returned by [[run()]] if in tabular mode.
     * 
     * @param \yii\db\ActiveRecord[] $models
     * @return array data to be encoded into a JSON array
     */
    abstract protected function runTabular($models);

    /**
     * Returns a JSON array generated from a single model.
     * 
     * The resulting array called and returned by [[run()]] if in single mode.
     * 
     * @param \yii\db\ActiveRecord $model
     * @return array data to be encoded into a JSON array
     */
    abstract protected function runSingle($model);
}