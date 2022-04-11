<?php
session_start();
require_once "../_includes/bootstrap.inc.php";

final class Page extends BaseDBPage{

    public function __construct()
    {
        parent::__construct();
        $this->title = "Employee listing";
    }

    protected function body() : string
    {
        if($_SESSION['logged']){
            if($_SESSION['admin'] == 1){
                return $this->m->render(
                    "employeeListAdmin",
                    ["employee" => EmployeeModel::getAll()]
                );
            } else {
                return $this->m->render(
                    "employeeList",
                    ["employee" => EmployeeModel::getAll()]
                );
            }
        } else {
            $return = "<a href='../login.php'><button type='button' class='btn btn-primary' style='float: '>Back to Login Page</button></a>";
            $return .= " You are not logged in";
            return $return;
        }
    }
}

(new Page())->render();