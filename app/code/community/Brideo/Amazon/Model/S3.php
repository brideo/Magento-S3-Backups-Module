<?php

require_once('Amazon/S3.php');

class Brideo_Amazon_Model_S3 extends Mage_Core_Model_Abstract
{

    /**
     * @var Brideo_Amazon_Helper_Data
     */
    protected $helper;

    /**
     * @var S3
     */
    protected $s3;

    /**
     * Amazon s3 constructor
     */
    protected function _construct()
    {
        $this->helper = Mage::helper('brideo_amazon');
        parent::_construct();
    }

    /**
     * Prepare the backup and send it to Amazon.
     *
     * @return $this
     */
    public function prepareBackup()
    {
        if (!$this->helper->isEnabled()) {
            return $this;
        }

        try {

            $backupDb = Mage::getModel('backup/db');
            $backup = Mage::getModel('backup/backup')
                ->setTime(time())
                ->setType('db')
                ->setPath(Mage::getBaseDir('var') . DS . 'backups');

            $backupDb->createBackup($backup);
            $this->transferFiles();

        } catch (Exception  $e) {
            Mage::logException($e);
        }

        return $this;
    }

    /**
     * Get the s3 library
     *
     * @return S3
     */
    public function getS3()
    {
        if(!$this->s3) {
            $this->s3 = new S3($this->helper->getAccessKey(), $this->helper->getSecretKey());
        }

        return $this->s3;
    }

    /**
     * Transfer the files to Amazon.
     *
     * @return $this
     */
    protected function transferFiles()
    {
        $this->s3->putBucket($this->helper->getBucketName(), S3::ACL_PUBLIC_READ);

        foreach ($this->getFileNames() as $file) {
            if ($this->s3->putObjectFile($file, $this->helper->getBucketName(), $this->getFileName($file), S3::ACL_PUBLIC_READ)) {
                unlink($file);
            } else {
                Mage::log("The file: {$this->getFileName($file)} did not Backup to Amazon S3.", Zend_Log::DEBUG, 'amazonS3.log');
            }
        }

        return $this;
    }

    /**
     * Get the files in the backups directory
     *
     * @return array
     */
    protected function getFileNames()
    {
        $dir = Mage::getBaseDir('var') . DS . 'backups/*.gz';
        return glob($dir);
    }

    /**
     * Get the file name with the sql filetype.
     *
     * @param $file
     *
     * @return string
     */
    protected function getFileName($file)
    {
        $info = pathinfo($file);
        return $info['filename'] . '.' . 'sql.gz';
    }

}
