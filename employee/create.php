<?php

require_once "../_includes/bootstrap.inc.php";

final class Page extends BaseDBPage{

    const STATE_FORM_REQUESTED = 1;
    const STATE_DATA_SENT = 2;
    const STATE_REPORT_RESULT = 3;

    const RESULT_SUCCESS = 1;
    const RESULT_FAIL = 2;

    private EmployeeModel $employee;
    private int $state;
    private int $result;

    public function __construct()
    {
        parent::__construct();
        $this->title = "Employee listing";
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->getState();

        if ($this->state === self::STATE_REPORT_RESULT) {
            if ($this->result === self::RESULT_SUCCESS) {
                $this->title = "Employee created";
            } else {
                $this->title = "Employee creation failed";
            }
            return;
        }

        if ($this->state === self::STATE_DATA_SENT) {
            $this->employee = EmployeeModel::getFromPost();
            if ($this->employee->validate()) {
                if ($this->employee->insert()) {
                    $this->redirect(self::RESULT_SUCCESS);
                } else {
                    $this->redirect(self::RESULT_FAIL);
                }
            } else {
                $this->state = self::STATE_FORM_REQUESTED;
                $this->title = "Invalid data";
            }
        } else {
            $this->title = "Create new employee";
            $this->employee = new EmployeeModel();
        }

    }


    protected function body(): string {
        if ($this->state === self::STATE_FORM_REQUESTED) {
            return $this->m->render("employeeForm", [
                "room"=>$this->employee,
                "errors"=>$this->employee->getValidationErrors(),
                "create"=>true,
                "rooms"=>RoomModel::getAll(),
                "keys"=>RoomModel::getAll()
            ]);
        } elseif ($this->state === self::STATE_REPORT_RESULT) {
            if ($this->result === self::RESULT_SUCCESS) {
                return $this->m->render("reportSuccess", ["data"=>"Employee created successfully"]);
            } else {
                return $this->m->render("reportFail", ["data"=>"Employee creation failed. Please contact administrator or try again later."]);
            }

        }
    }

    private function getState() : void {
        //je u?? hotovo?
        $result = filter_input(INPUT_GET, "result", FILTER_VALIDATE_INT);
        if ($result === self::RESULT_SUCCESS) {
            $this->state = self::STATE_REPORT_RESULT;
            $this->result = self::RESULT_SUCCESS;
            return;
        } elseif ($result === self::RESULT_FAIL) {
            $this->state = self::STATE_REPORT_RESULT;
            $this->result = self::RESULT_FAIL;
            return;
        }

        //byl odesl??n formul????
        $action = filter_input(INPUT_POST, "action");
        if ($action === "create") {
            $this->state = self::STATE_DATA_SENT;
            return;
        }

        $this->state = self::STATE_FORM_REQUESTED;
    }

    private function redirect(int $result) : void {
        //odkaz s??m na sebe, bez query string atd.
        $location = strtok($_SERVER['REQUEST_URI'], '?');

        header("Location: {$location}?result={$result}");
        exit;
    }
}

(new Page())->render();