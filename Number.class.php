<?php
namespace MiXa\Math;

abstract class Number
{
	private $positive = true;

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
	
	abstract public function More(Number $number);
	
	/*
	 * @param Number $number Number
	 * @return boolean
	 */
	public function Less(Number $number)
	{
		return $number->More($this);
	}
	
	abstract public function Plus(Number $number);
	abstract public function Minus(Number $number);
	
}