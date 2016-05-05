<?php
/**
 * @link https://github.com/jwaldock/yii2-ajaxform/
 * @copyright Copyright (c) 2016 Joel Waldock
 */

namespace jwaldock\ajaxform;

use yii\base\Action;
use Yii;
use yii\base\Model;
use yii\web\HttpException;
use yii\web\Response;
use yii\base\InvalidConfigException;

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
     * The model class must be a subclass of [[baseModelClass]].
     */
    public $modelClass;

    /**
     * @var boolean Whether tabular input is being used by the form.
     */
    public $tabular = false;
    
    /**
     * @var string The scenario to use when creating a model or models.
     */
    public $scenario = Model::SCENARIO_DEFAULT;
    
    /**
     * @var string
     */
    protected $baseModelClass = 'yii\db\ActiveRecord';

    /**
     * @inheritDoc
     */
    public function init()
    {
        $modelClass = $this->modelClass;
        $baseModelClass = $this->baseModelClass;
        if (!is_subclass_of($modelClass, $baseModelClass)) {
            throw new InvalidConfigException("$modelClass is not a subclass of $baseModelClass");
        }
    }
    
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
     * Loads [[yii\db\ActiveRecord]] with data from POST request.
     * 
     * @throws HttpException - if the model can't be loaded
     * @return \yii\db\ActiveRecord the model populated with data from the POST request
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
     * Loads array of [[yii\db\ActiveRecord]] with data from POST request.
     * 
     * @throws HttpException - if the models can't be loaded
     * @return \yii\db\ActiveRecord[] the models populated with data from the POST request
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
     * Creates an instance of modelClass with the defined scenario.
     * 
     * @return \yii\db\ActiveRecord the new instance of modelClass
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