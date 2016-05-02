<?php

namespace jwaldock\ajaxform;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 *
 */
class AjaxSubmitAction extends BaseAjaxAction
{    
    /**
     * {@inheritDoc}
     * @see \common\components\ajaxform\BaseAjaxAction::runTabular()
     */
    protected function runTabular($models)
    {
        if (Model::loadMultiple($models,  Yii::$app->request->post()) && Model::validateMultiple($models)) {
            foreach ($models as $model) {
                $model->save(false);
            }
            return [
                'success' => true,
                'model' => ArrayHelper::toArray($models),
            ];
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
            return [
                'success' => $model->save(),
                'model' => $model->toArray(),
            ];
        }
        return false;
    }
}