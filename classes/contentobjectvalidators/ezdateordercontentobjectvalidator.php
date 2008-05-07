<?php

class eZDateOrderContentObjectValidator
{
    var $Warnings;

    function eZDateOrderContentObjectValidator()
    {
        $this->Warnings = array();
    }

    function getWarnings()
    {
        return $this->Warnings;
    }

    function isValid( $object, $dataMap, $validationParameters )
    {
        $startDate = $dataMap['start_date']->attribute( 'content' );
        $endDate = $dataMap['end_date']->attribute( 'content' );

        //eZDebug::writeDebug( implode( '-', array( $startDate->year(), $startDate->month(), $startDate->day() ) ) );

        if ( !$endDate->isGreaterThan( $startDate ) )
        {
            $this->Warnings[] = array( 'text' => 'End date must be greater than start date' );
            $dataMap['start_date']->setHasValidationError();
            $dataMap['end_date']->setHasValidationError();

            return false;
        }

        return true;
    }
}

?>