<?php
namespace Dunhakdis\Changelog;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Cache\Adapter\Filesystem\FilesystemCachePool;

class Cache {

	var $filesystemAdapter;
	var $filesystem;
	var $pool;

	public function __construct() {

		$this->filesystemAdapter = new Local(__DIR__.'/var');
		$this->filesystem = new Filesystem( $this->filesystemAdapter );
		$this->pool = new FilesystemCachePool( $this->filesystem );

		return $this;
	}

	public function setCache( $key, $value, $expiration ) {

		$item = $this->pool->getItem($key);
		// Set some values and store
		$item->set($value);
		$item->expiresAfter($expiration);

		$this->pool->save($item);

		return $this;
	}

	public function deleteCache( $key ){

		$this->pool->deleteItem( $key );

		return $this;
	}

	public function hasCache( $key ) {
		//$item = $this->pool->getItem($key);
		//$item->isHit();
		return $this->pool->hasItem($key);
	}

	public function getCache( $key ) {
		$item = $this->pool->getItem( $key );
		// Get stored values
		
		return $item->get();
	}
}