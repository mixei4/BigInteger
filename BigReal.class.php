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
	
	public function isPositive()
	{
		return $this->number->isPositive();
	}
	
	/*
	 * Multiply current number by -1
	 */
	public function invert()
	{
		$this->number->invert();
	}

	/*
	 * Multiply current number by -1 and return the new one
	 * 
	 * @return BigInteger Inverted number
	 */
	public function getInverted()
	{
		$result = clone $this;
		$result->Invert();
		return $result;
	}

	/*
	 * Bring number's digits to base boundaries and remove zeros after the delimiter
	 */
	public function normalize()
	{
		$this->number->normalize();
		$shiftCount = 0;
		while ($this->shift > 0 && $this->number->getLastDigit() === 0)
		{
			$this->shift--;
			$shiftCount--;
		}
		$this->number->getShifted($shiftCount);
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
	public function more(BigReal $number)
	{
		//echo 'More'.$this.'!'.$number.'<br>';
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
		else if ($this->shift != $number->shift)
		{
			$max = max($this->shift, $number->shift);
			return $this->number->getShifted($max - $this->shift)->more($number->number->getShifted($max - $number->shift));
		}
		else
		{
			return $this->number->more($number->number);
		}
	}
	
	/*
	 * @param BigReal $number
	 * @return boolean
	 */
	public function less(BigReal $number)
	{
		//echo 'Less'.$this.'!'.$number.'<br>';
		return $number->more($this);
	}

	/*
	 * @param BigReal $number Number to be added
	 * @return BigReal The sum of two numbers
	 */
	public function plus(BigReal $number)
	{
		if (!$this->isPositive() && !$number->isPositive())
		{
			echo '1.'.$this.'!'.$number.'<br>';
			$result = $this->getInverted()->plus($number->getInverted());
			$result->invert();
			return $result;
		}
		else if (!$this->isPositive() || !$number->isPositive())
		{
			echo '2.'.$this.'!'.$number.'<br>';
			if ($this->isPositive())
			{
				return $this->minus($number->getInverted());
			}
			else
			{
				return $number->minus($this->getInverted());
			}
		}
		else if ($this->shift != $number->shift)
		{
			echo '3.'.$this.'!'.$number.'<br>';
			if ($this->shift < $number->shift)
			{
				$t = clone $this;
				$t->number->shift($number->shift - $this->shift);
				$t->shift += $number->shift - $this->shift;
				return $t->Plus($number);
			}
			else if ($this->shift > $number->shift)
			{
				$t = clone $number;
				$t->number->shift($this->shift - $number->shift);
				$t->shift += $this->shift - $number->shift;
				return $this->Plus($t);
			}
		}
		echo '4.'.$this.'!'.$number.'<br>';
		$result = clone $this;
		$result->number = $result->number->plus($number->number);
		return $result;
	}

	/*
	 * @param BigReal $number Number to be subtracted
	 * @return BigReal The difference between two numbers
	 */
	public function minus(BigReal $number)
	{
		if (!$number->isPositive())
		{
			echo '5.'.$this.'!'.$number.'<br>';
			$result = $this->plus($number->getInverted());
			return $result;
		}
		else if (!$this->isPositive())
		{
			echo '6.'.$this.'!'.$number.'<br>';
			$result = $this->getInverted()->plus($number);
			$result->invert();
			return $result;
		}
		else if ($this->shift != $number->shift)
		{
			echo '9.'.$this.'!'.$number.'<br>';
			if ($this->shift < $number->shift)
			{
				$t = clone $this;
				$t->number->shift($number->shift - $this->shift);
				$t->shift += $number->shift - $this->shift;
				return $t->Minus($number);
			}
			else if ($this->shift > $number->shift)
			{
				$t = clone $number;
				$t->number->shift($this->shift - $number->shift);
				$t->shift += $this->shift - $number->shift;
				return $this->Minus($t);
			}
		}
		else if ($number->more($this))
		{
			echo '7.'.$this.'!'.$number.'<br>';
			$result = $number->minus($this);
			$result->invert();
			return $result;
		}
		echo '8.'.$this.'!'.$number.'<br>';
		$result = clone $this;
		$result->number = $result->number->minus($number->number);
		return $result;
	}
}