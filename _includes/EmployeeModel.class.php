<?php

class EmployeeModel
{
    public ?int $employee_id;
    public string $eName = "";
    public string $surname = "";
    public string $job = "";
    public int $wage;
    public string $room = "";
    public string $roomName = "";
    public ?string $login = "";
    public ?string $password = "";
    public int $admin = 0;
    public ?array $keys = [];

    public ?array $takeKeys = [];
    public ?array $keyNames = [];

    private array $validationErrors = [];

    public function getValidationErrors(): array
    {
        return $this->validationErrors;
    }

    public function __construct()
    {
    }

    public function insert() : bool {

        $sql = "INSERT INTO employee (name, surname, job, wage, room, login, password, admin) VALUES (:name, :surname, :job, :wage, :room, :login, :password, :admin)";

        $stmt = DB::getConnection()->prepare($sql);
        $stmt->bindParam(':name', $this->eName);
        $stmt->bindParam(':surname', $this->surname);
        $stmt->bindParam(':job', $this->job);
        $stmt->bindParam(':wage', $this->wage);
        $stmt->bindParam(':room', $this->room);
        $stmt->bindParam(':login', $this->login);
        $this->password = hash("sha256", $this->password);
        $stmt->bindParam(':password', $this->password);
        $stmt->bindParam(':admin', $this->admin);

        $sus = $stmt->execute();

        $sql = "SELECT employee_id FROM employee WHERE name=:name AND surname=:surname";
        $stmt = DB::getConnection()->prepare($sql);
        $stmt->bindParam(':name', $this->eName);
        $stmt->bindParam(':surname', $this->surname);
        $stmt->execute();

        $employeeId = $stmt->fetch()->employee_id;

        foreach ($this->keys as $row){
            $sql = "INSERT INTO `key` (employee, room) VALUES (:employee, :room)";
            $stmt = DB::getConnection()->prepare($sql);
            $stmt->bindParam(':employee',$employeeId);
            $stmt->bindParam(':room', $row);
            $amogus = $stmt->execute();
        }

        if($amogus && $sus) {
            return true;
        }else{return false;}
    }

    public function update() : bool
    {
        $sql = 'UPDATE employee SET name=:employeeName, surname=:surname, job=:job, wage=:wage, room=:room, login=:login, password=:password, admin=:admin WHERE employee_id=:employee_id';

        $stmt = DB::getConnection()->prepare($sql);
        $stmt->bindParam(':employee_id', $this->employee_id);
        $stmt->bindParam(':employeeName', $this->eName);
        $stmt->bindParam(':surname', $this->surname);
        $stmt->bindParam(':job', $this->job);
        $stmt->bindParam(':wage', $this->wage);
        $stmt->bindParam(':room', $this->room);
        $stmt->bindParam(':login', $this->login);
        $stmt->bindParam(':password', $this->password);
        $stmt->bindParam(':admin', $this->admin);

        $sus = $stmt->execute();

        $sql = "DELETE FROM `key` WHERE employee=:employee";
        $stmt = DB::getConnection()->prepare($sql);
        $stmt->bindParam(':employee', $this->employee_id);
        $stmt->execute();

        $sql = "SELECT employee_id FROM employee WHERE name=:name AND surname=:surname";
        $stmt = DB::getConnection()->prepare($sql);
        $stmt->bindParam(':name', $this->eName);
        $stmt->bindParam(':surname', $this->surname);
        $stmt->execute();

        $employeeId = $stmt->fetch()->employee_id;

        foreach ($this->keys as $row){
            $sql = "INSERT INTO `key` (employee, room) VALUES (:employee, :room)";
            $stmt = DB::getConnection()->prepare($sql);
            $stmt->bindParam(':employee', $employeeId->employee_id);
            $stmt->bindParam(':room', $row);
            $amogus = $stmt->execute();
        }

        if($amogus && $sus) {
            return true;
        }else{return false;}
    }

    public static function getById($employee_id) : ?self
    {
        $pdo = DB::getConnection();

        $stmt = $pdo->prepare("SELECT e.employee_id, e.name AS employeeName, e.surname, r.name AS roomName, r.phone, e.job, e.wage, e.room, e.password, e.login FROM employee AS e JOIN room AS r ON e.room=r.room_id  WHERE employee_id=:employee_id");
        $stmt->bindParam(':employee_id', $employee_id);
        $stmt->execute();

        $record = $stmt->fetch();

        $stmt = $pdo->prepare("SELECT * FROM `key` WHERE employee=:employee_id");
        $stmt->bindParam(':employee_id', $employee_id);
        $stmt->execute();

        $keyRecord = [];
        $keyNameRecord = [];

        foreach ($stmt as $item) {
            array_push($keyRecord,$item->room);
        }
        foreach ($keyRecord as $row){
            $stmt = $pdo->prepare("SELECT name FROM room WHERE room_id=:room_id");
            $stmt->bindParam(':room_id', $row);
            $stmt->execute();
            array_push($keyNameRecord, $stmt->fetch()->name);
        }

        if (!$record)
            return null;

        $model = new self();
        $model->employee_id = $record->employee_id;
        $model->eName = $record->employeeName;
        $model->surname = $record->surname;
        $model->job = $record->job;
        $model->wage = $record->wage;
        $model->room = $record->room;
        $model->roomName = $record->roomName;
        $model->login = $record->login;
        $model->password = $record->password;
        $model->takeKeys = $keyRecord;
        $model->keyNames = $keyNameRecord;

        return $model;



    }

    public static function getAll($orderBy = "surname", $orderDir = "ASC") : PDOStatement
    {
        $stmt = DB::getConnection()->prepare("SELECT e.employee_id, e.name AS 'employeeName', e.surname, r.name AS 'roomName', r.phone, e.job FROM `employee` AS e JOIN room AS r ON e.room=r.room_id ORDER BY `{$orderBy}` {$orderDir}");
        $stmt->execute();
        return $stmt;
    }

    public static function deleteById(int $employee_id) : bool
    {
        $sql = "DELETE FROM employee WHERE employee_id=:employee_id";
        $stmt = DB::getConnection()->prepare($sql);
        $stmt->bindParam(':employee_id', $employee_id);

        return $stmt->execute();
    }

    public function delete() : bool
    {
        return self::deleteById($this->employee_id);
    }

    public static function getFromPost() : self {
        $employee = new EmployeeModel();

        $employee->employee_id = filter_input(INPUT_POST, "employee_id", FILTER_VALIDATE_INT);
        $employee->eName = filter_input(INPUT_POST, "employeeName");
        $employee->surname = filter_input(INPUT_POST, "surname");
        $employee->job = filter_input(INPUT_POST, "job");
        $employee->wage = filter_input(INPUT_POST, "wage");
        $employee->room = filter_input(INPUT_POST, "room");
        $employee->login = filter_input(INPUT_POST, "login");
        $employee->password = filter_input(INPUT_POST, "password");

        if($_POST['keys'] === null){
            $_POST['keys'] = [];
        }

        $employee->keys = $_POST['keys'];

        return $employee;
    }

    public function validate() : bool {
        $isOk = true;
        $errors = [];

        if (!$this->eName){
            $isOk = false;
            $errors["employeeName"] = "Employee name cannot be empty";
        }
        if (!$this->surname){
            $isOk = false;
            $errors["surname"] = "Employee surname cannot be empty";
        }
        if (!$this->job){
            $isOk = false;
            $errors["job"] = "Employee job cannot be empty";
        }
        if (!$this->wage){
            $isOk = false;
            $errors["wage"] = "Employee wage cannot be empty";
        }
        if($this->wage<0){
            $isOk=false;
            $errors["wage"] = "Employee wage cannot be negative";
        }
        if (!$this->room){
            $isOk = false;
            $errors["room"] = "Employee room cannot be empty";
        }
        if (!$this->login){
            $isOk = false;
            $errors["login"] = "Employee login cannot be empty";
        }
        if (!$this->password){
            $isOk = false;
            $errors["password"] = "Employee password cannot be empty";
        }

        $this->validationErrors = $errors;
        return $isOk;
    }
}