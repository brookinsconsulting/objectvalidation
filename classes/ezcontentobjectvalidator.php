<?php

/*!
 \brief eZContentObjectValidator allows to do validation on object level
 eZ publish currently only allows validation of separate attributes. eZContentObjectValidator allows you
 to implement advanced business rules when storing or publishing an object.
*/
class eZContentObjectValidator
{
    function &instance( $type )
    {
        $impl = false;
        $params = array(
            'ini-name' => 'objectvalidation.ini',
            'repository-group' => 'ValidatorSettings',
            'repository-variable' => 'ValidatorRepositories',
            'extension-group' => 'ValidatorSettings',
            'extension-variable' => 'ValidatorExtensions',
            'extension-subdir' => 'classes/contentobjectvalidators',
            'suffix-name' => 'contentobjectvalidator.php',
            'type-directory' => false,
            'type' => strtolower( $type )
        );

        $result = eZExtension::findExtensionType( $params, $out );

        if ( $out['found-type'] == false )
        {
            $cli->writeError( 'unable to find validation handler of type ' . $type );
            return $impl;
        }

        include_once( $out['found-file-path'] );
        $className = $out['type'] . 'ContentObjectValidator';
        $impl = new $className();
        return $impl;
    }

    function validate( &$object, $validationParameters )
    {
        //eZDebug::writeDebug( $validationParameters );
        $result = array( 'validated' => true, 'warnings' => array() );
        $validated =& $result['validated'];
        $warnings =& $result['warnings'];

        $classIdentifier = $object->attribute( 'class_identifier' );

        include_once( 'lib/ezutils/classes/ezini.php' );
        $ini =& eZINI::instance( 'objectvalidation.ini' );
        $groupName = 'Class_' . $classIdentifier;

        if ( $ini->hasGroup( $groupName ) && $ini->hasVariable( $groupName, 'Validators' ) )
        {
            $validators = $ini->variable( $groupName, 'Validators' );

            foreach ( $validators as $validatorName )
            {
                $impl =& eZContentObjectValidator::instance( $validatorName );
                if ( $impl )
                {
                    $isValid = $impl->isValid( $object, $validationParameters );
                    if ( !$isValid )
                    {
                        $newWarnings = $impl->getWarnings();
                        if ( $newWarnings )
                        {
                            $warnings = array_merge( $warnings, $newWarnings );
                        }
                        $validated = false;
                    }
                }
                else
                {
                    eZDebug::writeWarning( 'Object validator implementation not found' );
                }
            }
        }

        return $result;
    }
}

?>