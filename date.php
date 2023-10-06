<?php
class ShamsiDateConverter
{
    // Function to convert a Gregorian date to a Shamsi date
    public static function gregorianToShamsi($gregorianDate)
    {
        $gregorianDateParts = explode('-', $gregorianDate);
        if (count($gregorianDateParts) != 3) {
            throw new Exception("Invalid Gregorian date format. Use YYYY-MM-DD.");
        }

        $gregorianYear = intval($gregorianDateParts[0]);
        $gregorianMonth = intval($gregorianDateParts[1]);
        $gregorianDay = intval($gregorianDateParts[2]);

        // Calculate the Julian Day Number (JDN) for the Gregorian date
        $julianDay = gregoriantojd($gregorianMonth, $gregorianDay, $gregorianYear);

        // Calculate the JDN for the vernal equinox for the given Gregorian year
        $vernalEquinox = gregoriantojd(3, 20, $gregorianYear);

        // Calculate the number of days between the two JDNs
        $daysDifference = $julianDay - $vernalEquinox - 1; // Subtract 1 day here

        // Calculate the Shamsi year
        if ($daysDifference < 0) {
            $shamsiYear = $gregorianYear - 622;
        } else {
            $shamsiYear = $gregorianYear - 621;
        }

        // Calculate the Shamsi month and day
        $shamsiMonth = 1;
        $shamsiDay = $daysDifference + 1;

        while ($shamsiMonth <= 12 && $shamsiDay > self::getShamsiMonthDays($shamsiYear, $shamsiMonth)) {
            $shamsiDay -= self::getShamsiMonthDays($shamsiYear, $shamsiMonth);
            $shamsiMonth++;
        }

        return sprintf('%04d-%02d-%02d', $shamsiYear, $shamsiMonth, $shamsiDay);
    }

    // Function to get the number of days in a Shamsi month
    private static function getShamsiMonthDays($shamsiYear, $shamsiMonth)
    {
        $shamsiMonthDays = [
            31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29
        ];

        if ($shamsiMonth == 12 && !self::isShamsiLeapYear($shamsiYear)) {
            $shamsiMonthDays[$shamsiMonth - 1] = 30;
        }

        return $shamsiMonthDays[$shamsiMonth - 1];
    }

    // Function to check if a Shamsi year is leap
    private static function isShamsiLeapYear($shamsiYear)
    {
        $a = ($shamsiYear - 474) % 128;
        $b = ($shamsiYear - 474) % 132;
        $c = ($shamsiYear - 474) % 29;

        return ($a == 0 || ($b == 0 && $c != 0));
    }
}

// Usage example
$gregorianDate = '2023-10-05';
$shamsiDate = ShamsiDateConverter::gregorianToShamsi($gregorianDate);
echo "Gregorian Date: $gregorianDate\n";
echo "Shamsi Date: $shamsiDate\n";
