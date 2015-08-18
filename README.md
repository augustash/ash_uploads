# Description

This generic uploads module borrows and improves upon a module from [Tsvetan Stoychev](https://github.com/Jarlssen/Jarlssen_UploaderComponent). The premise is to encapsulate the common behavior of uploading files within Magento admin area forms. By enabling this module and defining in XML which fields are uploads, you are done.

Dependencies
------------

* [Ash_Core](https://github.com/augustash/ash_core)

Installation
------------

1. Install [composer](http://getcomposer.org/download/)
2. Configure your project for [Magento Composer](https://github.com/magento-hackathon/magento-composer-installer)
3. Add August Ash Packages repository (requires authentication - check Sequoia)
4. Add `Ash_Uploads` to your project's `composer.json`
5. From the project root, run `php composer.phar install` or `composer install`
6. Logout and delete all contents of the Magento cache

#### Sample Project `composer.json`

```json
{
    ...
    "minimum-stability": "dev",
    "require": {
        "magento-hackathon/magento-composer-installer": "*",
        "augustash/ash_uploads": "*"
    },

    "repositories": [
        {
            "type": "composer",
            "url": "https://packages.augustash.com/repo/private/"
        },
        {
            "type": "composer",
            "url": "http://packages.firegento.com"
        }
    ],
    ...
}

```

Usage
-----

Once your custom module is built, enabling uploads with this module is a simple two step process.

1. Add a dependency on `Ash_Uploads` within your module to ensure proper load order

```xml
<?xml version="1.0" encoding="UTF-8"?>
<config>
    <modules>
        <Example_Module>
            <active>true</active>
            <codePool>local</codePool>
            <depends>
                <Ash_Uploads/>
            </depends>
        </Example_Module>
    </modules>
</config>
```

2. Add the upload fields to your module's `config.xml`

```xml
<?xml version="1.0" encoding="UTF-8"?>
<config>
    ...
    <ash_uploads_model_config>
        <uploads>
            <Example_Module_Model_Test>
                <thumbnail>
                    <input_name>thumbnail</input_name>
                    <upload_dir>example_module/thumbnails</upload_dir>
                    <allowed_extensions>jpg,jpeg,gif,png</allowed_extensions>
                </thumbnail>
                <background>
                    <input_name>background_image</input_name>
                    <upload_dir>example_module/backgrounds</upload_dir>
                    <allowed_extensions>jpg,jpeg,gif,png</allowed_extensions>
                </background>
            </Example_Module_Model_Test>
        </uploads>
    </ash_uploads_model_config>
    ...
</config>
```


That's it. Now when the `save()` and `delete()` methods of your model are triggered via your admin controller, `Ash_Uploads` will check if it can operate on the defined fields in your XML.

Example Code
------------

**File:** `app/code/local/Example/Module/Block/Adminhtml/Test/Edit/Form.php`

```php
<?php
    // ...
    $fieldset->addField('thumbnail', 'image', array(
        'name'     => 'thumbnail',
        'label'    => Mage::helper('example_module')->__('Thumbnail Image'),
        'title'    => Mage::helper('example_module')->__('Thumbnail Image'),
        'required' => true,
    ));

    $fieldset->addField('background_image', 'image', array(
        'name'     => 'background_image',
        'label'    => Mage::helper('example_module')->__('Background Image'),
        'title'    => Mage::helper('example_module')->__('Background Image'),
        'required' => true,
    ));
    // ...
```

**File:** `app/code/local/Example/Module/controllers/Adminhtml/TestController.php`

```php
<?php
    // ...
    public function saveAction()
    {
        //...
        $model->addData($data);
        $model->save(); // A `model_save_before` event will handle the uploads
        $this->_getSession()->addSuccess($this->__('Saved!'));
        //...
    }

    public function deleteAction()
    {
        //...
        $model = Mage::getModel('example_module/test')->load($id);
        $model->delete(); // A `model_delete_after` event will remove the uploads
        $this->_getSession()->addSuccess($this->__('Deleted!'));
        //...
    }
    // ...
```

```
@copyright  Copyright (c) 2015 August Ash, Inc. (http://www.augustash.com)
```
