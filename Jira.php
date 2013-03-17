<?php
/**
 * Phergie Jira Plugin
 *
 * PHP version 5
 *
 * LICENSE
 *
 * You can do whatever you want with this file.
 *
 * @category  Phergie
 * @package   Phergie_Plugin_Jira
 * @author    Ryan Null <ryan@ryannull.com>
 * @link      https://github.com/BIGjuevos/phergie-plugins
 */

/**
 * Provides multiple commands to interface with a JIRA system through
 * phergie
 *
 * @category Phergie
 * @package  Phergie_Plugin_Jira
 * @author    Ryan Null <ryan@ryannull.com>
 * @link      https://github.com/BIGjuevos/phergie-plugins
 */
class Phergie_Plugin_Jira extends Phergie_Plugin_Abstract {
    /**
     * Intercepts the end of the "message of the day" response and responds by
     * joining the channels specified in the configuration file.
     *
     * @return void
     */
    public function onResponse() {
    }
}
