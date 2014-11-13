<?php

include_once(dirname(__FILE__).'/SeqrConfig.php');

abstract class SeqrCoreConfig implements SeqrConfig {

    private $userId = null;
    private $terminalId = null;
    private $terminalPass = null;
    private $mode = null;
    private $wsdl = null;
    private $timeout = null;

    public function populate($params)
    {
        $this->userId = $params[SeqrConfig::SEQR_USER_ID];
        $this->terminalId = $params[SeqrConfig::SEQR_TERMINAL_ID];
        $this->terminalPass = $params[SeqrConfig::SEQR_TERMINAL_PASS];
        $this->mode = $params[SeqrConfig::SEQR_MODE];
        $this->timeout = $params[SeqrConfig::SEQR_PAYMENT_TIMEOUT];
        $this->wsdl = $this->loadWsdl();
    }

    public function isValid()
    {
        return
            !empty($this->userId)
            && !empty($this->terminalId)
            && !empty($this->terminalPass)
            && !empty($this->mode)
            && !empty($this->timeout)
            && !empty($this->wsdl);
    }

    public function isDemoMode()
    {
       return $this->getMode() == SeqrConfig::SEQR_MODE_DEMO;
    }


    protected abstract function loadWsdl();

    public function getUserId()
    {
        return $this->userId;
    }

    public function getTerminalId()
    {
        return $this->terminalId;
    }

    public function getTerminalPass()
    {
        return $this->terminalPass;
    }

    public function getMode()
    {
        return $this->mode;
    }

    public function getWsdl()
    {
        return $this->wsdl;
    }

    public function getTimeout() {
        return $this->timeout;
    }


}