<?php

class Shift
{
    private $shiftID;
    private $employeeID;
    private $startTime;
    private $endTime;

    public function __construct($shiftID, $employeeID, $startTime, $endTime)
    {
        $this->shiftID = $shiftID;
        $this->employeeID = $employeeID;
        $this->startTime = new DateTime($startTime);
        $this->endTime = new DateTime($endTime);
    }

    public function getEmployeeID()
    {
        return $this->employeeID;
    }

    public function getShiftID()
    {
        return $this->shiftID;
    }

    public function getStartTime()
    {
        return $this->startTime;
    }

    public function getEndTime()
    {
        return $this->endTime;
    }

    public function calculateDurationInHours()
    {
        $duration = $this->startTime->diff($this->endTime);
        return $duration->h + ($duration->days * 24);
    }

    public function isSplitAcrossWeeks($weekStart, $weekEnd)
    {
        return ($this->endTime > $weekEnd && $this->startTime < $weekStart);
    }

    public function calculateHoursSplitAcrossWeeks($weekStart, $weekEnd)
    {
        $splitHoursFirstPart = ($weekEnd->getTimestamp() - $this->startTime->getTimestamp()) / (60 * 60);
        $splitHoursFirstPart = max(0, $splitHoursFirstPart);

        $splitHoursSecondPart = ($this->endTime->getTimestamp() - $weekStart->getTimestamp()) / (60 * 60);
        $splitHoursSecondPart = max(0, $splitHoursSecondPart);

        return $splitHoursFirstPart + $splitHoursSecondPart;
    }
}

?>
