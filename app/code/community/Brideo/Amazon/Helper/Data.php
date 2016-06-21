<?php

class Brideo_Amazon_Helper_Data extends Mage_Core_Helper_Abstract
{

    const XML_PATH_AMAZON_ACCESS_KEY = 'amazon/configuration/accesskey';
    const XML_PATH_AMAZON_SECRET_KEY = 'amazon/configuration/secretkey';
    const XML_PATH_AMAZON_BUCKET_NAME = 'amazon/configuration/bucketname';
    const XML_PATH_AMAZON_ENABLED = 'amazon/configuration/enabled';

    /**
     * Get the Amazon S3 key.
     *
     * @return string
     */
    public function getAccessKey()
    {
        return Mage::getStoreConfig(static::XML_PATH_AMAZON_ACCESS_KEY);
    }

    /**
     * Get the Amazon secret key.
     *
     * @return string
     */
    public function getSecretKey()
    {
        return Mage::getStoreConfig(static::XML_PATH_AMAZON_SECRET_KEY);
    }

    /**
     * Get the Amazon bucket name.
     *
     * @return string
     */
    public function getBucketName()
    {
        return Mage::getStoreConfig(static::XML_PATH_AMAZON_BUCKET_NAME);
    }

    /**
     * Check if the Amazon S3 backup module is enabled.
     *
     * @return bool
     */
    public function isEnabled()
    {
        return Mage::getStoreConfigFlag(static::XML_PATH_AMAZON_ENABLED);
    }
}
