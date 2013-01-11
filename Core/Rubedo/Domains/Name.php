<?php
/**
 * Rubedo -- ECM solution
 * Copyright (c) 2012, WebTales (http://www.webtales.fr/).
 * All rights reserved.
 * licensing@webtales.fr
 *
 * Open Source License
 * ------------------------------------------------------------------------------------------
 * Rubedo is licensed under the terms of the Open Source GPL 3.0 license.
 *
 * @category   Rubedo
 * @package    Rubedo
 * @copyright  Copyright (c) 2012-2012 WebTales (http://www.webtales.fr)
 * @license    http://www.gnu.org/licenses/gpl.html Open Source GPL 3.0 license
 */
namespace Rubedo\Domains;

/**
 * Validator for "name" Domain
 * 
 * Should be a string of at most 256 caracters
 * 
 * @author jbourdin
 *
 */
class Name implements IDomains
{
    /**
     * Check if a value is valid for the current domain
     * 
     * @param mixed $value
     * @return boolean
     * @see Rubedo\Domains\IDomains::isValid()
     */
    public static function isValid($value){
        if(!is_string($value)){
            return false;
        }
        if(mb_strlen($value,'UTF-8') > 256){
            return false;
        }
        return true;
    }
}