<?php

namespace Bishopm\Methodist\Classes;

use Carbon\Carbon;
use Bishopm\Methodist\Models\Lection;
use Bishopm\Methodist\Models\Eastersunday;
use Illuminate\Support\Collection;

class LectionaryService
{
    protected Carbon $date;
    protected Carbon $easterSunday;
    protected int $year;

    /**
     * Get lectionary readings for a given date
     *
     * @param string|Carbon $date
     * @return array
     */
    public function getReadings($date): array
    {
        $this->date = $date instanceof Carbon ? $date : Carbon::parse($date);
        $this->year = $this->date->year;
        
        // Get Easter Sunday for the liturgical year
        $this->easterSunday = $this->getEasterSunday();
        
        // Get the Sunday for this date
        $sunday = $this->getSundayForDate($this->date);
        
        // Get the liturgical day name
        $liturgicalDay = $this->getLiturgicalDay($sunday);
        
        // Get readings for the Sunday
        $sundayReadings = $this->fetchReadings($liturgicalDay);
        
        // Get any special midweek services in the same week
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
     * Get Easter Sunday for the current liturgical year
     */
    protected function getEasterSunday(): Carbon
    {
        // Liturgical year starts with Advent (4 Sundays before Christmas)
        // Determine which Easter to use based on the date
        $year = $this->year;
        
        // If we're before Easter, we might be in the previous liturgical year
        $easter = Eastersunday::where('year', $year)->first();
        
        if ($easter) {
            $easterDate = Carbon::parse($easter->eastersunday);
            
            // If current date is before Easter, check if we need previous year's Easter
            if ($this->date->lt($easterDate) && $this->date->month < 6) {
                $prevEaster = Eastersunday::where('year', $year - 1)->first();
                if ($prevEaster) {
                    return Carbon::parse($prevEaster->eastersunday);
                }
            }
            
            return $easterDate;
        }
        
        throw new \Exception("Easter Sunday not found for year {$year}");
    }

    /**
     * Get the Sunday for a given date
     */
    protected function getSundayForDate(Carbon $date): Carbon
    {
        if ($date->isSunday()) {
            return $date->copy();
        }
        
        // Get the next Sunday
        return $date->copy()->next(Carbon::SUNDAY);
    }

    /**
     * Determine the liturgical day name based on the date
     */
    protected function getLiturgicalDay(Carbon $sunday): string
    {
        $month = $sunday->month;
        $day = $sunday->day;
        
        // Calculate days from Easter
        $daysFromEaster = $this->easterSunday->diffInDays($sunday, false);
        
        // Easter Season (Easter to Pentecost)
        if ($daysFromEaster === 0) {
            return 'Easter Sunday';
        } elseif ($daysFromEaster > 0 && $daysFromEaster < 49) {
            $week = intdiv($daysFromEaster, 7);
            return "Easter {$week}";
        } elseif ($daysFromEaster === 49) {
            return 'Pentecost';
        }
        
        // Trinity Sunday (first Sunday after Pentecost)
        if ($daysFromEaster >= 56 && $daysFromEaster < 63) {
            return 'Trinity Sunday';
        }
        
        // After Pentecost - Ordinary Time (date-based, following RCL)
        if ($daysFromEaster >= 63 || ($month >= 5 && $daysFromEaster > 49)) {
            // Use date ranges as per RCL
            if ($month === 5 && $day >= 24 && $day <= 28) return 'Ordinary 8';
            if ($month === 5 && $day >= 29 || ($month === 6 && $day <= 4)) return 'Ordinary 9';
            if ($month === 6 && $day >= 5 && $day <= 11) return 'Ordinary 10';
            if ($month === 6 && $day >= 12 && $day <= 18) return 'Ordinary 11';
            if ($month === 6 && $day >= 19 && $day <= 25) return 'Ordinary 12';
            if (($month === 6 && $day >= 26) || ($month === 7 && $day <= 2)) return 'Ordinary 13';
            if ($month === 7 && $day >= 3 && $day <= 9) return 'Ordinary 14';
            if ($month === 7 && $day >= 10 && $day <= 16) return 'Ordinary 15';
            if ($month === 7 && $day >= 17 && $day <= 23) return 'Ordinary 16';
            if ($month === 7 && $day >= 24 && $day <= 30) return 'Ordinary 17';
            if (($month === 7 && $day === 31) || ($month === 8 && $day <= 6)) return 'Ordinary 18';
            if ($month === 8 && $day >= 7 && $day <= 13) return 'Ordinary 19';
            if ($month === 8 && $day >= 14 && $day <= 20) return 'Ordinary 20';
            if ($month === 8 && $day >= 21 && $day <= 27) return 'Ordinary 21';
            if (($month === 8 && $day >= 28) || ($month === 9 && $day <= 3)) return 'Ordinary 22';
            if ($month === 9 && $day >= 4 && $day <= 10) return 'Ordinary 23';
            if ($month === 9 && $day >= 11 && $day <= 17) return 'Ordinary 24';
            if ($month === 9 && $day >= 18 && $day <= 24) return 'Ordinary 25';
            if (($month === 9 && $day >= 25) || ($month === 10 && $day <= 1)) return 'Ordinary 26';
            if ($month === 10 && $day >= 2 && $day <= 8) return 'Ordinary 27';
            if ($month === 10 && $day >= 9 && $day <= 15) return 'Ordinary 28';
            if ($month === 10 && $day >= 16 && $day <= 22) return 'Ordinary 29';
            if ($month === 10 && $day >= 23 && $day <= 29) return 'Ordinary 30';
            if (($month === 10 && $day >= 30) || ($month === 11 && $day <= 5)) return 'Ordinary 31';
            if ($month === 11 && $day >= 6 && $day <= 12) return 'Ordinary 32';
            if ($month === 11 && $day >= 13 && $day <= 19) return 'Ordinary 33';
            if ($month === 11 && $day >= 20 && $day <= 26) return 'Ordinary 34';
        }
        
        // Before Easter - Lent
        if ($daysFromEaster >= -42 && $daysFromEaster <= -1) {
            if ($daysFromEaster >= -7 && $daysFromEaster <= -1) {
                return 'Palm Sunday';
            }
            $week = intdiv(abs($daysFromEaster + 1), 7) + 1;
            if ($week >= 1 && $week <= 5) {
                return "Lent {$week}";
            }
        }
        
        // Transfiguration (last Sunday after Epiphany, before Lent)
        if ($daysFromEaster === -49) {
            return 'Transfiguration';
        }
        
        // Epiphany Season (January 6 to Transfiguration Sunday)
        $epiphany = Carbon::create($this->year, 1, 6);
        if ($sunday->gte($epiphany) && $daysFromEaster < -49) {
            if ($sunday->isSameDay($epiphany) || ($sunday->isAfter($epiphany) && $sunday->diffInDays($epiphany) < 7)) {
                return 'Baptism of the Lord';
            }
            $week = intdiv($epiphany->diffInDays($sunday), 7);
            if ($week > 0) {
                return "Ordinary {$week}";
            }
        }
        
        // Advent and Christmas
        $christmas = Carbon::create($this->year, 12, 25);
        $adventStart = $christmas->copy()->subDays(28)->previous(Carbon::SUNDAY);
        
        if ($sunday->gte($adventStart) && $sunday->lt($christmas)) {
            $weeksToChristmas = intdiv($christmas->diffInDays($sunday) + 6, 7);
            return "Advent " . (5 - $weeksToChristmas);
        }
        
        if ($sunday->between($christmas, $christmas->copy()->addDays(6))) {
            return 'Christmas';
        }
        
        if ($sunday->gt($christmas) && $sunday->lt($epiphany)) {
            return 'Christmas ' . intdiv($sunday->diffInDays($christmas), 7);
        }
        
        // Default
        return 'Ordinary Time';
    }

    /**
     * Fetch readings from the database
     */
    protected function fetchReadings(string $liturgicalDay): ?array
    {
        $lection = Lection::where('lection', $liturgicalDay)->first();
        
        if ($lection) {
            return [
                'old_testament' => $lection->ot ?? null,
                'psalm' => $lection->psalm ?? null,
                'epistle' => $lection->nt ?? null,
                'gospel' => $lection->gospel ?? null,
            ];
        }
        
        return null;
    }

    /**
     * Get midweek service readings for the week containing the Sunday
     */
    protected function getMidweekReadings(Carbon $sunday): array
    {
        $midweekServices = [];
        
        // Define special midweek services relative to the Sunday
        $specialDays = [
            'Ash Wednesday' => -46, // 46 days before Easter
            'Maundy Thursday' => -3,
            'Good Friday' => -2,
            'Holy Saturday' => -1,
        ];
        
        foreach ($specialDays as $dayName => $daysFromEaster) {
            $specialDate = $this->easterSunday->copy()->addDays($daysFromEaster);
            
            // Check if this special day falls in the same week as our Sunday
            $specialSunday = $this->getSundayForDate($specialDate);
            
            if ($specialSunday->eq($sunday)) {
                $readings = $this->fetchReadings($dayName);
                if ($readings) {
                    $midweekServices[] = [
                        'date' => $specialDate->toDateString(),
                        'day_name' => $dayName,
                        'readings' => $readings,
                    ];
                }
            }
        }
        
        return $midweekServices;
    }

    /**
     * Get the liturgical year (A, B, or C)
     * The liturgical year starts with Advent (roughly December 1)
     * and runs through the end of Ordinary Time the following calendar year
     */
    public function getLiturgicalYear(?Carbon $date = null): string
    {
        $checkDate = $date ?? $this->date;
        
        // Determine which year to use for the calculation
        // If we're in Advent (late November/December), the liturgical year is based on the NEXT year
        $liturgicalYear = $checkDate->year;
        
        // Check if we're in Advent of the previous calendar year
        // Advent starts 4 Sundays before December 25
        $christmas = Carbon::create($checkDate->year, 12, 25);
        $adventStart = $christmas->copy()->subDays(28)->previous(Carbon::SUNDAY);
        
        // If we're before Advent of this calendar year, use this year for calculation
        // If we're after Advent starts, use next year for calculation
        if ($checkDate->gte($adventStart)) {
            $liturgicalYear = $checkDate->year + 1;
        }
        
        $yearMod = $liturgicalYear % 3;
        
        return match($yearMod) {
            0 => 'A',
            1 => 'B',
            2 => 'C',
            default => 'A',
        };
    }
}