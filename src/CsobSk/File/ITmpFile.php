<?php

namespace Omnipay\CsobSk\File;

/**
 * Interface ktory platobne tlacidlo vyuziva vzdy ked potrebuje pouzit tmp subor. 
 * @package platobnetlacidlo
 */
interface ITmpFile
{

	/**
	 * Vrati nazov suboru do ktoreho sa moze zapisovat. 
	 * Volanie s rovnakym key musi vratit rovnaky nazov suboru. 
	 * @param string $key 
	 * @return string nazov temp. suboru
	 */
	public function getTempFileName($key);
}
