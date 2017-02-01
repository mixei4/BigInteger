<?php
namespace MiXa\Math;

class BigInteger extends Number
{
	const baseDigits = 9;
	const base = 1000000000;
	private $number;
	protected $positive = true;
	
	/* 
	 * @param string $string 
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
	public function normalize()
	{
		$i = 0;
		while (($i < $this->length()))
		{
			if ($this->number[$i] >= self::base)
			{
				if (!isset($this->number[$i+1]))
				{
					$this->addDigit();
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
	
	public function isPositive()
	{
		return $this->positive;
	}
	
	/*
	 * Multiply current number by -1
	 */
	public function invert()
	{
		$this->positive = !$this->positive;
	}

	/*
	 * Multiply current number by -1 and return the new one
	 * 
	 * @return BigInteger Inverted number
	 */
	public function getInverted()
	{
		$result = clone $this;
		$result->invert();
		return $result;
	}

	/*
	 * Adds one digit to the Number
	 */
	protected function addDigit()
	{
		array_push($this->number, 0);
	}
	
	/*
	 * @param BigInteger $number
	 * @return boolean
	 */
	public function more(BigInteger $number)
	{
		if (!$this->isPositive() && !$number->isPositive())
		{
			return $this->getInverted()->less($number->getInverted());
		}
		else if (!$this->isPositive())
		{
			return false;
		}
		else if (!$number->isPositive())
		{
			return true;
		}
		else if ($this->length() > $number->length())
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
	 * @param BigInteger $number
	 * @return boolean
	 */
	public function less(BigInteger $number)
	{
		return $number->more($this);
	}
	
	/*
	 * @param BigInteger $number Number to be added
	 * @return BigInteger The sum of two numbers
	 */
	public function plus(BigInteger $number)
	{
		if (!$this->isPositive() && !$number->isPositive())
		{
			$result = $this->getInverted()->plus($number->getInverted());
			$result->invert();
			return $result;
		}
		else if (!$this->isPositive() || !$number->isPositive())
		{
			if ($this->isPositive())
			{
				return $this->minus($number->getInverted());
			}
			else
			{
				return $number->minus($this->getInverted());
			}
		}
		$result = clone $this;
		$i = 0;
		while ($i < $number->length())
		{
			if (!isset($result->number[$i]))
			{
				$result->addDigit();
			}
			$result->number[$i] = $result->number[$i] + $number->number[$i];
			if ($result->number[$i] >= self::base)
			{
				if (!isset($result->number[$i+1]))
				{
					$result->addDigit();
				}
				$result->number[$i+1] += floor($result->number[$i] / self::base);
				$result->number[$i] = $result->number[$i] % self::base;
			}
			$i++;
		}
		$result->normalize();
		return $result;
	}

	/*
	 * @param BigInteger $number Number to be subtracted
	 * @return BigInteger The difference between two numbers
	 */
	public function minus(BigInteger $number)
	{
		if (!$number->isPositive())
		{
			$result = $this->plus($number->getInverted());
			return $result;
		}
		else if (!$this->isPositive())
		{
			$result = $this->getInverted()->plus($number);
			$result->invert();
			return $result;
		}
		if ($number->more($this))
		{
			$result = $number->minus($this);
			$result->invert();
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
		$result->normalize();
		return $result;
	}
	
	/*
	 * Add $digits zeros to the end of the number
	 * 
	 * @param integer $digits
	 */
	public function shift($digits)
	{
		if ($digits > 0)
		{
			$result = new self($this . str_repeat('0', $digits));
			$this->number = $result->number;
		}
		else if ($digits < 0)
		{
			$s = strval($this);
			$digits = max(-strlen($s), $digits);
			$result = new self(substr($s, 0, strlen($s) + $digits));
			$this->number = $result->number;
		}
	}

	/*
	 * Add $digits zeros to the end of the number and return it
	 * 
	 * @param integer $digits
	 * @return BigInteger
	 */
	public function getShifted($digits)
	{
		$result = clone $this;
		$result->shift($digits);
		return $result;
	}
	
	/*
	 * @return integer The last digit
	 */
	public function getLastDigit()
	{
		return $this->number[0] % 10;
	}

}
