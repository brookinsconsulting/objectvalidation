Installation
--------------

Enable the objectvalidation extension

Move extension/objectvalidation/install/objectvalidation.ini to settings/objectvalidation.ini

Patch your eZ Publish installation with extension/objectvalidation/install/attribute_edit.patch

In your template code where you normally show any attribute validation errors (content/edit_validation.tpl) you should
also use $validation.business_rules now in addition to $validation.attributes and $validation.placement. For an example
take a look at extension/objectvalidation/design/admin/templates/content/edit_validation.tpl


Configuration
----------------

You need to define a new INI group in objectvalidation.ini.append.php for each class on which you want to
use custom validators. The INI group consists of "Class_" and the content class identifier.

In the INI group, you can specify which validators to run with the Validators setting.

There's one example validator in the objectvalidation extension which checks if the value of the end_date attribute
(type: ezdate) is greater than the value of the start_date attribute (type: ezdate).

[Class_date_test]
Validators[]
Validators[]=ezdateorder


Writing custom validators
---------------------------

You can put custom validators in your own extensions.

Let eZ publish now that your extension contains custom validators, in yourextension/settings/objectvalidation.ini.append:

[ValidatorSettings]
ValidatorExtensions[]=yourextension

The class name of a custom content object validator consists of a unique identifier, which you need to choose yourself,
and "ContentObjectValidator". The name of the file to put this class into is the class name in lower case with the .php
file extension appended. Place this file in the directory yourextension/classes/contentobjectvalidators.

For an example, take a look at extension/objectvalidation/classes/contentobjectvalidators/ezdateordercontentobjectvalidator.php