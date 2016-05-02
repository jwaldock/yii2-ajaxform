<?php

namespace jwaldock\ajaxform;

use Yii;
use yii\base\Model;
use yii\widgets\ActiveForm;

/**
 *
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