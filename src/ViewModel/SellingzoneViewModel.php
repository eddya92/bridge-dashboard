<?php
declare(strict_types=1);

namespace App\ViewModel;

/**
 * @param array $stores
 */
final class SellingzoneViewModel{
	public function __construct(private array $stores){
	}

	/**
	 * @return array
	 */
	public function getStore() : array{
		return $this->stores;
	}
}
