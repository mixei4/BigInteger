<?php
namespace MiXa\Math;

abstract class Number
{
	protected $positive = true;

	/*
	 * Multiply current number by -1
	 */
	public function Invert()
	{
		$this->positive = !$this->positive;
	}

	/*
	 * Multiply current number by -1 and return the new one
	 * 
	 * @return BigInteger Inverted number
	 */
	public function GetInverted()
	{
		$result = clone $this;
		$result->Invert();
		return $result;
	}
	
}