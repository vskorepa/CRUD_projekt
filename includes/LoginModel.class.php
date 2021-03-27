<?php


final class LoginModel extends BaseModel
{

    protected string $dbTable = "employee";
    protected string $primaryKeyName = "employee_id";

    protected array $dbKeys = ["login", "password"];

    public string $login = "";
    public string $password = "";
    public int $employee_id=0;
    public bool $authenticated = false;

    public function isValid(): bool {
        if (!$this->login)
            return false;

        if (!$this->password)
            return false;

        return true;
    }

}