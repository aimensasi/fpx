<?php

namespace Aimensasi\FPX;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Aimensasi\FPX\Skeleton\SkeletonClass
 */
class FPXFacade extends Facade {
	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() {
		return 'FPX';
	}
}
