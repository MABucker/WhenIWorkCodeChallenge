<?php

class WeeklyReport
{
    private $shifts = [];
    private $weekStart;
    private $weekEnd;

    public function __construct($shifts, $weekStart, $weekEnd)
    {
        $this->shifts = $shifts;
        $this->weekStart = new DateTime($weekStart);
        $this->weekEnd = new DateTime($weekEnd);
    }

    public function generateReport()
    {
        $employeeHours = [];

        foreach ($this->shifts as $shiftData) {
            $shift = new Shift(
                $shiftData['ShiftID'],
                $shiftData['EmployeeID'],
                $shiftData['StartTime'],
                $shiftData['EndTime']
            );

            if ($shift->isSplitAcrossWeeks($this->weekStart, $this->weekEnd)) {
                $regularHours = $shift->calculateHoursSplitAcrossWeeks($this->weekStart, $this->weekEnd);
                $invalidShifts = [$shift->getShiftID()];
            } else {
                $regularHours = $shift->calculateDurationInHours();
                $invalidShifts = ($shift->getEndTime() > $this->weekEnd || $shift->getStartTime() < $this->weekStart) ? [$shift->getShiftID()] : [];
            }

            $employeeID = $shift->getEmployeeID();
            $employeeHours[$employeeID]['RegularHours'] = ($employeeHours[$employeeID]['RegularHours'] ?? 0) + $regularHours;
            $employeeHours[$employeeID]['InvalidShifts'] = array_merge($employeeHours[$employeeID]['InvalidShifts'] ?? [], $invalidShifts);
        }

        $output = [];
        foreach ($employeeHours as $employeeID => $hoursData) {
            $regularHours = min(40, $hoursData['RegularHours']);
            $overtimeHours = max(0, $hoursData['RegularHours'] - 40);

            $output[] = [
                'EmployeeID' => $employeeID,
                'StartOfWeek' => $this->weekStart->format('Y-m-d'),
                'RegularHours' => round($regularHours, 2),
                'OvertimeHours' => round($overtimeHours, 2),
                'InvalidShifts' => array_unique($hoursData['InvalidShifts'] ?? [])
            ];
        }

        return $output;
    }

}

?>
