<?php


abstract class BaseCRUDPage extends BaseDBPage
{

    const STATE_FORM_REQUESTED = 1;
    const STATE_FORM_SENT = 2;
    const STATE_PROCESSED = 3;
    const STATE_DELETE_REQUESTED = 4;

    const RESULT_SUCCESS = 1;
    const RESULT_FAIL = 2;

    const AUTHENTICATED = true;
    const NOT_AUTHENTICATED = false;

    protected int $state;
    protected int $result = 0;

    /**
     * BaseCRUDPage constructor.
     */
    public function __construct() {
        parent::__construct();
    }


    protected abstract function getState() : int;

    protected function redirect(string $token) : void {
        $location = strtok($_SERVER['REQUEST_URI'], '?');
        $query = http_build_query(['state' => self::STATE_PROCESSED, 'token' => $token]);
        header("Location: {$location}?$query");
        exit;
    }

    protected function isProcessed() : bool {
        $state = filter_input(INPUT_GET, 'state', FILTER_VALIDATE_INT);

        if ($state === self::STATE_PROCESSED) {
            $token = filter_input(INPUT_GET, 'token');

            if (!$this->sessionStorage->get($token))
                throw new RequestException(400);

            $result = $this->sessionStorage->get($token)['result'];

            if ($result === self::RESULT_SUCCESS) {
                $this->result = self::RESULT_SUCCESS;
                return true;
            } elseif ($result === self::RESULT_FAIL) {
                $this->result = self::RESULT_FAIL;
                return true;
            }

            throw new RequestException(400);
        }

        return false;
    }

    protected function findRooms() : object {
        $pdo = DB::getConnection();
        $query = 'SELECT name, room_id FROM room';
        $stmt = $pdo -> prepare($query);
        $stmt ->execute();
        return $stmt;
    }
    protected function findEmployees() : object {
        $pdo = DB::getConnection();
        $query = 'SELECT employee_id , name , surname  FROM employee';
        $stmt = $pdo -> prepare($query);
        $stmt ->execute();

        return $stmt;
    }
    protected function findKeys($employee_id) : object {
        $pdo = DB::getConnection();
        $query = 'SELECT `key`.room, room_id, `name`, key_id , employee FROM `key`, room WHERE employee= :id AND room_id = `key`.room';
        $stmt = $pdo -> prepare($query);
        $stmt->bindParam(":id",$employee_id);
        $stmt ->execute();
        return $stmt;
    }


}