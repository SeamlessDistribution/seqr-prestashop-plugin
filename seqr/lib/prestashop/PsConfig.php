<?php

include_once(dirname(__FILE__).'/../config/SeqrCoreConfig.php');


/**
 * Class PsConfig
 * Provides configuration definition and operations for the Prestashop platform.
 */
final class PsConfig extends SeqrCoreConfig {

    public function save()
    {
        return Configuration::updateValue(SeqrConfig::SEQR_USER_ID, $this->getUserId())
            && Configuration::updateValue(SeqrConfig::SEQR_TERMINAL_ID, $this->getTerminalId())
            && Configuration::updateValue(SeqrConfig::SEQR_TERMINAL_PASS, $this->getTerminalPass())
            && Configuration::updateValue(SeqrConfig::SEQR_PAYMENT_TIMEOUT, $this->getTimeout())
            && Configuration::updateValue(SeqrConfig::SEQR_WSDL, $this->getWsdl());
    }

    public function load()
    {
        $this->populate(
            array(
                SeqrConfig::SEQR_USER_ID => Configuration::get(SeqrConfig::SEQR_USER_ID),
                SeqrConfig::SEQR_TERMINAL_ID => Configuration::get(SeqrConfig::SEQR_TERMINAL_ID),
                SeqrConfig::SEQR_TERMINAL_PASS => Configuration::get(SeqrConfig::SEQR_TERMINAL_PASS),
                SeqrConfig::SEQR_PAYMENT_TIMEOUT => Configuration::get(SeqrConfig::SEQR_PAYMENT_TIMEOUT),
                SeqrConfig::SEQR_WSDL => Configuration::get(SeqrConfig::SEQR_WSDL)
            )
        );
    }

    public function install()
    {
        return Configuration::updateValue(SeqrConfig::SEQR_WSDL, SeqrConfig::SEQR_WSDL_DEMO)
        && Configuration::updateValue(SeqrConfig::SEQR_MODULE_INSTALLED, SeqrConfig::SEQR_MODULE_INSTALLED)
        && Configuration::updateValue(SeqrConfig::SEQR_PAYMENT_TIMEOUT, 120);
    }

    function isInstalled()
    {
        return SeqrConfig::SEQR_MODULE_INSTALLED == Configuration::get(SeqrConfig::SEQR_MODULE_INSTALLED);
    }

    public function uninstall()
    {
        return Configuration::deleteByName(SeqrConfig::SEQR_WSDL)
            && Configuration::deleteByName(SeqrConfig::SEQR_USER_ID)
            && Configuration::deleteByName(SeqrConfig::SEQR_TERMINAL_ID)
            && Configuration::deleteByName(SeqrConfig::SEQR_TERMINAL_PASS)
            && Configuration::deleteByName(SeqrConfig::SEQR_PAYMENT_TIMEOUT)
            && Configuration::deleteByName(SeqrConfig::SEQR_MODULE_INSTALLED);
    }

    public function getSeqrModuleUrl()
    {
        return Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'module/'.$this->module->name;
    }

}