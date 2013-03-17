<?php
/**
 * Responds to QUOTE requests from undernet servers.
 *
 * @category Phergie
 * @package  Phergie_Plugin_Quote
 * @author   Ryan Null <ryan@ryannull.com>
 * @license  http://phergie.org/license New BSD License
 * @link     http://pear.phergie.org/package/Phergie_Plugin_Pong
 */
class Phergie_Plugin_Quote extends Phergie_Plugin_Abstract {
    /**
     * Processes the Undernet '/QUOTE' Requirement so it can join the servers
     *
     * @return void
     */
    public function onNotice() {
        $event = $this->event;
        if ( $event->getArgument(0) == "AUTH" && preg_match("@QUOTE PASS ([0-9]+)@i", $event->getArgument(1), $match) ) {
                $this->doRaw('PASS ' . $match[1]);
        }
    }
}
