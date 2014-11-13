<?php

include_once(dirname(__FILE__).'/../common/SeqrCoreConfig.php');

class PsConfig extends SeqrCoreConfig {

    public function save()
    {
        return Configuration::updateValue(SeqrConfig::SEQR_USER_ID, $this->getUserId())
            && Configuration::updateValue(SeqrConfig::SEQR_TERMINAL_ID, $this->getTerminalId())
            && Configuration::updateValue(SeqrConfig::SEQR_TERMINAL_PASS, $this->getTerminalPass())
            && Configuration::updateValue(SeqrConfig::SEQR_PAYMENT_TIMEOUT, $this->getTimeout())
            && Configuration::updateValue(SeqrConfig::SEQR_MODE, $this->getMode());
    }

    public function load()
    {
        $this->populate(
            array(
                SeqrConfig::SEQR_USER_ID => Configuration::get(SeqrConfig::SEQR_USER_ID),
                SeqrConfig::SEQR_TERMINAL_ID => Configuration::get(SeqrConfig::SEQR_TERMINAL_ID),
                SeqrConfig::SEQR_TERMINAL_PASS => Configuration::get(SeqrConfig::SEQR_TERMINAL_PASS),
                SeqrConfig::SEQR_PAYMENT_TIMEOUT => Configuration::get(SeqrConfig::SEQR_PAYMENT_TIMEOUT),
                SeqrConfig::SEQR_MODE => Configuration::get(SeqrConfig::SEQR_MODE)
            )
        );
    }

    public function install()
    {
        return Configuration::updateValue(SeqrConfig::SEQR_DEMO_WSDL, 'https://extdev.seqr.com/extclientproxy/service/v2?wsdl')
        && Configuration::updateValue(SeqrConfig::SEQR_LIVE_WSDL, 'https://extdev.seqr.com/extclientproxy/service/v2?wsdl')
        && Configuration::updateValue(SeqrConfig::SEQR_MODULE_INSTALLED, SeqrConfig::SEQR_MODULE_INSTALLED)
        && Configuration::updateValue(SeqrConfig::SEQR_MODE, SeqrConfig::SEQR_MODE_LIVE)
        && Configuration::updateValue(SeqrConfig::SEQR_PAYMENT_TIMEOUT, 30);
    }

    function isInstalled()
    {
        return SeqrConfig::SEQR_MODULE_INSTALLED == Configuration::get(SeqrConfig::SEQR_MODULE_INSTALLED);
    }


    public function uninstall()
    {
        return Configuration::deleteByName(SeqrConfig::SEQR_MODE)
            && Configuration::deleteByName(SeqrConfig::SEQR_DEMO_WSDL)
            && Configuration::deleteByName(SeqrConfig::SEQR_LIVE_WSDL)
            && Configuration::deleteByName(SeqrConfig::SEQR_USER_ID)
            && Configuration::deleteByName(SeqrConfig::SEQR_TERMINAL_ID)
            && Configuration::deleteByName(SeqrConfig::SEQR_TERMINAL_PASS)
            && Configuration::deleteByName(SeqrConfig::SEQR_PAYMENT_TIMEOUT)
            && Configuration::deleteByName(SeqrConfig::SEQR_MODULE_INSTALLED);
    }

    protected function loadWsdl()
    {
        switch($this->getMode()) {
            case SeqrConfig::SEQR_MODE_DEMO: return Configuration::get(SeqrConfig::SEQR_DEMO_WSDL);
            case SeqrConfig::SEQR_MODE_LIVE: return Configuration::get(SeqrConfig::SEQR_LIVE_WSDL);
        }
    }

    public function getSeqrModuleUrl()
    {
        return Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'module/'.$this->module->name;
    }

}