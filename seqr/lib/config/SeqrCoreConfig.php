<?php

include_once(dirname(__FILE__) . '/SeqrConfig.php');

/**
 * Class SeqrCoreConfig
 * Common config definition for the SEQR configuration.
 * This class should be extended by platform specific configuration class.
 */
abstract class SeqrCoreConfig implements SeqrConfig {

    private $userId = null;
    private $terminalId = null;
    private $terminalPass = null;
    private $wsdl = null;
    private $timeout = null;

    public function populate($params) {
        $this->userId = $params[SeqrConfig::SEQR_USER_ID];
        $this->terminalId = $params[SeqrConfig::SEQR_TERMINAL_ID];
        $this->terminalPass = $params[SeqrConfig::SEQR_TERMINAL_PASS];
        $this->timeout = $params[SeqrConfig::SEQR_PAYMENT_TIMEOUT];
        $this->wsdl = $params[SeqrConfig::SEQR_WSDL];
    }

    public function isValid() {
        return
            !empty($this->userId)
            && !empty($this->terminalId)
            && !empty($this->terminalPass)
            && !empty($this->timeout)
            && !empty($this->wsdl);
    }

    public function isDemoMode() {
        return trim($this->wsdl) === trim(SeqrConfig::SEQR_WSDL_DEMO);
    }

    public function getUserId() {
        return $this->userId;
    }

    public function getTerminalId() {
        return $this->terminalId;
    }

    public function getTerminalPass() {
        return $this->terminalPass;
    }

    public function getWsdl() {
        return $this->wsdl;
    }

    public function getTimeout() {
        return $this->timeout;
    }
}