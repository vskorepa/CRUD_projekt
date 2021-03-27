<?php
require "../includes/bootstrap.inc.php";


final class UpdateEmployeePage extends BaseCRUDPage {

    private EmployeeModel $employee;


    protected function setUp(): void
    {

        parent::setUp();

        if (!$this->sessionStorage->get("login")["authenticated"]) {
            $this->extraHeaders[] = "<meta http-equiv='refresh' content='5;url=../login.php'>";
            $this->title = "Přihlašte se";
        }
        else{

            if ($this->sessionStorage->get("login")["admin"] !== 1){
                $this->extraHeaders[] = "<meta http-equiv='refresh' content='1;url=./'>";

                header("Location: ./");
            }


            $this->state = $this->getState();

            if ($this->state === self::STATE_PROCESSED) {
                //je hotovo, reportujeme
                if ($this->result === self::RESULT_SUCCESS) {
                    $this->extraHeaders[] = "<meta http-equiv='refresh' content='5;url=./index.php'>";
                    $this->title = "Místnost upravena";
                } elseif ($this->result === self::RESULT_FAIL) {
                    $this->title = "Aktualizace místnosti selhala";
                }
            } elseif ($this->state === self::STATE_FORM_SENT) {
                //načíst data
                $this->employee = $this->readPost();
                //validovat data
                if ($this->employee->isValid()){
                    if ($this->findLogin()){

                        //uložit a přesměrovat
                        $token = bin2hex( random_bytes(20) );

                        //uložit a přesměrovat
                        if ($this->employee->update()) {
                            //přesměruj se zprávou "úspěch"
                            $this->sessionStorage->set($token, ['result' => self::RESULT_SUCCESS]);
                        } else {
                            //přesměruj se zprávou "neúspěch"
                            $this->sessionStorage->set($token, ['result' => self::RESULT_FAIL]);
                        }

                        $this->redirect($token);

                    }
                    else{
                        echo $this->m->render("FormHint",["login"=>" "]);
                        $this->state = self::STATE_FORM_REQUESTED;
                        $this->title = "Aktualizovat místnost : Neplatný formulář";
                    }


                } else {
                    //jít na formulář nebo
                    foreach ($this->employee as $key=>$value){
                        if ($value === '' || $value === 0 && $key !== "admin"){
                            echo $this->m->render("FormHint",["message"=>$key]);
                        }
                    }
                    $this->state = self::STATE_FORM_REQUESTED;
                    $this->title = "Aktualizovat místnost : Neplatný formulář";
                }
            } else {
                //přejít na formulář
                $this->title = "Aktualizovat místnost";
                $employee_id = $this->findId();
                if (!$employee_id)
                    throw new RequestException(400);
                if (!EmployeeModel::getById($employee_id))
                    throw new RequestException(404);
                $this->employee = EmployeeModel::getById($employee_id);
            }
//            $employee_id = $this->findId();
//            $this->employee = EmployeeModel::getById($employee_id);


        }




    }

    protected function body(): string
    {
        if ($this->sessionStorage->get("login")["authenticated"]) {

            if ($this->state === self::STATE_FORM_REQUESTED) {
                return $this->m->render("employeeForm", ['update' => true, 'employee' => $this->employee ,"myroom"=>$this->findMYRoom(),"rooms"=>$this->findOtherRooms(), "keys"=>$this->findKeys($this->findId())]);
            } elseif ($this->state === self::STATE_PROCESSED) {
                if ($this->result === self::RESULT_SUCCESS) {
                    return $this->m->render("employeeSuccess", ["message" => "Zaměstnanec byl úspěšně aktualizován."]);
                } elseif ($this->result === self::RESULT_FAIL) {
                    return $this->m->render("employeeFail", ["message" => "Aktualizace zaměstnance selhala"]);
                }
            }

        }else{
            $this->title = "Přihlašte se";
            return $this->m->render("notLogedFail" , ["root"=>"../login.php"]);

        }



    }

    protected function getState() : int {
        //rozpoznání processed
        if ($this->isProcessed())
            return self::STATE_PROCESSED;

        $action = filter_input(INPUT_POST, 'action');
        if ($action === 'update') {
            return self::STATE_FORM_SENT;
        }

        return self::STATE_FORM_REQUESTED;
    }

    private function findId() : ?int {
        $employee_id = filter_input(INPUT_GET, 'employee_id', FILTER_VALIDATE_INT);
        return $employee_id;
    }
    private function findLogin() : bool {
        $query = "SELECT login from employee WHERE employee_id != :id";
        $stmt = $this->pdo->prepare($query);
        $id = $this->findId();
        $stmt->bindParam(":id",$id);
        $stmt->execute();
        foreach ($stmt as $key=>$value){
            if ($value->login === $this->employee->login){
                return false;
            }
        }
        return true;
    }

    private function findMyRoom() : object {
        $pdo = DB::getConnection();
        $query = 'SELECT name, room_id FROM room WHERE room_id = :id';
        $stmt = $pdo -> prepare($query);
        $id = $this->employee->room;
        $stmt->bindParam(":id",$id );
        $stmt ->execute();
        return $stmt;
    }
    private function findOtherRooms() : object {
        $pdo = DB::getConnection();
        $query = 'SELECT name, room_id FROM room WHERE room_id != :id';
        $stmt = $pdo -> prepare($query);
        $id = $this->employee->room;
        $stmt->bindParam(":id",$id );
        $stmt ->execute();
        return $stmt;
    }


    private function readPost() : EmployeeModel {
        $employee = [];

        $employee['employee_id'] = filter_input(INPUT_POST, 'employee_id', FILTER_VALIDATE_INT);

        $employee['name'] = filter_input(INPUT_POST, 'name');
        $employee['surname'] = filter_input(INPUT_POST, 'surname');
        $employee['room'] = filter_input(INPUT_POST, 'room');
        $employee['job'] = filter_input(INPUT_POST, 'job');
        $employee['wage'] = filter_input(INPUT_POST, 'wage');
        $employee['login'] = filter_input(INPUT_POST, 'login');

        if (filter_input(INPUT_POST, 'newpassword') === ""){
            $employee['password'] = filter_input(INPUT_POST, 'password');
        }else{
            $hash = password_hash(filter_input(INPUT_POST, 'newpassword'),PASSWORD_DEFAULT);
            $employee['password'] = $hash;
        }
        $employee['admin'] = filter_input(INPUT_POST, 'admin');
        if ($employee['admin'] === null)$employee['admin'] = 0;
        return new EmployeeModel($employee);
    }

}

$page = new UpdateEmployeePage();
$page->render();

