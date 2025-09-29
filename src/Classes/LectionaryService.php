<?php

namespace Bishopm\Methodist\Classes;

use Carbon\Carbon;
use Bishopm\Methodist\Models\Lection;
use Bishopm\Methodist\Models\Eastersunday;

class LectionaryService
{
    /**
     * Get all services (Sundays + special weekdays) for the week containing the given date.
     *
     * @param string|\DateTimeInterface $date
     * @return Lection[]  Array keyed by date (Y-m-d)
     */
    public function getReadingsForDate(string|\DateTimeInterface $date): array
    {
        $carbonDate = Carbon::parse($date);

        $year = $carbonDate->year;
        $easter = Eastersunday::where('year', $year)->firstOrFail()->eastersunday;
        $easter = Carbon::parse($easter);

        $liturgicalYear = $this->getLiturgicalYear($carbonDate);

        $services = $this->getServicesForWeek($carbonDate, $easter, $liturgicalYear);

        $readings = [];

        foreach ($services as $serviceDate => $serviceName) {
            $reading = Lection::where('year', strtolower($liturgicalYear))
                ->where('lection', $serviceName)
                ->first();

            if ($reading) {
                $readings[$serviceDate] = $reading;
            }
        }

        return $readings;
    }

    protected function getLiturgicalYear(Carbon $date): string
    {
        $advent1 = $this->getAdventSunday($date->year);
        $year = $date->lessThan($advent1) ? $date->year - 1 : $date->year;

        $cycle = ($year + 1) % 3;

        return match ($cycle) {
            1 => 'A',
            2 => 'B',
            0 => 'C',
        };
    }

    protected function getAdventSunday(int $year): Carbon
    {
        $date = Carbon::create($year, 11, 27);
        return $date->next(Carbon::SUNDAY);
    }

    /**
     * Return all services for the week containing $date (Sundays + special weekdays)
     *
     * @param Carbon $date
     * @param Carbon $easter
     * @param string $liturgicalYear
     * @return array  Keyed by date (Y-m-d) with service name
     */
    protected function getServicesForWeek(Carbon $date, Carbon $easter, string $liturgicalYear): array
    {
        $services = [];

        $year = $date->year;
        $christmas = Carbon::create($year, 12, 25);
        $ashWednesday = $easter->copy()->subDays(46);

        // Week boundaries (Sunday → Saturday)
        $weekStart = $date->copy()->startOfWeek(Carbon::SUNDAY);
        $weekEnd   = $date->copy()->endOfWeek(Carbon::SATURDAY);

        // --- Special weekdays ---

        // Epiphany
        $epiphany = Carbon::create($year, 1, 6);
        if ($epiphany->betweenIncluded($weekStart, $weekEnd)) {
            $services[$epiphany->toDateString()] = "Epiphany";
        }

        // Ash Wednesday
        if ($ashWednesday->betweenIncluded($weekStart, $weekEnd)) {
            $services[$ashWednesday->toDateString()] = "Ash Wednesday";
        }

        // Holy Week
        $maundyThursday = $easter->copy()->subDays(3);
        $goodFriday     = $easter->copy()->subDays(2);
        $holySaturday   = $easter->copy()->subDay();

        foreach ([
            $maundyThursday->toDateString() => "Maundy Thursday",
            $goodFriday->toDateString()     => "Good Friday",
            $holySaturday->toDateString()   => "Holy Saturday",
        ] as $d => $label) {
            if (Carbon::parse($d)->betweenIncluded($weekStart, $weekEnd)) {
                $services[$d] = $label;
            }
        }

        // Ascension Day (Thursday)
        $ascension = $easter->copy()->addDays(39);
        if ($ascension->betweenIncluded($weekStart, $weekEnd)) {
            $services[$ascension->toDateString()] = "Ascension Day";
        }

        // Christmas Eve & Christmas Day
        $christmasEve = $christmas->copy()->subDay();
        foreach ([
            $christmasEve->toDateString() => "Christmas Eve",
            $christmas->toDateString()    => "Christmas Day",
        ] as $d => $label) {
            if (Carbon::parse($d)->betweenIncluded($weekStart, $weekEnd)) {
                $services[$d] = $label;
            }
        }

        // --- Sunday of the week ---
        $sunday = $weekStart;
        $services[$sunday->toDateString()] = $this->getSundayTitle($sunday);

        // Sort by date ascending
        ksort($services);

        return $services;
    }

    /**
     * Return the proper Sunday title based on RCL
     *
     * @param Carbon $date
     * @return string
     */
    protected function getSundayTitle(Carbon $date): string
    {
        $year = $date->year;
        $easter = Eastersunday::where('year', $year)->firstOrFail()->eastersunday;
        $easter = Carbon::parse($easter);
        $christmas = Carbon::create($year, 12, 25);
        $ashWednesday = $easter->copy()->subDays(46);
        $advent1 = $this->getAdventSunday($year);

        // Easter
        if ($date->equalTo($easter)) return "Easter Sunday";
        if ($date->betweenIncluded($easter->copy()->addWeek(), $easter->copy()->addWeeks(6))) {
            $week = $easter->diffInWeeks($date) + 1;
            return $this->ordinal($week) . " Sunday of Easter";
        }
        if ($date->equalTo($easter->copy()->addWeeks(7))) return "Pentecost Sunday";
        if ($date->equalTo($easter->copy()->addWeeks(8))) return "Trinity Sunday";

        // Lent
        $lent1 = $ashWednesday->copy()->next(Carbon::SUNDAY);
        if ($date->betweenIncluded($lent1, $easter->copy()->subWeek())) {
            $week = $lent1->diffInWeeks($date) + 1;
            return $this->ordinal($week) . " Sunday in Lent";
        }
        if ($date->equalTo($easter->copy()->subWeek())) return "Palm Sunday";

        // Advent
        if ($date->betweenIncluded($advent1, $christmas->copy()->subDay())) {
            $week = $advent1->diffInWeeks($date) + 1;
            return $this->ordinal($week) . " Sunday of Advent";
        }

        // Christmas season
        if ($date->greaterThanOrEqualTo($christmas) && $date->lessThan($this->getAdventSunday($year + 1))) {
            if ($date->equalTo($christmas->copy()->addWeek())) return "First Sunday after Christmas";
            return "Christmas Season Sunday";
        }

        // Epiphany season
        $epiphany = Carbon::create($year, 1, 6);
        if (!$epiphany->isSunday()) $epiphany = $epiphany->next(Carbon::SUNDAY);
        if ($date->greaterThan($epiphany) && $date->lessThan($ashWednesday)) {
            $week = $epiphany->diffInWeeks($date);
            return $this->ordinal($week + 1) . " Sunday after Epiphany";
        }

        // Ordinary time (after Pentecost until Advent)
        $pentecost = $easter->copy()->addWeeks(7);
        if ($date->greaterThan($pentecost) && $date->lessThan($advent1)) {
            if ($date->equalTo($advent1->copy()->subWeek())) return "Christ the King Sunday";
            $week = $pentecost->diffInWeeks($date);
            return $this->ordinal($week) . " Sunday after Pentecost";
        }

        return "Ordinary Sunday";
    }

    /**
     * Convert number to ordinal string
     */
    private function ordinal(int $number): string
    {
        return match ($number) {
            1 => "First",
            2 => "Second",
            3 => "Third",
            4 => "Fourth",
            5 => "Fifth",
            6 => "Sixth",
            7 => "Seventh",
            8 => "Eighth",
            9 => "Ninth",
            10 => "Tenth",
            11 => "Eleventh",
            12 => "Twelfth",
            13 => "Thirteenth",
            14 => "Fourteenth",
            15 => "Fifteenth",
            default => $number . "th",
        };
    }
}
