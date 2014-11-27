<?php

include_once(dirname(__FILE__) . '/../config/SeqrCoreConfig.php');


/**
 * Class PsConfig
 * Provides configuration definition and operations for the Prestashop platform.
 */
final class PsConfig extends SeqrCoreConfig {

    const SEQR_HIDE_LEFT = "SEQR_HIDE_LEFT";
    const SEQR_HIDE_RIGHT = "SEQR_HIDE_RIGHT";

    public $hideLeftColumn = false;
    public $hideRightColumn = false;

    public function save() {
        return Configuration::updateValue(SeqrConfig::SEQR_USER_ID, $this->getUserId())
        && Configuration::updateValue(SeqrConfig::SEQR_TERMINAL_ID, $this->getTerminalId())
        && Configuration::updateValue(SeqrConfig::SEQR_TERMINAL_PASS, $this->getTerminalPass())
        && Configuration::updateValue(SeqrConfig::SEQR_PAYMENT_TIMEOUT, $this->getTimeout())
        && Configuration::updateValue(SeqrConfig::SEQR_WSDL, $this->getWsdl())
        && Configuration::updateValue(PsConfig::SEQR_HIDE_LEFT, $this->hideLeftColumn)
        && Configuration::updateValue(PsConfig::SEQR_HIDE_RIGHT, $this->hideRightColumn);
    }

    public function load() {
        $this->populate(
            array(
                SeqrConfig::SEQR_USER_ID => Configuration::get(SeqrConfig::SEQR_USER_ID),
                SeqrConfig::SEQR_TERMINAL_ID => Configuration::get(SeqrConfig::SEQR_TERMINAL_ID),
                SeqrConfig::SEQR_TERMINAL_PASS => Configuration::get(SeqrConfig::SEQR_TERMINAL_PASS),
                SeqrConfig::SEQR_PAYMENT_TIMEOUT => Configuration::get(SeqrConfig::SEQR_PAYMENT_TIMEOUT),
                SeqrConfig::SEQR_WSDL => Configuration::get(SeqrConfig::SEQR_WSDL)
            )
        );

        $this->hideLeftColumn = Configuration::get(PsConfig::SEQR_HIDE_LEFT);
        $this->hideRightColumn = Configuration::get(PsConfig::SEQR_HIDE_RIGHT);
    }

    public function install() {
        return Configuration::updateValue(SeqrConfig::SEQR_WSDL, SeqrConfig::SEQR_WSDL_DEMO)
        && Configuration::updateValue(SeqrConfig::SEQR_PAYMENT_TIMEOUT, 120);
    }

    public function uninstall() {
        return Configuration::deleteByName(SeqrConfig::SEQR_WSDL)
        && Configuration::deleteByName(SeqrConfig::SEQR_USER_ID)
        && Configuration::deleteByName(SeqrConfig::SEQR_TERMINAL_ID)
        && Configuration::deleteByName(SeqrConfig::SEQR_TERMINAL_PASS)
        && Configuration::deleteByName(SeqrConfig::SEQR_PAYMENT_TIMEOUT)
        && Configuration::deleteByName(PsConfig::SEQR_HIDE_LEFT)
        && Configuration::deleteByName(PsConfig::SEQR_HIDE_RIGHT);
    }

    public function getSeqrModuleUrl() {
        return Tools::getShopDomainSsl(true, true) . __PS_BASE_URI__ . 'module/' . $this->module->name;
    }

}