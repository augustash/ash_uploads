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
 * Core data helper
 *
 * @category    Ash
 * @package     Ash_Uploads
 * @author      August Ash Team <core@augustash.com>
 */
class Ash_Uploads_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Enabled constant for uploads
     *
     * @var integer
     */
    const XML_PATH_UPLOADS_ENABLED = 'ash_uploads/general/enabled';

    /**
     * Global config constant for uploads
     *
     * @var array
     */
    const XML_PATH_UPLOADS_CONFIG = 'ash_uploads_model_config/uploads';

    /**
     * Configuration element for uploads
     *
     * @var Mage_Core_Model_Config_Element
     */
    protected $_uploadConfig = null;

    /**
     * Check if upload handling is enabled
     *
     * @return  boolean
     */
    public function isEnabled()
    {
        if (Mage::getStoreConfigFlag(self::XML_PATH_UPLOADS_ENABLED)) {
            return true;
        }

        return false;
    }

    /**
     * Check if upload configuration exists
     *
     * @return  boolean
     */
    public function isConfigured()
    {
        return (!$this->getConfig());
    }

    /**
     * Returns global upload configuration node
     *
     * @return  Mage_Core_Model_Config_Element|null
     */
    public function getConfig()
    {
        if ($this->_uploadConfig === null) {
            // attempt to populate config
            $this->_uploadConfig = Mage::getConfig()->getNode(self::XML_PATH_UPLOADS_CONFIG);
        }

        return $this->_uploadConfig;
    }

    /**
     * Validate that the passed model is correctly configured for file upload
     * processing
     *
     * @param   Mage_Core_Model_Abstract $model
     * @return  boolean
     */
    public function validateModelConfig(Mage_Core_Model_Abstract $model)
    {
        $config    = $this->getConfig();
        $className = get_class($model);
        $available = array_keys($config->asArray());

        return (in_array($className, $available));
    }

    /**
     * Return the passed model's configuration as array of upload fields
     *
     * @param   Mage_Core_Model_Abstract $model
     * @return  array
     */
    public function getModelConfig(Mage_Core_Model_Abstract $model)
    {
        $config      = $this->getConfig();
        $className   = get_class($model);
        $fields      = $config->$className->asArray();
        $modelConfig = array();

        foreach($fields as $field) {
            $modelConfig[] = Mage::getModel('ash_uploads/config_field', $field);
        }

        return $modelConfig;
    }
}
