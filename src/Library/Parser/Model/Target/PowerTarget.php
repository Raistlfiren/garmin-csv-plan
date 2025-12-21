<?php

namespace App\Library\Parser\Model\Target;

class PowerTarget extends AbstractTarget
{
    public const REGEX_WATTS = '/^(\d+)(?:\s*-\s*(\d+))?\s*(w|watts)$/i';
    public const REGEX_CP = '/^(\d+)(?:\s*-\s*(\d+))?\s*(%cp|pcp)$/i';

    public static function testPower($powerText, ?int $criticalPower = null): false|\App\Library\Parser\Model\Target\PowerTarget
    {
        $powerText = (string) $powerText;

        // First, try to match percentage-based power (%cp)
        if ($criticalPower > 0 && preg_match(self::REGEX_CP, $powerText, $matches)) {
            if (!isset($matches[1]) || empty($matches[1])) {
                return false;
            }

            $fromPercent = (int) $matches[1];
            $from = (int) round($criticalPower * ($fromPercent / 100));

            // If a range is provided (e.g., 90-95%cp), calculate the 'to' value.
            if (isset($matches[2]) && !empty($matches[2])) {
                $toPercent = (int) $matches[2];
                $to = (int) round($criticalPower * ($toPercent / 100));
            } else {
                $to = $from + 1;
            }

            return new PowerTarget($from, $to);
        }

        // If %cp doesn't match or isn't applicable, try to match absolute watts
        if (preg_match(self::REGEX_WATTS, $powerText, $matches)) {
            if (!isset($matches[1]) || empty($matches[1])) {
                return false;
            }

            $from = (int) $matches[1];
            
            if (isset($matches[2]) && !empty($matches[2])) {
                $to = (int) $matches[2];
            } else {
                $to = $from + 1;
            }

            return new PowerTarget($from, $to);
        }

        return false;
    }

    public function __construct(protected int $from, protected int $to)
    {
        // Ensure 'from' is always less than 'to'.
        if ($this->from > $this->to) {
            [$this->from, $this->to] = [$this->to, $this->from]; // Swap values
        }

    }

    protected function getTypeId(): int
    {
        return 2;
    }

    protected function getTypeKey(): string
    {
        return 'power.range';
    }

    protected function getTargetValueOne()
    {
        return $this->from;
    }

    protected function getTargetValueTwo()
    {
        return $this->to;
    }
}
