<?php
namespace MiXa\Math;

class BigReal extends Number
{
	private $number;
	private $shift;
	
	/* 
	 * @param string $string 
	 */
	public function __construct($string)
	{
		if (preg_match("/^(-?[0-9]*)(\.?)([0-9]*)/", $string, $matches))
		{
			$stringLeft = $matches[1];
			if ($matches[2] === '.')
			{
				$stringRight = $matches[3];
			}
			else
			{
				$stringRight = '0';
			}
		}
		else
		{
			$stringLeft = '0';
			$stringRight = '0';
		}
		$this->number = new BigInteger($stringLeft.$stringRight);
		$this->shift = strlen($stringRight);
	}
	
	protected function isPositive()
	{
		return $this->number->isPositive();
	}
	
	/*
	 * @return Number as string
	 */
	public function __toString()
	{
		$result = $this->number->Abs();
		if ($this->shift > 0)
		{
			if ($this->shift >= strlen($result))
			{
				$result = '0.' . str_repeat('0', $this->shift - strlen($result)) . $result;
			}
			else
			{
				$result = substr_replace($result, '.', strlen($result) - $this->shift, 0);
			}
		}
		if (!$this->number->isPositive())
		{
			$result = '-'.$result;
		}
		return $result;
	}
	

}