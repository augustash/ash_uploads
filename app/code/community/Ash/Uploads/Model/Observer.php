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
 * Observer model
 *
 * @category    Ash
 * @package     Ash_Uploads
 * @author      August Ash Team <core@augustash.com>
 */
class Ash_Uploads_Model_Observer
{
    /**
     * Handler for admin "model_save_before" event which will look for possible
     * file uploads
     *
     * @param   Varien_Event_Observer $observer
     * @return  void
     */
    public function processUpload(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('ash_uploads')->isEnabled()
            && !Mage::helper('ash_uploads')->isConfigured()) {
            return;
        }

        $model = $observer->getEvent()->getData('object');

        if (Mage::helper('ash_uploads')->validateModelConfig($model)) {
            $uploader = Mage::helper('ash_uploads/uploader');
            $uploader->processFiles($model);
        }
    }

    /**
     * Handler for admin "model_delete_after" event which will look for possible
     * file removals
     *
     * @param   Varien_Event_Observer $observer
     * @return  void
     */
    public function processRemovals(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('ash_uploads')->isEnabled()
            && !Mage::helper('ash_uploads')->isConfigured()) {
            return;
        }

        $model = $observer->getEvent()->getData('object');

        if (Mage::helper('ash_uploads')->validateModelConfig($model)) {
            $remover = Mage::helper('ash_uploads/uploader');
            $remover->processRemovals($model);
        }
    }
}
