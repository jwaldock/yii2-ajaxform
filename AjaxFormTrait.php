<?php

namespace jwaldock\ajaxform;

trait AjaxFormTrait
{
    /**
     * @var array event => handler see yii.ajaxForm.js for events
     */
    public $ajaxFormConfig = [];
    
    public function run()
    {
        parent::run();
        $ajaxFormOptions = $this->ajaxFormOptions;
        $ajaxFormOptions['form'] = $this;
        AjaxFormWidget::widget($ajaxFormOptions);
    }
}
