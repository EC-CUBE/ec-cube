<?php
/**
 * This is a data type that is used in SOAP Interop testing, but is here as an
 * example of using complex types.  When the class is deserialized from a SOAP
 * message, it's constructor IS NOT CALLED!  So your type classes need to
 * behave in a way that will work with that.
 *
 * Some types may need more explicit serialization for SOAP.  The __to_soap
 * function allows you to be very explicit in building the SOAP_Value
 * structures.  The soap library does not call this directly, you would call
 * it from your soap server class, echoStruct in the server class is an
 * example of doing this.
 */
class SOAPStruct
{

    var $varString;
    var $varInt;
    var $varFloat;

    function SOAPStruct($s = null, $i = null, $f = null)
    {
        $this->varString = $s;
        $this->varInt = $i;
        $this->varFloat = $f;
    }
    
    function &__to_soap($name = 'inputStruct', $header = false,
                        $mustUnderstand = 0,
                        $actor = 'http://schemas.xmlsoap.org/soap/actor/next')
    {
        $inner[] = new SOAP_Value('varString', 'string', $this->varString);
        $inner[] = new SOAP_Value('varInt', 'int', $this->varInt);
        $inner[] = new SOAP_Value('varFloat', 'float', $this->varFloat);

        if ($header) {
            $value = new SOAP_Header($name,'{http://soapinterop.org/xsd}SOAPStruct',$inner,$mustUnderstand,$actor);
        } else {
            $value = new SOAP_Value($name,'{http://soapinterop.org/xsd}SOAPStruct',$inner);
        }

        return $value;
    }
}
