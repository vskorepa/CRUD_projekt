<?php


abstract class BaseDBPage extends BasePage
{
    protected ?PDO $pdo;

    protected function getID($name): int
    {
        $id = (int)($_GET["{$name}_id"] ?? 0);

        if (filter_input(INPUT_GET, "{$name}_id", FILTER_VALIDATE_INT)) {

            $query = "SELECT name FROM {$name} WHERE {$name}_id=?";
            $stmt = $this->pdo -> prepare($query);
            $stmt->execute([$id]);

            if (!$stmt->fetch(PDO::FETCH_ASSOC)){
                throw new RequestException(404);
            }


            return $id;
        }
        throw new RequestException(400);
    }

    protected function sortEmployees(){
        $sort_id = filter_input(INPUT_GET, "sort_id", FILTER_VALIDATE_INT);

        switch ($sort_id) {
            case 1:
                return  $this->employeeOrderBy("ename" ,"");
                break;
            case 2:
                return $this->employeeOrderBy("ename" ,"DESC");
                break;
            case 3:
                return $this->employeeOrderBy("surname" ,"");
                break;
            case 4:
                return $this->employeeOrderBy("surname" ,"DESC");
                break;
            case 5:
                return $this->employeeOrderBy("rname" ,"");
                break;
            case 6:
                return $this->employeeOrderBy("rname" ,"DESC");
                break;
            case 7:
                return $this->employeeOrderBy("phone" ,"");
                break;
            case 8:
                return $this->employeeOrderBy("phone" ,"DESC");
                break;
            case 9:
                return $this->employeeOrderBy("job" ,"");
                break;
            case 10:
                return $this->employeeOrderBy("job" ,"DESC");
                break;
            case 11:
                return $this->employeeOrderBy("login" ,"");
                break;
            case 12:
                return $this->employeeOrderBy("login" ,"DESC");
                break;
            default:
                return $this->pdo->prepare("SELECT employee.name AS ename, surname ,job, employee_id, room.name AS rname, room.phone, login , admin  FROM employee JOIN room ON room.room_id = employee.room");
                break;
        }

    }

    protected function sortRooms(){
        $sort_id = filter_input(INPUT_GET, "sort_id", FILTER_VALIDATE_INT);

        switch ($sort_id) {
            case 1:
                return $this->roomOrderBy("name","");
                break;
            case 2:
                return $this->roomOrderBy("name","DESC");
                break;
            case 3:
                return $this->roomOrderBy("phone","");
                break;
            case 4:
                return $this->roomOrderBy("phone","DESC");
                break;
            case 5:
                return $this->roomOrderBy("no","");
                break;
            case 6:
                return $this->roomOrderBy("no","DESC");
                break;
            default:
                return $this->pdo->prepare('SELECT * FROM room');
                break;
        }
    }


    protected function employeeOrderBy($order ,$how){

        if ($how === "DESC"){
            return $this->pdo->prepare("SELECT employee.name AS ename, surname ,job, employee_id, room.name AS rname, room.phone, login , admin  FROM employee JOIN room ON room.room_id = employee.room ORDER BY {$order} DESC");
        }
        return $this->pdo->prepare("SELECT employee.name AS ename, surname ,job, employee_id, room.name AS rname, room.phone, login , admin  FROM employee JOIN room ON room.room_id = employee.room ORDER BY {$order}");

    }
    protected function roomOrderBy($order ,$how){

        if ($how === "DESC"){
            return $this->pdo->prepare("SELECT * FROM `room` ORDER BY {$order} DESC");
        }
        return $this->pdo->prepare("SELECT * FROM `room` ORDER BY {$order} ");

    }



    protected function setUp(): void
    {
        parent::setUp();
        $this->pdo = DB::getConnection();
    }

}