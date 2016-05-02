<?php
/**
 * @link https://github.com/jwaldock/yii2-ajaxform/
 * @copyright Copyright (c) 2016 Joel Waldock
 */

namespace jwaldock\ajaxform;

use Yii;
use yii\base\Model;
use yii\widgets\ActiveForm;

/**
 * AjaxValidateAction is an external [[Action]] that validates a [[\yii\widgets\ActiveForm]].
 * 
 * AjaxValidateAction is intended to be used with [[AjaxFormWidget]] to provide AJAX model submission
 * for [[\yii\widgets\ActiveForm]].
 * 
 * This action can be set up in a [[Controller]] like the following:
 * 
 * ```php
 * public function actions()
 * {
 *      return [
 *          'validate-model' => [
 *              'class' => 'jwaldock\ajaxform\AjaxValidateAction',
 *              'modelClass' => 'model', // the fully qualified class name of the model  
 *              'tabular' => true, // set to true if using a tabular form - defaults to false
 *              'scenario' => 'model-scenario' // optional model scenario
 *          ],
 *          // other actions
 *      ];
 * }
 * ```
 * 
 * The [[\yii\widgets\ActiveForm]] should be configured to use AjaxValidation:
 * 
 * ```php
 * $form = ActiveForm::begin([
 *     'validationUrl' => ['controller/action'],
 * ]);
 * ```
 * 
 * @author Joel Waldock <joel.c.waldock@gmail.com>
 */
class AjaxValidateAction extends BaseAjaxAction
{   
    /**
     * {@inheritDoc}
     * @see \common\components\ajaxform\BaseAjaxAction::runTabular()
     */
    protected function runTabular($models)
    {
        if (Model::loadMultiple($models, Yii::$app->request->post())) {
            return ActiveForm::validateMultiple($models);
        }
        return false;
    }
    
    /**
     * {@inheritDoc}
     * @see \common\components\ajaxform\BaseAjaxAction::runSingle()
     */
    protected function runSingle($model)
    {       
        if ($model->load(Yii::$app->request->post())) {
            return ActiveForm::validate($model);
        }
        return false;
    }
}