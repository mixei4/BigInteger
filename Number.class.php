<?php
namespace MiXa\Math;

abstract class Number
{
	abstract public function isPositive();
	abstract public function invert();

	/*
	 * @return Absolute value of the number
	 */
	public function abs()
	{
		$result = clone $this;
		if (!$result->isPositive())
		{
			$result->invert();
		}
		return $result;
	}
	
}