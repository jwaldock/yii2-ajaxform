<?php

namespace jwaldock\ajaxform;

use yii\base\Action;
use Yii;
use yii\db\ActiveRecord;
use yii\web\HttpException;
use yii\web\Response;

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
     * {@inheritDoc}
     * @see \yii\base\Action::beforeRun()
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
     * @param \yii\db\ActiveRecord[] $models
     * @return array JSON 
     */
    abstract protected function runTabular($models);
    

    /**
     * @param \yii\db\ActiveRecord $model
     * @return array JSON 
     */
    abstract protected function runSingle($model);
}