<?php

namespace MoneyManager;

class Money
{
    /**
     * @var string
     */
    protected $amount;

    /**
     * @var Currency
     */
    protected $currency;

    /**
     * @var int
     */
    protected static $defaulPrecision = 4;

    /**
     * @param  string                           $amount
     * @param  Currency $currency
     * @throws Exception\InvalidArgumentException
     */
    public function __construct($amount, Currency $currency)
    {
        $this->amount   = (string) $amount;
        $this->currency = $currency;
    }

    /**
     * Returns the monetary value represented by this object.
     *
     * @return string
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Returns the currency of the monetary value represented by this
     * object.
     *
     * @return Currency
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    public static function setDefaultPrecision($defaulPrecision)
    {
        self::$defaulPrecision = $defaulPrecision;
    }

    public static function getDefaultPrecision()
    {
        return self::$defaulPrecision;
    }

    /**
     * Returns a new Money object that represents the monetary value
     * of the sum of this Money object and another.
     *
     * @param  Money $other
     * @return Money
     * @throws Exception\CurrencyMismatchException
     */
    public function add(Money $other, $precision = null)
    {
        $this->assertSameCurrency($this, $other);
        $value = bcadd($this->amount, $other->getAmount(), $this->evalutePrecision($precision));

        return $this->newMoney($value);
    }

    /**
     * Returns a new Money object that represents the monetary value
     * of the difference of this Money object and another.
     *
     * @param  Money $other
     * @return Money
     * @throws Exception\CurrencyMismatchException
     */
    public function subtract(Money $other, $precision = null)
    {
        $this->assertSameCurrency($this, $other);
        $value = bcsub($this->amount, $other->getAmount(), $this->evalutePrecision($precision));

        return $this->newMoney($value);
    }

    /**
     * Returns a new Money object that represents the negated monetary value
     * of this Money object.
     *
     * @return Money
     */
    public function negate($precision = null)
    {
        return $this->newMoney(bcmul(-1, $this->amount, $this->evalutePrecision($precision)));
    }

    /**
     * Returns a new Money object that represents the monetary value
     * of this Money object multiplied by a given factor.
     *
     * @param  string|Money   $factor
     * @param  integer $roundingMode
     * @return Money
     */
    public function multiply($factor, $precision = null)
    {
        if ($factor instanceof Money) {
            $factor = $factor->getAmount();
        }

        return $this->newMoney(bcmul($factor, $this->amount, $this->evalutePrecision($precision)));
    }

    /**
     * Returns a new Money object that represents the monetary value
     * of this Money object multiplied by a given factor.
     *
     * @param  string|Money   $divide
     * @param  integer $roundingMode
     * @return Money
     */
    public function divide($divide, $precision = null)
    {
        if ($divide instanceof Money) {
            $divide = $divide->getAmount();
        }

        return $this->newMoney(bcdiv($this->amount, $divide, $this->evalutePrecision($precision)));
    }

    /**
     * Compares this Money object to another.
     *
     * Returns an integer less than, equal to, or greater than zero
     * if the value of this Money object is considered to be respectively
     * less than, equal to, or greater than the other Money object.
     *
     * @param  Money $other
     * @return integer -1|0|1
     * @throws Exception\CurrencyMismatchException
     */
    public function compareTo(Money $other)
    {
        $this->assertSameCurrency($this, $other);

        if ($this->amount == $other->getAmount()) {
            return 0;
        }

        return $this->amount < $other->getAmount() ? -1 : 1;
    }

    /**
     * Returns TRUE if this Money object equals to another.
     *
     * @param  Money $other
     * @return boolean
     * @throws Exception\CurrencyMismatchException
     */
    public function equals(Money $other)
    {
        return $this->compareTo($other) == 0;
    }

    /**
     * Returns TRUE if the monetary value represented by this Money object
     * is greater than that of another, FALSE otherwise.
     *
     * @param  Money $other
     * @return boolean
     * @throws Exception\CurrencyMismatchException
     */
    public function greaterThan(Money $other)
    {
        return $this->compareTo($other) == 1;
    }

    /**
     * Returns TRUE if the monetary value represented by this Money object
     * is greater than or equal that of another, FALSE otherwise.
     *
     * @param  Money $other
     * @return boolean
     * @throws Exception\CurrencyMismatchException
     */
    public function greaterThanOrEqual(Money $other)
    {
        return $this->greaterThan($other) || $this->equals($other);
    }

    /**
     * Returns TRUE if the monetary value represented by this Money object
     * is smaller than that of another, FALSE otherwise.
     *
     * @param  Money $other
     * @return boolean
     * @throws Exception\CurrencyMismatchException
     */
    public function lessThan(Money $other)
    {
        return $this->compareTo($other) == -1;
    }

    /**
     * Returns TRUE if the monetary value represented by this Money object
     * is smaller than or equal that of another, FALSE otherwise.
     *
     * @param  Money $other
     * @return boolean
     * @throws Exception\CurrencyMismatchException
     */
    public function lessThanOrEqual(Money $other)
    {
        return $this->lessThan($other) || $this->equals($other);
    }

    /**
     * @param  Money $a
     * @param  Money $b
     * @throws Exception\CurrencyMismatchException
     */
    private function assertSameCurrency(Money $a, Money $b)
    {
        if ($a->getCurrency() != $b->getCurrency()) {
            throw new Exception\CurrencyMismatchException;
        }
    }

    /**
     * @param  integer $amount
     * @return Money
     */
    private function newMoney($amount)
    {
        return new static($amount, $this->currency);
    }

    protected function evalutePrecision($precision)
    {
        return $precision !== null ? $precision : self::getDefaultPrecision();
    }
}
