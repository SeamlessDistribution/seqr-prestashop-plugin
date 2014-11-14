<?php

/**
 * The SEQR configuration definition which provides accessors to the configuration values
 * and methods to manage the configuration in the ecommerce platform.
 */
interface SeqrConfig {

    const SEQR_WSDL = 'SEQR_WSDL';
    const SEQR_WSDL_DEMO = 'https://extdev.seqr.com/extclientproxy/service/v2?wsdl';
    const SEQR_USER_ID = 'SEQR_USER_ID';
    const SEQR_TERMINAL_ID = 'SEQR_TERMINAL_ID';
    const SEQR_TERMINAL_PASS = 'SEQR_TERMINAL_PASS';
    const SEQR_PAYMENT_TIMEOUT = 'SEQR_PAYMENT_TIMEOUT';

    // Statuses
    const SEQR_PAYMENT_ISSUED = "ISSUED";
    const SEQR_PAYMENT_PAID = "PAID";
    const SEQR_PAYMENT_CANCELED = "CANCELED";
    const SEQR_PAYMENT_ERROR = "ERROR";

    /**
     * Populates configuration object with given values.
     * @param $params
     * @return mixed
     */
    public function populate($params);

    /**
     * Saves configuration in the database.
     * @return mixed
     */
    public function save();

    /**
     * Loads configuration from the database
     * @return mixed
     */
    public function load();

    /**
     * Method should be invoked on plugin installation.
     * Adds required and default configurations options.
     * @return mixed
     */
    public function install();

    /**
     * Triggered on plugin uninstall procedure.
     * Removes all configuration values from the database.
     * @return mixed
     */
    public function uninstall();

    /**
     * Verifies the module configuration.
     * @return mixed
     */
    public function isValid();

    /**
     * Checks if module is in demo mode.
     * If the DEMO server is used.
     * @return mixed
     */
    public function isDemoMode();

    /**
     * Gets user id from configuration.
     * @return mixed
     */
    public function getUserId();

    /**
     * Gets terminal id from configuration.
     * @return mixed
     */
    public function getTerminalId();

    /**
     * Gets password for terminal.
     * @return mixed
     */
    public function getTerminalPass();

    /**
     * Gets SEQR wsdl location.
     * @return mixed
     */
    public function getWsdl();

    /**
     * Gets the defined payment timeout.
     * @return mixed
     */
    public function getTimeout();

    /**
     * Returns url to the SEQR module.
     * @return mixed
     */
    public function getSeqrModuleUrl();

}