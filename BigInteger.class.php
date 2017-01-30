<?php
namespace MiXa\Math;

class BigInteger
{
	const baseDigits = 9;
	const base = 1000000000;
	private $number;
	private $positive = true;
	
	/* 
	 * @param string $string 
	 * @param integer $baseDigits Digits count in base - 1..9. Default is 9.
	 */
	public function __construct($string)
	{
		if (preg_match("/^(-?)([0-9]+)/", $string, $matches))
		{
			$string = $matches[2];
			$this->positive = $matches[1] === '';
		}
		else
		{
			$string = '0';
		}
		$this->number = array();
		while (strlen($string) > 0)
		{
			$this->number[] = intval(substr($string, max(0, strlen($string) - self::baseDigits)));
			$string = substr($string, 0, max(0, strlen($string) - self::baseDigits));
		}
	}
	
	/*
	 * Count of the digits in number in used base.
	 */
	protected function length()
	{
		return count($this->number);
	}

	protected function isPositive()
	{
		return $this->positive;
	}
	
	/*
	 * Remove zeros from the front of the number
	 */
	protected function removeZeros()
	{
		$i = $this->length() - 1;
		while ($i > 0 && $this->number[$i] == 0)
		{
			array_pop($this->number);
			$i--;
		}
	}

	/*
	 * Bring number's digits to base boundaries
	 */
	protected function Normalize()
	{
		$i = 0;
		while (($i < $this->length()))
		{
			if ($this->number[$i] >= self::base)
			{
				if (!isset($this->number[$i+1]))
				{
					$this->AddDigit();
				}
				$this->number[$i+1] += floor($this->number[$i] / self::base);
				$this->number[$i] = $this->number[$i] % self::base;
			}
			else if ($this->number[$i] < 0)
			{
				$this->number[$i+1] -= ceil(-$this->number[$i] / self::base);
				$this->number[$i] += ceil(-$this->number[$i] / self::base) * self::base;
			}
			$i++;
		}
		$this->removeZeros();
	}
	
	/*
	 * @return Number as string
	 */
	public function __toString()
	{
		$i = $this->length() - 1;
		$result = strval($this->number[$i]);
		if (!$this->isPositive() && $result > 0)
		{
			$result = '-'.$result;
		}
		while ($i > 0)
		{
			$i--;
			$result .= str_pad($this->number[$i], 9, '0', STR_PAD_LEFT);
		}
		return $result;
	}
	
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
	
	/*
	 * Adds one digit to the Number.
	 */
	public function AddDigit()
	{
		array_push($this->number, 0);
	}
	
	/*
	 * @param BigInteger $number Number
	 * @return boolean
	 */
	public function More(BigInteger $number)
	{
		if ($this->length() > $number->length())
		{
			return true;
		}
		else if ($this->length() < $number->length())
		{
			return false;
		}
		for ($i = $this->length()-1; $i >= 0; $i--)
		{
			if ($this->number[$i] > $number->number[$i])
			{
				return true;
			}
			else if ($this->number[$i] < $number->number[$i])
			{
				return false;
			}
		}
		return false;
	}
	
	/*
	 * @param BigInteger $number Number to be added
	 * @return BigInteger The sum of two numbers
	 */
	public function Plus(BigInteger $number)
	{
		if (!$this->isPositive() && !$number->isPositive())
		{
			$result = $this->GetInverted()->Plus($number->GetInverted());
			$result->Invert();
			return $result;
		}
		else if (!$this->isPositive() || !$number->isPositive())
		{
			if ($this->isPositive())
			{
				return $this->Minus($number->GetInverted());
			}
			else
			{
				return $number->Minus($this->GetInverted());
			}
		}
		$result = clone $this;
		$i = 0;
		while ($i < $number->length())
		{
			if (!isset($result->number[$i]))
			{
				$result->AddDigit();
			}
			$result->number[$i] = $result->number[$i] + $number->number[$i];
			if ($result->number[$i] >= self::base)
			{
				if (!isset($result->number[$i+1]))
				{
					$result->AddDigit();
				}
				$result->number[$i+1] += floor($result->number[$i] / self::base);
				$result->number[$i] = $result->number[$i] % self::base;
			}
			$i++;
		}
		$result->Normalize();
		return $result;
	}

	/*
	 * @param BigInteger $number Number to be subtracted
	 * @return BigInteger The difference between two numbers
	 */
	public function Minus(BigInteger $number)
	{
		if (!$number->isPositive())
		{
			$result = $this->Plus($number->GetInverted());
			return $result;
		}
		else if (!$this->isPositive())
		{
			$result = $this->GetInverted()->Plus($number);
			$result->Invert();
			return $result;
		}
		if ($number->More($this))
		{
			$result = $number->Minus($this);
			$result->Invert();
			return $result;
		}
		$result = clone $this;
		$i = 0;
		while ($i < $number->length())
		{
			$result->number[$i] = $result->number[$i] - $number->number[$i];
			if ($result->number[$i] < 0)
			{
				$result->number[$i+1] -= ceil(-$result->number[$i] / self::base);
				$result->number[$i] += ceil(-$result->number[$i] / self::base) * self::base;
			}
			$i++;
		}
		$result->Normalize();
		return $result;
	}

}
?>