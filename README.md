ActiveForm AJAX Save Widget
===========================
Provides AJAX save for yii2 ActiveForm

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Add

```
"repositories":[
    {
        "type": "git",
        "url": "https://github.com/jwaldock/yii2-ajaxform"
        "url": "https://path.to/your/repo"
    }
]
```

to your `composer.json` file.

Either run

```
php composer.phar require jwaldock/yii2-ajaxform:dev-master
```

or add.

```
"jwaldock/yii2-ajaxform": "dev-master"
```

```
"repositories":[
    {
        "type": "git",
        "url": "https://path.to/your/repo"
    }
]
```

to your `composer.json` file.

Either run

```
php composer.phar require jwaldock/yii2-ajaxform:dev-master
```

or add.

```
"jwaldock/yii2-ajaxform": "dev-master"
```

to the require section of your `composer.json` file

Usage
-----
Set up AJAX validation for your 

`yiiAjaxForm` provides three events `submitBegin`, `submitEnd` and `submitFailed`. 
Event handlers for these events should have the following function signatures:


```js
// 'submitBegin' event
function (event) {
    // handle submission begin
}
```

```js
// 'submitEnd' event
function (event, success, modeldata) {
    if (success) {
        // handle successful save
        return
    }
    // handle failed save
}
```

```js
// submitFailed
function (event, xhr) {
    // handle failure
}
```

```php
public function actions()
{
   return [
       'submit-model' => [
           'class' => 'jwaldock\ajaxform\AjaxSubmitAction',
           'modelClass' => 'model', // the fully qualified class name of the model  
           'tabular' => true, // set to true if using a tabular form - defaults to false
           'scenario' => 'model-scenario' // optional model scenario
       ],
       // other actions
   ];
}
```


```php
<?php 
$js = <<<JS
function (event, success, modeldata)  {
    if (success) {
        // handle successful save
    }
}
JS;

AjaxFormWidget::widget([
    'form' => $form, // ActiveForm
    'disableSubmit' => true, // whether to disable the submit button on submitting the form
    'savingContent' => 'Saving...', the inner html content of the submit button when saving
    'clientEvents' => [
        'submitEnd' => $js,
    ],
]);
?>
```
