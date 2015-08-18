<?php
/**
 * Easily attach file upload functionality to a module
 *
 * @category    Ash
 * @package     Ash_Uploads
 * @copyright   Copyright (c) 2015 August Ash, Inc. (http://www.augustash.com)
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software Licence 3.0 (OSL-3.0)
 * @see         https://github.com/Jarlssen/Jarlssen_UploaderComponent
 */

/**
 * Field config model
 *
 * @category    Ash
 * @package     Ash_Uploads
 * @author      August Ash Team <core@augustash.com>
 */
class Ash_Uploads_Model_Config_Field extends Varien_Object
{
    /**
     * Returns the form's input name
     *
     * @return  string
     */
    public function getName()
    {
        return $this->_data['input_name'];
    }

    /**
     * Prepares the allowed file extensions configuration for Varien_File_Uploader
     *
     * @return  array
     */
    public function getAllowedExtensions()
    {
        if (isset($this->_data['allowed_extensions'])
            && $this->_data['allowed_extensions'] != '*') {
            return explode(',', $this->_data['allowed_extensions']);
        }

        return array();
    }

    /**
     * Returns the absolute upload path
     *
     * @return  string
     */
    public function getAbsoluteUploadPath()
    {
        return Mage::getBaseDir('media') . DS . $this->_data['upload_dir'];
    }

    /**
     * Returns the relative upload path from media directory
     *
     * @return  string
     */
    public function getRelativeUploadPath()
    {
        return $this->_data['upload_dir'];
    }
}
