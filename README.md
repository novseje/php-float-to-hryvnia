
Клас на PHP який конвертує float число в суму в гривнях з копійками та пише її словами
___
# Usage

```php
// Приклад використання
$converter = new CurrencyConverter();
$amount = 123.456;
$result = $converter->convertToHryvniaWords($amount);
echo $result; // Виведе: сто двадцять три гривні сорок шість копійок

$amount2 = 50;
$result2 = $converter->convertToHryvniaWords($amount2);
echo $result2; // Виведе: п’ятдесят гривень нуль копійок
```