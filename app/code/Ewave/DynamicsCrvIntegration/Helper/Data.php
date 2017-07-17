<?php

namespace Ewave\DynamicsCrvIntegration\Helper;

/**
 * Class Data
 * @package Ewave\Emailblocker\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const GP_INTEGRATION_GL_PATH = 'ewave_gp_integration/general_ledger_transaction';
    const FTP_DIR = 'ftp_dir';
    const FTP_USERNAME = 'ftp_username';
    const FTP_PASSWORD = 'ftp_password';

    /**
     * @return string
     */
    public function getGlFtpPath()
    {
        return $this->scopeConfig->getValue(self::GP_INTEGRATION_GL_PATH . '/' . self::FTP_DIR);
    }

    /**
     * @return string
     */
    public function getGlFtpUsername()
    {
        return $this->scopeConfig->getValue(self::GP_INTEGRATION_GL_PATH . '/' . self::FTP_USERNAME);
    }

    /**
     * @return string
     */
    public function getGlFtpPassword()
    {
        return $this->scopeConfig->getValue(self::GP_INTEGRATION_GL_PATH . '/' . self::FTP_PASSWORD);
    }

    /**
     * @return string
     */
    public function getPxFtpPath()
    {
        return $this->scopeConfig->getValue(self::GP_INTEGRATION_GL_PATH . '/' . self::FTP_DIR);
    }

    /**
     * @return string
     */
    public function getPxFtpUsername()
    {
        return $this->scopeConfig->getValue(self::GP_INTEGRATION_GL_PATH . '/' . self::FTP_USERNAME);
    }

    /**
     * @return string
     */
    public function getPxFtpPassword()
    {
        return $this->scopeConfig->getValue(self::GP_INTEGRATION_GL_PATH . '/' . self::FTP_PASSWORD);
    }
}
