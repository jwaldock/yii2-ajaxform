ActiveForm AJAX Save Widget
===========================
Provides AJAX save for yii2 ActiveForm

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Add
<<<<<<< HEAD

```
"repositories":[
    {
        "type": "git",
<<<<<<< HEAD
        "url": "https://github.com/jwaldock/yii2-ajaxform"
=======
        "url": "https://path.to/your/repo"
>>>>>>> origin/master
    }
]
```

to your `composer.json` file.

Either run

```
php composer.phar require jwaldock/yii2-ajaxform:dev-master
```

or add.
<<<<<<< HEAD

```
"jwaldock/yii2-ajaxform": "dev-master"
```

=======
=======

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
>>>>>>> origin/master

```
"jwaldock/yii2-ajaxform": "dev-master"
```

<<<<<<< HEAD
>>>>>>> origin/master
=======
>>>>>>> origin/master
to the require section of your `composer.json` file

Usage
-----
Set up AJAX validation for your 

`yiiAjaxForm` provides three events `submitBegin`, `submitEnd` and `submitFailed`. 
Event handlers for these events should have the following function signatures:

<<<<<<< HEAD

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

<<<<<<< HEAD

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
    'disableSubmit' => true,
    'savingContent' => 'Saving...',
    'clientEvents' => [
        'submitEnd' => $js,
    ],
]);
AjaxFormAsset::register($this);
?>
```
=======
>>>>>>> origin/master
=======
>>>>>>> origin/master
