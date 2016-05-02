<?php
/**
 * @link https://github.com/jwaldock/yii2-ajaxform/
 * @copyright Copyright (c) 2016 Joel Waldock
 */

namespace jwaldock\ajaxform;

use yii\base\Widget;
use yii\helpers\Json;

/**
 * AjaxFormWidget is a widget that provides AJAX save for [[\yii\widgets\ActiveForm]].
 * TODO docs
 * 
 * @author Joel Waldock
 */
class AjaxFormWidget extends Widget
{
    /**
     * @var boolean whether to reset the form on successful save
     */
    public $resetOnSave;

    /**
     * @var boolean whether to disable the submit button on saving
     */
    public $disableSubmit;
    
    /**
     * @var string content for the submit button on save - if not set the submit button content
     * will not change on save.
     */
    public $savingContent;
    
    /**
     * @var string selector for submit button. Only needed if form does not contain submit button.
     */
    public $submitSelector;
    
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
        $view->registerJs("jQuery('#$id').yiiAjaxForm($options);");
        $this->registerClientEvents();
    }
    
    /**
     * Returns the options for the ajax form JS widget.
     * @return array the options
     */
    protected function getClientOptions()
    {
        $options = [
            'resetOnSave' => $this->resetOnSave,
            'disableSubmit' => $this->disableSubmit,
            'savingContent' => $this->savingContent,
            'submitSelector' => $this->submitSelector,
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