<?php
session_start();
require_once "../_includes/bootstrap.inc.php";

final class Page extends BaseDBPage
{

    public function __construct()
    {
        parent::__construct();
        $this->title = "Employee Detail";
    }

    protected function body(): string
    {
        $employee = EmployeeModel::getById(filter_input(INPUT_GET, "employee_id", FILTER_VALIDATE_INT));

        if ($_SESSION['logged']) {
            return $this->m->render(
                "employeeDetail",
                ["employee" => $employee]);
        } else {
            $return = "<a href='../login.php'><button type='button' class='btn btn-primary' style='float: '>Back to Login Page</button></a>";
            $return .= " You are not logged in";
            return $return;
        }
    }
}

(new Page())->render();