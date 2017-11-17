<?php

class Organization
{
    protected $employees = [];

    public function addEmployee(Employee $employee)
    {
        $this->employees[] = $employee;
    }

    public function getNetSalaries()
    {
        $salaries = 0;
        foreach ($this->employees as & $employee) {
            $salaries += $employee->getSalary();
        }

        return $salaries;
    }
}
