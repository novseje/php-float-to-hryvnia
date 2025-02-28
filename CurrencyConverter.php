<?php

class CurrencyConverter {
    private $ones = array(
        0 => 'нуль',
        1 => 'одна',
        2 => 'дві',
        3 => 'три',
        4 => 'чотири',
        5 => 'п’ять',
        6 => 'шість',
        7 => 'сім',
        8 => 'вісім',
        9 => 'дев’ять',
        10 => 'десять',
        11 => 'одинадцять',
        12 => 'дванадцять',
        13 => 'тринадцять',
        14 => 'чотирнадцять',
        15 => 'п’ятнадцять',
        16 => 'шістнадцять',
        17 => 'сімнадцять',
        18 => 'вісімнадцять',
        19 => 'дев’ятнадцять'
    );

    private $tens = array(
        2 => 'двадцять',
        3 => 'тридцять',
        4 => 'сорок',
        5 => 'п’ятдесят',
        6 => 'шістдесят',
        7 => 'сімдесят',
        8 => 'вісімдесят',
        9 => 'дев’яносто'
    );

    private $hundreds = array(
        1 => 'сто',
        2 => 'двісті',
        3 => 'триста',
        4 => 'чотириста',
        5 => 'п’ятсот',
        6 => 'шістсот',
        7 => 'сімсот',
        8 => 'вісімсот',
        9 => 'дев’ятсот'
    );

    private $thousands = array(
        1 => 'тисяча',
        2 => 'мільйон',
        3 => 'мільярд',
        4 => 'трильйон'
    );

    /**
     * Конвертує число з плаваючою комою в суму в гривнях з копійками та пише їх словами.
     *
     * @param float $amount Сума для конвертації.
     * @param bool $firstLetterUppercase Починати з великої літери
     * @return string Відформатована сума в гривнях з копійками словами.
     */
    public function convertToHryvniaWords(float $amount, $firstLetterUppercase = false): string {
        if (!is_numeric($amount)) {
            return "Некоректне значення";
        }

        $amount = round($amount, 2);
        $hryvnia = floor($amount);
        $coins = round(($amount - $hryvnia) * 100);

        $hryvniaWords = $this->numberToWords($hryvnia, 'гривня');
        $coinsWords = $this->numberToWords($coins, 'копійка');

        $string = "{$hryvniaWords} грн. {$coinsWords} коп";

        if ($firstLetterUppercase) {
            $string = $this->ucfirst($string);
        }

        return $string;
    }

    private function numberToWords(int $number, string $currency): string {
        if ($number == 0) {
            return 'нуль';
        }

        if ($number < 20) {
            return $this->ones[$number];
        }

        if ($number < 100) {
            $tensDigit = floor($number / 10);
            $onesDigit = $number % 10;
            return $this->tens[$tensDigit] . ($onesDigit > 0 ? ' ' . $this->ones[$onesDigit] : '');
        }

        if ($number < 1000) {
            $hundredsDigit = floor($number / 100);
            $remainder = $number % 100;
            return $this->hundreds[$hundredsDigit] . ($remainder > 0 ? ' ' . $this->numberToWords($remainder, $currency) : '');
        }

        $thousandsCounter = 0;
        $result = '';

        while ($number > 0) {
            $group = $number % 1000;
            if ($group > 0) {
                $result = $this->convertGroup($group, $thousandsCounter) . ($result ? ' ' . $result : '');
            }
            $number = floor($number / 1000);
            $thousandsCounter++;
        }

        return $result;
    }

    private function convertGroup(int $group, int $thousandsCounter): string {
        if ($group == 0) {
            return '';
        }

        $hundredsDigit = floor($group / 100);
        $remainder = $group % 100;

        $result = '';
        if ($hundredsDigit > 0) {
            $result .= $this->hundreds[$hundredsDigit] . ' ';
        }

        if ($remainder > 0) {
            if ($remainder < 20) {
                $result .= $this->ones[$remainder] . ' ';
            } else {
                $tensDigit = floor($remainder / 10);
                $onesDigit = $remainder % 10;
                $result .= $this->tens[$tensDigit] . ($onesDigit > 0 ? ' ' . $this->ones[$onesDigit] : '') . ' ';
            }
        }

        if ($thousandsCounter > 0) {
            $result .= $this->getThousandsSuffix($group, $thousandsCounter);
        }

        return trim($result);
    }

    private function getThousandsSuffix(int $group, int $thousandsCounter): string {
        $suffix = $this->thousands[$thousandsCounter];

        if ($thousandsCounter == 1) {
            if ($group % 10 == 1 && $group % 100 != 11) {
                $suffix = 'тисяча';
            } elseif ($group % 10 >= 2 && $group % 10 <= 4 && ($group % 100 < 10 || $group % 100 >= 20)) {
                $suffix = 'тисячі';
            } else {
                $suffix = 'тисяч';
            }
        } elseif ($thousandsCounter == 2) {
            if ($group % 10 == 1 && $group % 100 != 11) {
                $suffix = 'мільйон';
            } elseif ($group % 10 >= 2 && $group % 10 <= 4 && ($group % 100 < 10 || $group % 100 >= 20)) {
                $suffix = 'мільйони';
            } else {
                $suffix = 'мільйонів';
            }
        } elseif ($thousandsCounter == 3) {
            if ($group % 10 == 1 && $group % 100 != 11) {
                $suffix = 'мільярд';
            } elseif ($group % 10 >= 2 && $group % 10 <= 4 && ($group % 100 < 10 || $group % 100 >= 20)) {
                $suffix = 'мільярди';
            } else {
                $suffix = 'мільярдів';
            }
        }

        return $suffix;
    }

    function ucfirst(string $string): string {
        if (empty($string)) {
            return '';
        }

        $firstChar = mb_substr($string, 0, 1, 'UTF-8');
        $rest = mb_substr($string, 1, null, 'UTF-8');

        return mb_strtoupper($firstChar, 'UTF-8') . $rest;
    }

}
