<?php

namespace Bishopm\Methodist\Classes;

use Carbon\Carbon;
use Bishopm\Methodist\Models\Lection;
use Bishopm\Methodist\Models\Eastersunday;

class LectionaryService
{
    protected Carbon $date;
    protected Carbon $easterSunday;
    protected string $liturgicalYear;

    protected array $liturgicalAliases = [
        'Easter Sunday'       => ['Easter Sunday', 'Easter', 'Resurrection of the Lord'],
        'Pentecost'           => ['Pentecost', 'Day of Pentecost', 'Whitsunday'],
        'Trinity Sunday'      => ['Trinity Sunday', 'Holy Trinity'],
        'Ash Wednesday'       => ['Ash Wednesday'],
        'Maundy Thursday'     => ['Maundy Thursday', 'Holy Thursday'],
        'Good Friday'         => ['Good Friday', 'Holy Friday'],
        'Holy Saturday'       => ['Holy Saturday', 'Easter Vigil'],
        'Palm Sunday'         => ['Palm Sunday', 'Passion Sunday'],
        'Transfiguration'     => ['Transfiguration', 'Last Sunday after Epiphany'],
        'Monday Holy Week'    => ['Monday Holy Week'],
        'Tuesday Holy Week'   => ['Tuesday Holy Week'],
        'Wednesday Holy Week' => ['Wednesday Holy Week'],
    ];

    /**
     * Get lectionary readings for a given date
     */
    public function getReadings($date): array
    {
        $this->date = $date instanceof Carbon ? $date->copy()->startOfDay() : Carbon::parse($date)->startOfDay();
        $this->liturgicalYear = $this->getLiturgicalYear($this->date);

        // Determine Easter Sunday for this liturgical year
        $this->easterSunday = $this->getEasterSundayForLiturgicalYear();

        // Determine the Sunday for this date
        $sunday = $this->getSundayForDate($this->date);

        // Determine liturgical day
        $liturgicalDay = $this->getLiturgicalDay($sunday);

        // Get Sunday readings
        $sundayReadings = $this->fetchReadings($liturgicalDay);

        // Get midweek readings
        $midweekReadings = $this->getMidweekReadings($sunday);

        return [
            'date' => $this->date->toDateString(),
            'sunday' => $sunday->toDateString(),
            'liturgical_day' => $liturgicalDay,
            'sunday_readings' => $sundayReadings,
            'midweek_readings' => $midweekReadings,
        ];
    }

    /**
     * Determine Easter Sunday for this liturgical year
     */
    protected function getEasterSundayForLiturgicalYear(): Carbon
    {
        $easter = Eastersunday::where('year', $this->liturgicalYear)->first();
        if (!$easter) {
            throw new \Exception("Easter Sunday not found for liturgical year {$this->liturgicalYear}");
        }
        return Carbon::parse($easter->eastersunday)->startOfDay();
    }

    /**
     * Determine the Sunday for a given date
     */
    protected function getSundayForDate(Carbon $date): Carbon
    {
        return $date->isSunday() ? $date->copy()->startOfDay() : $date->copy()->next(Carbon::SUNDAY)->startOfDay();
    }

    /**
     * Determine the liturgical day for a Sunday
     */
    protected function getLiturgicalDay(Carbon $sunday): string
    {
        $sunday = $sunday->copy()->startOfDay();
        $easter = $this->easterSunday->copy()->startOfDay();
        $daysFromEaster = $sunday->diffInDays($easter, false);

        // Holy Week Monday–Saturday
        $holyWeek = [
            -6 => 'Monday Holy Week',
            -5 => 'Tuesday Holy Week',
            -4 => 'Wednesday Holy Week',
            -3 => 'Maundy Thursday',
            -2 => 'Good Friday',
            -1 => 'Holy Saturday',
        ];
        if (isset($holyWeek[$daysFromEaster])) {
            return $holyWeek[$daysFromEaster];
        }

        // Easter Sunday
        if ($daysFromEaster === 0) {
            return 'Easter Sunday';
        }

        // Easter season: Easter Monday → Pentecost Sunday (days 1–49)
        if ($daysFromEaster > 0 && $daysFromEaster < 49) {
            $week = intdiv($daysFromEaster, 7) + 1; // Easter 1, Easter 2, etc.
            return "Easter {$week}";
        }

        // Pentecost
        if ($daysFromEaster === 49) {
            return 'Pentecost';
        }

        // Trinity Sunday (first Sunday after Pentecost)
        if ($daysFromEaster >= 50 && $daysFromEaster < 56) {
            return 'Trinity Sunday';
        }

        // All other Sundays: Ordinary Time
        return 'Ordinary Time';
    }


    /**
     * Fetch readings from DB for a liturgical day
     */
    protected function fetchReadings(string $liturgicalDay): ?array
    {
        $lookupDays = $this->liturgicalAliases[$liturgicalDay] ?? [$liturgicalDay];
        $lection = Lection::whereIn('lection', $lookupDays)
            ->where('year', $this->liturgicalYear)
            ->first();

        if ($lection) {
            return [
                'old_testament' => $lection->ot ?? null,
                'psalm'         => $lection->psalm ?? null,
                'epistle'       => $lection->nt ?? null,
                'gospel'        => $lection->gospel ?? null,
            ];
        }

        return null;
    }

    /**
     * Get midweek readings (Holy Week, Ash Wednesday, etc.)
     */
    protected function getMidweekReadings(Carbon $sunday): array
    {
        $midweek = [];

        $specialDays = [
            'Ash Wednesday'       => -46,
            'Monday Holy Week'    => -6,
            'Tuesday Holy Week'   => -5,
            'Wednesday Holy Week' => -4,
            'Maundy Thursday'     => -3,
            'Good Friday'         => -2,
            'Holy Saturday'       => -1,
        ];

        foreach ($specialDays as $dayName => $offset) {
            $date = $this->easterSunday->copy()->addDays($offset);

            // Include only if in same ISO week as Sunday
            if ($date->isoWeek() === $sunday->isoWeek()) {
                $readings = $this->fetchReadings($dayName);
                if ($readings) {
                    $midweek[] = [
                        'date' => $date->toDateString(),
                        'day_name' => $dayName,
                        'readings' => $readings,
                    ];
                }
            }
        }

        // Sort by date
        usort($midweek, fn($a, $b) => strcmp($a['date'], $b['date']));

        return $midweek;
    }

    /**
     * Determine the liturgical year (A, B, C)
     */
    public function getLiturgicalYear(?Carbon $date = null): string
    {
        $date = $date ?? $this->date;

        // Advent start for the liturgical year
        $christmas = Carbon::create($date->year, 12, 25);
        $adventStart = $christmas->copy()->subDays(28)->previous(Carbon::SUNDAY);

        $adventYear = $date->lt($adventStart) ? $date->year - 1 : $date->year;

        // Known reference: Advent 2022 → liturgical year A
        $offset = $adventYear - 2022;

        return match($offset % 3) {
            0 => 'A',
            1 => 'B',
            2, -1 => 'C',
            default => 'A',
        };
    }
}
