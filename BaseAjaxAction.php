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

/**
 * BaseAjaxAction is an abstract class that is the base for [[AjaxSubmitAction]] and [[AjaxValidateAction]].
 * 
 * Implementing classes should override [[runTabular()]] and [[runSingle()]].
 * 
 * @author Joel Waldock <joel.c.waldock@gmail.com>
 */
abstract class BaseAjaxAction extends Action
{
    /**
     * @var string class name of the model which will be handled by this action.
     * The model class must be an instance of [[ActiveRecord]].
     * This property must be set.
     */
    public $modelClass;

    /**
     * @var boolean Whether tabular input is being used by the form.
     */
    public $tabular = false;
    
    /**
     * @var string The scenario to use when creating a model or models.
     */
    public $scenario;

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
        $model = $this->getModel();
        $model->scenario = isset($this->scenario) ? $this->scenario : $model->scenario;
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($this->tabular) {
            $count = count(Yii::$app->request->post($model->formName(), []));
            $models = [];
            for ($i = 0; $i < $count; $i++) {
                $models[] = new $this->modelClass([
                    'scenario' => $model->scenario,
                ]);
            }
            $response = $this->runTabular($models);
        } else {
            $response = $this->runSingle($model);
        }
        if($response !== false) {
            return $response;
        }
        throw new HttpException(400, 'Bad POST data.');
    }
    
    
    /**
     * @return \yii\db\ActiveRecord
     */
    protected function getModel()
    {
        //TODO checking
        if (is_a($model = new $this->modelClass, '\yii\db\ActiveRecord')) {
            return $model;
        }
        throw new HttpException(500, 'AJAX submit action model not a ActiveRecord implementation');
    }

    /**
     * Returns a JSON array generated from multiple models.
     * 
     * The resulting array called and returned by [[run()]] if in tabular mode.
     * 
     * @param \yii\db\ActiveRecord[] $models
     * @return array JSON 
     */
    abstract protected function runTabular($models);
    

    /**
     * Returns a JSON array generated from a single model.
     * 
     * The resulting array called and returned by [[run()]] if in single mode.
     * 
     * @param \yii\db\ActiveRecord $model
     * @return array JSON 
     */
    abstract protected function runSingle($model);
}