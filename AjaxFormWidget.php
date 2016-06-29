<?php
/**
 * @link https://github.com/jwaldock/yii2-ajaxform/
 * @copyright Copyright (c) 2016 Joel Waldock
 */

namespace jwaldock\ajaxform;

use yii\base\Widget;
use yii\helpers\Json;
use yii\helpers\Url;

/**
 * AjaxFormWidget is a widget that provides AJAX submission for [[\yii\widgets\ActiveForm]].
 * TODO docs
 * 
 * @author Joel Waldock
 */
class AjaxFormWidget extends Widget
{
    /**
     * @var array|string $action the form action URL. This parameter will be processed by [[\yii\helpers\Url::to()]].
     * Defaults to form action if not set.
     * @see method for specifying the HTTP method for this form.
     */
    public $action;
    
    /**
     * @var boolean whether to reset the form on successful submission
     */
    public $resetOnSuccess;

    /**
     * @var boolean whether form must be reset before it can be submitted again
     */
    public $requireReset;
    
    /**
     * @var string 
     */
    public $failCallback;
    
    /**
     * @var \yii\widgets\ActiveForm the form that ajax saving is registered for.
     */
    public $form;
    
    /**
     * @var array event => handler see yii.ajaxForm.js for events
     */
    public $clientEvents;

    /**
     * @inheritDoc
     */
    public function run()
    {
        $view = $this->getView();
        $id = $this->form->id;
        $options = Json::htmlEncode($this->getClientOptions());
        
        AjaxFormAsset::register($view);
        $view->registerJs("jQuery('#$id').ajaxForm($options);");
        $this->registerClientEvents();
    }
    
    /**
     * Returns the options for the ajax form JS widget.
     * @return array the options
     */
    protected function getClientOptions()
    {
        $options = [
            'resetOnSuccess' => $this->resetOnSuccess,
            'requireReset' => $this->requireReset,
            'failCallback' => $this->failCallback,
            'action' => Url::to($this->action),
        ];
        
        return array_filter($options, function($value) { return $value !== null;});
    }
    
    /**
     * Registers client events for the ajax form JS widget.
     */
    protected function registerClientEvents()
    {
        if (!empty($this->clientEvents)) {
            $id = $this->form->id;
            $js = [];
            foreach ($this->clientEvents as $event => $handler) {
                $js[] = "jQuery('#$id').on('$event', $handler);";
            }
            $this->getView()->registerJs(implode("\n", $js));
        }
    }
}
