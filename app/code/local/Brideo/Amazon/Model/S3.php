<?php

require_once('Amazon/S3.php');

class Brideo_Amazon_Model_S3 extends Mage_Core_Model_Abstract {

    protected function _construct() {
        $__accessKey = Mage::getStoreConfig('amazon/configuration/accesskey');
        $__secretKey = Mage::getStoreConfig('amazon/configuration/secretkey');
        $__bucketName = Mage::getStoreConfig('amazon/configuration/bucketname');
        if (!defined('awsAccessKey')) define('awsAccessKey', $__accessKey);
        if (!defined('awsSecretKey')) define('awsSecretKey', $__secretKey);
        if (!defined('awsBucketName')) define('awsBucketName', $__bucketName);
    }

    public function prepareBackup()
    {
        if(Mage::getStoreConfig('amazon/configuration/enabled')) {
            try {

                $backupDb = Mage::getModel('backup/db');
                $backup   = Mage::getModel('backup/backup')
                    ->setTime(time())
                    ->setType('db')
                    ->setPath(Mage::getBaseDir('var') . DS . 'backups');

                $backupDb->createBackup($backup);

            } catch (Exception  $e) {
                Mage::logException($e);
            }
            $this->_transferFiles();
        }
    }

    private function _transferFiles() {
        $s3 = new S3(awsAccessKey, awsSecretKey);

        $s3->putBucket(awsBucketName, S3::ACL_PUBLIC_READ);
        foreach($this->_getFileNames() as $file) {
            $fileName = $this->_saveName($file);
            if($s3->putObjectFile($file, awsBucketName, $fileName, S3::ACL_PUBLIC_READ)) {
                unlink($file);
            } else {
                Mage::log('The file: ' . $fileName . ' did not Backup to Amazon S3.', null, 'amazonS3.log');
            }
        }
    }

    private function _getFileNames() {
        $dir = Mage::getBaseDir('var') . DS . 'backups/*.gz';
        return glob($dir);
    }

    private function _saveName($file) {
        $array = explode('/', $file);
        return end($array);
    }

}