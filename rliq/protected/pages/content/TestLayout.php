<?php

class TestLayout extends TTemplateControl
{
    
    /**
    * @desc Creation of container components, defined in db
    */
    public function onInit($param)
    {
        parent::onInit($param);
        
        // Register onPreLoad event for this component
        $this->getPage()->attachEventHandler("onPreLoad",array($this,"onPreLoad"));        
    }
    
    
    
    /**
    * @desc Regenerating of dynamically created components
    */
    public function onPreLoad($param)
    {
        //recreate at runtime created dynamic components
        $dc = $this->Page->getControlState('WebgisDynamicControls',0);
        if(is_array($dc))
        {
            foreach($dc as $control)
            {
                // make a reflection object
                $reflectionObj = new ReflectionClass($control['classname']);

                // use Reflection to create a new instance, using the $args
                $c = $reflectionObj->newInstance();             

                // add dynamic control
                $this->Content->Controls[] = $c;
            }
        }
    }
    
    
    public function loadTestTemplate($sender, $param)
    {
        $c = new TestTemplate();

        $this->Content->Controls[] = $c;
        $c->dataBind();
        $this->Content->render($param->newWriter);
        $value = $this->Page->getControlState('WebgisDynamicControls');  
        $value[] = array('classname' => get_class($c), 'args' => null);
        $this->Page->setControlState('WebgisDynamicControls',TPropertyValue::ensureArray($value),0);
    }
    
        
}

?>