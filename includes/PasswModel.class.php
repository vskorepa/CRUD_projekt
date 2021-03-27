<?php


final class PasswModel extends BaseModel
{

    protected string $dbTable = "employee";
    protected string $primaryKeyName = "employee_id";

    protected array $dbKeys = [ "oldpassw", "password","checkpassw"];

    public string $password = "";
    public string $oldpassw = "";
    public string $checkpassw = "";
    public int $id = 0;


    public function isValid(): bool {
        if (!$this->oldpassw)
            return false;
        if (!$this->password)
            return false;
        if (!$this->checkpassw)
            return false;
        if ($this->password)
        if ($this->password !== $this->checkpassw){
            return false;
        }

        return true;
    }

}