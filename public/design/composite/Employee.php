<?php

interface Employee
{
    public function __construct($name, $salary);

    public function getName();

    public function getSalary();

    public function setSalary($salary);

    public function getRole();
}
