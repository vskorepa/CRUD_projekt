<?php


final class EmployeeModel extends BaseModel
{

    protected string $dbTable = "employee";
    protected string $primaryKeyName = "employee_id";

    protected array $dbKeys = ["name", "surname","wage","job","login","password","admin","room"];

    public string $name = "";
    public string $surname = "";
    public int $wage = 0;
    public string $job = "";
    public string $login = "";
    public string $password = "";
    public int $admin = 0;
    public int $room = 1;



    public function isValid(): bool {
        if (!$this->name)
            return false;

        if (!$this->surname)
            return false;

        if (!$this->job)
            return false;

        if (!$this->wage)
            return false;

        if (!$this->login)
            return false;

        if (!$this->password)
            return false;



        return true;
    }

}