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
    public function onResponse()
    {
        switch ($this->getEvent()->getCode()) {
        case Phergie_Event_Response::RPL_ENDOFMOTD:
        case Phergie_Event_Response::ERR_NOMOTD:
            $keys = null;
            if ($channels = $this->config['autojoin.channels']) {
                if (is_array($channels)) {
                    // Support autojoin.channels being in these formats:
                    // 'hostname' => array('#channel1', '#channel2', ... )
                    $host = $this->getConnection()->getHost();
                    if (isset($channels[$host])) {
                        $channels = $channels[$host];
                    }
                    if (is_array($channels)) {
                        $channels = implode(',', $channels);
                    }
                } elseif (strpos($channels, ' ') !== false) {
                    list($channels, $keys) = explode(' ', $channels);
                }

                $this->doJoin($channels, $keys);
            }
            $this->getPluginHandler()->removePlugin($this);
        }
    }
}
