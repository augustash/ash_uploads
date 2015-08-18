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
 * Uploaded files helper
 *
 * @category    Ash
 * @package     Ash_Uploads
 * @author      August Ash Team <core@augustash.com>
 */
class Ash_Uploads_Helper_Uploader extends Mage_Core_Helper_Abstract
{
    /**
     * Process any uploaded files defined by the passed model's configuration.
     *
     * @param   Mage_Core_Model_Abstract $model
     * @return  void
     * @throws  Exception
     */
    public function processFiles(Mage_Core_Model_Abstract $model)
    {
        $fields   = Mage::helper('ash_uploads')->getModelConfig($model);
        $postData = $this->_getRequest()->getPost();

        // check if any of the model's configured fields need to be processed
        foreach ($fields as $field) {
            if ($_FILES[$field->getName()]['name']
                && $_FILES[$field->getName()]['error'] === 0) {
                // process an upload
                $this->processFile($model, $field, $_FILES[$field->getName()]['name']);
            } else {
                if (isset($postData[$field->getName()]['delete'])) {
                    // Delete old image
                    $this->_removeImage($field, $postData[$field->getName()]['value']);
                    $model->setData($field->getName(), '');
                } else {
                    if (isset($postData[$field->getName()]['value'])) {
                        // retain the existing image
                        $model->setData($field->getName(), $postData[$field->getName()]['value']);
                    }
                }
            }
        }
    }

    /**
     * When a model record is deleted, also remove any associated uploaded files
     *
     * @param   Mage_Core_Model_Abstract $model
     * @return  void
     * @throws  Exception
     */
    public function processRemovals(Mage_Core_Model_Abstract $model)
    {
        $fields = Mage::helper('ash_uploads')->getModelConfig($model);

        // check if any of the model's configured fields need to be processed
        foreach ($fields as $field) {
            // Delete old image
            $this->_removeImage($field, $model->getData($field->getName()));
        }
    }

    /**
     * Uploads a file and updates the corresponding model with new file location.
     *
     * @param   Mage_Core_Model_Abstract $model
     * @param   Ash_Uploads_Model_Config_Field $field
     * @param   string $filename
     * @return  void
     * @throws  Exception
     */
    public function processFile(Mage_Core_Model_Abstract $model, Ash_Uploads_Model_Config_Field $field, $filename)
    {
        /*
         * Upload and save the file. Then populate the model with the new
         * file's path for later retrieval.
         */
        try {
            $results = $this->_uploadFile($field, $filename);
            if (!empty($results)) {
                $uploadedFile = $field->getRelativeUploadPath() . DS . $results['file'];
                $model->setData($field->getName(), $uploadedFile);
            }
        } catch (Exception $e) {
            // reset to original data if failure
            $model->setData($field->getName(), $model->getOrigData($field->getName()));
            Mage::logException($e);
            throw new Exception('Failed to upload file: ' . $filename);
        }
    }

    /**
     * Uploads a file and returns the results of the process
     *
     * @param   Ash_Uploads_Model_Config_Field $field
     * @param   string $filename
     * @return  array
     */
    protected function _uploadFile(Ash_Uploads_Model_Config_Field $field, $filename)
    {
        $uploader = $this->_getUploader($field);
        return $uploader->save($field->getAbsoluteUploadPath(), $filename);
    }

    /**
     * Returns a configured uploader class.
     *
     * @param   Ash_Uploads_Model_Config_Field $field
     * @return  Varien_File_Uploader
     */
    protected function _getUploader(Ash_Uploads_Model_Config_Field $field)
    {
        $uploader = new Varien_File_Uploader($field->getName());
        $uploader->setAllowedExtensions($field->getAllowedExtensions());
        $uploader->setAllowRenameFiles(true);
        $uploader->setFilesDispersion(false);

        return $uploader;
    }

    /**
     * Removes an uploaded image from the filesystem
     *
     * @param   Ash_Uploads_Model_Config_Field $field
     * @param   string $imageName
     * @return  void
     * @throws  Exception
     */
    protected function _removeImage(Ash_Uploads_Model_Config_Field $field, $imageName)
    {
        try {
            $fileName = Mage::getBaseDir('media') . DS . $imageName;
            $ioProxy  = new Varien_Io_File();
            if ($ioProxy->fileExists($fileName)) {
                $ioProxy->rm($fileName);
            }
        } catch (Exception $e) {
            Mage::logException($e);
            throw new Exception('Failed to remove file: ' . $imageName);
        }
    }
}
