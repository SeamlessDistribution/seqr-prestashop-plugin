<?php

include_once(dirname(__FILE__) . '/../config/SeqrCoreConfig.php');


/**
 * Class PsConfig
 * Provides configuration definition and operations for the Prestashop platform.
 */
final class PsConfig extends SeqrCoreConfig
{

    const SEQR_HIDE_LEFT = "SEQR_HIDE_LEFT";
    const SEQR_HIDE_RIGHT = "SEQR_HIDE_RIGHT";

    public $hideLeftColumn = false;
    public $hideRightColumn = false;

    public function save()
    {
        return Configuration::updateValue(SeqrConfig::SEQR_USER_ID, $this->getUserId())
        && Configuration::updateValue(SeqrConfig::SEQR_TERMINAL_ID, $this->getTerminalId())
        && Configuration::updateValue(SeqrConfig::SEQR_TERMINAL_PASS, $this->getTerminalPass())
        && Configuration::updateValue(SeqrConfig::SEQR_PAYMENT_TIMEOUT, $this->getTimeout())
        && Configuration::updateValue(SeqrConfig::SEQR_WSDL, $this->getWsdl())
        && Configuration::updateValue(PsConfig::SEQR_HIDE_LEFT, $this->hideLeftColumn)
        && Configuration::updateValue(PsConfig::SEQR_HIDE_RIGHT, $this->hideRightColumn);
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

        $this->hideLeftColumn = Configuration::get(PsConfig::SEQR_HIDE_LEFT);
        $this->hideRightColumn = Configuration::get(PsConfig::SEQR_HIDE_RIGHT);
    }

    public function install()
    {
        return Configuration::updateValue(SeqrConfig::SEQR_WSDL, SeqrConfig::SEQR_WSDL_TEST)
        && Configuration::updateValue(SeqrConfig::SEQR_PAYMENT_TIMEOUT, 120) && $this->installDatabase();
    }

    public function uninstall()
    {
        return Configuration::deleteByName(SeqrConfig::SEQR_WSDL)
        && Configuration::deleteByName(SeqrConfig::SEQR_USER_ID)
        && Configuration::deleteByName(SeqrConfig::SEQR_TERMINAL_ID)
        && Configuration::deleteByName(SeqrConfig::SEQR_TERMINAL_PASS)
        && Configuration::deleteByName(SeqrConfig::SEQR_PAYMENT_TIMEOUT)
        && Configuration::deleteByName(PsConfig::SEQR_HIDE_LEFT)
        && Configuration::deleteByName(PsConfig::SEQR_HIDE_RIGHT);
    }

    public function getSeqrModuleUrl()
    {
        return Tools::getShopDomainSsl(true, true) . __PS_BASE_URI__ . 'module/' . $this->module->name;
    }

    private function installDatabase()
    {
        Db::getInstance()->execute(
            'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'seqr` (
              `id_transaction` int(11) NOT NULL AUTO_INCREMENT,
              `id_seqr` varchar(100) NOT NULL,
              `id_order` int(11) NOT NULL,
              `id_payment` int(11) NOT NULL,
              `amount` decimal(20,6) NOT NULL,
              `status` varchar(50) NOT NULL DEFAULT "PENDING",
              `refund` int(1) NOT NULL DEFAULT 0,
              `amount_refunded` decimal(20,6) DEFAULT 0,
              `time` int(11) NOT NULL,
              `qr_code` varchar(100) NOT NULL,
              `ers_reference` varchar(100) NOT NULL DEFAULT "",
              PRIMARY KEY (`id_transaction`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;');

        return true;
    }
}