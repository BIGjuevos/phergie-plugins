<?php
/**
 * Watches a p2pool server and drops alerts
 *
 * @category Phergie
 * @package  Phergie_Plugin_P2Pool
 * @author   Ryan Null <ryan@ryannull.com>
 * @license  http://phergie.org/license New BSD License
 * @link     http://pear.phergie.org/package/Phergie_Plugin_
 */
class Phergie_Plugin_P2Pool extends Phergie_Plugin_Abstract {
	private $_nextBlockCheck = 0;
	private $_nextSpeedCheck = 0;

	/**
	 * Let's do some magic
	 *
	 * @return void
	 */
	public function onTick() {
		$time = time();

		try {

		if ( $this->_nextBlockCheck <= $time ) {
			$this->checkBlocks();
			$this->_nextBlockCheck = time() + $this->getConfig('p2pool.blocks_interval');
		}

		if ( $this->_nextSpeedCheck <= $time ) {
			$this->checkSpeed();
			$this->_nextSpeedCheck = time() + $this->getConfig('p2pool.speed_interval');
		}

		} catch (Exception $e) {
			//well, something went wrong, catch it so phergie doesn't die
		}
	}

	public function onPrivMsg() {
		switch ( $this->getEvent()->getArgument(1) ) {
			case "!hashrate":
				//set to zero and let the tick manager take care of it
				$this->_nextSpeedCheck = 0;
				break;
		}
	}

	private function checkBlocks() {
		$pools = $this->getConfig('p2pool.pools');
		foreach ($pools as $currency => $url) {
			$blocks = file_get_contents( $url . "recent_blocks");

			$blocks = json_decode($blocks);

			if ( !file_exists( $this->getConfig('p2pool.storage') . "phergie-p2pool.dat" ) ) {
				touch( $this->getConfig('p2pool.storage') . "phergie-p2pool.dat" );
			}
			$existingBlocks = explode("\n", file_get_contents( $this->getConfig('p2pool.storage') . "phergie-p2pool.dat") );

			foreach ( $blocks as $block ) {
				if ( !in_array($block->hash, $existingBlocks) ) {
					$this->doPrivMsg($this->getConfig('p2pool.channel'), 
							"NEW BLOCK $currency [{$block->number}] has been found!");
					$existingBlocks[] = $block->hash;
				}
			}

			file_put_contents($this->getConfig('p2pool.storage') . "phergie-p2pool.dat", implode("\n", $existingBlocks) );
		}
	}

	private function checkSpeed() {
		$pools = $this->getConfig('p2pool.pools');
		foreach ($pools as $currency => $url ) {
			$stats = file_get_contents( $url . "global_stats");
			$stats = json_decode($stats);

			$localStats = file_get_contents( $url . "local_stats");
			$localStats = json_decode($localStats);

			$localRate = 0;
			foreach ($localStats->miner_hash_rates as $rate) {
				$localRate += $rate;
			}

			$this->doPrivMsg( $this->getConfig('p2pool.channel'),
					"$currency " . 
					"Pool Hash Rate: " . round($stats->pool_hash_rate / 1000, 2) . "kH/s  " .
					"Local Hash Rate: " . round($localRate / 1000, 2) . "kH/s  " .
					"Efficiency Rate: " . round($localStats->efficiency, 2) );
		}
	}
}
