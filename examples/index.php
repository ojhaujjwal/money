<?php

include  __DIR__ . '/../vendor/autoload.php';

use MoneyManager\Money;
use MoneyManager\Currency;

$money = new Money(100, new Currency('NPR'));
echo $money->divide(new Money(50, new Currency('NPR')))->getAmount();
echo "\n";
