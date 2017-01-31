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
		$this->positive = $this->number->isPositive();
	}
	
	protected function isPositive()
	{
		return $this->number->isPositive();
	}
	
	/*
	 * Multiply current number by -1
	 */
	public function Invert()
	{
		$this->number->Invert();
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
	
	/*
	 * @param BigReal $number
	 * @return boolean
	 */
	public function More(BigReal $number)
	{
		//echo 'More'.$this.'!'.$number.'<br>';
		if (!$this->isPositive() && !$number->isPositive())
		{
			return $this->GetInverted()->Less($number->GetInverted());
		}
		else if (!$this->isPositive())
		{
			return false;
		}
		else if (!$number->isPositive())
		{
			return true;
		}
		else if ($this->shift != $number->shift)
		{
			$max = max($this->shift, $number->shift);
			return $this->number->GetShifted($max - $this->shift)->More($number->number->GetShifted($max - $number->shift));
		}
		else
		{
			return $this->number->More($number->number);
		}
	}
	
	/*
	 * @param BigReal $number
	 * @return boolean
	 */
	public function Less(BigReal $number)
	{
		//echo 'Less'.$this.'!'.$number.'<br>';
		return $number->More($this);
	}
	

}