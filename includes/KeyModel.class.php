<?php


final class KeyModel extends BaseModel
{

    protected string $dbTable = "`key`";
    protected string $primaryKeyName = "key_id";

    protected array $dbKeys = ["employee","room"];

    public int $room = 0;
    public int $employee = 0;


    public function isValid(): bool {
        if ($this->room === 0)
            return false;
        if ($this->employee === 0)
            return false;

        return true;
    }



}