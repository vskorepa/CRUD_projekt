<?php
require "../includes/bootstrap.inc.php";



final class CreateEmployeePage extends BaseCRUDPage {

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
                header("Location: ./");
            }


            $this->state = $this->getState();

            if ($this->state === self::STATE_PROCESSED) {
                //je hotovo, reportujeme
                if ($this->result === self::RESULT_SUCCESS) {
                    $this->extraHeaders[] = "<meta http-equiv='refresh' content='5;url=./../employees/index.php'>";
                    $this->title = "Zaměstnanec založen";
                } elseif ($this->result === self::RESULT_FAIL) {
                    $this->title = "Založení zaměstnance selhalo";
                }
            } elseif ($this->state === self::STATE_FORM_SENT) {
                //načíst data
                $this->employee = $this->readPost();
                //validovat data
                if ($this->employee->isValid()){
                    if ($this->findLogin()){

                        $token = bin2hex( random_bytes(20) );
                        //uložit a přesměrovat
                        if ($this->employee->insert()) {
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
                        $this->title = "Vytvořit zaměstnance : Neplatný formulář";
                    }


                } else {
                    foreach ($this->employee as $key=>$value){
                        if ($value === '' || $value === 0 && $key !== "admin"){
                            echo $this->m->render("FormHint",["message"=>$key]);
                        }
                    }
                    //jít na formulář nebo
                    $this->state = self::STATE_FORM_REQUESTED;
                    $this->title = "Vytvořit zaměstnance : Neplatný formulář";
                }
            } else {
                //přejít na formulář
                $this->title = "Vytvořit zaměstnance";
                $this->employee = new EmployeeModel();
            }

        }

    }


    protected function body(): string
    {

        if ($this->sessionStorage->get("login")["authenticated"]) {

            if ($this->state === self::STATE_FORM_REQUESTED) {
                return $this->m->render("employeeForm", ['create' => true, 'employee' => $this->employee , "rooms"=>$this->findRooms()]);
            } elseif ($this->state === self::STATE_PROCESSED) {
                if ($this->result === self::RESULT_SUCCESS) {
                    return $this->m->render("employeeSuccess", ["message" => "Zaměstnanec byl úspěšně vytvořen."]);
                } elseif ($this->result === self::RESULT_FAIL) {
                    return $this->m->render("employeeFail", ["message" => "Vytvoření zaměstnance selhalo"]);
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
        if ($action === 'create') {
            return self::STATE_FORM_SENT;
        }

        return self::STATE_FORM_REQUESTED;
    }

    private function readPost() : EmployeeModel {
        $employee = [];
        $employee['name'] = filter_input(INPUT_POST, 'name');
        $employee['surname'] = filter_input(INPUT_POST, 'surname');
        $employee['room'] = filter_input(INPUT_POST, 'room');
        $employee['job'] = filter_input(INPUT_POST, 'job');
        $employee['wage'] = filter_input(INPUT_POST, 'wage');
        $employee['login'] = filter_input(INPUT_POST, 'login');
        $hash = password_hash(filter_input(INPUT_POST, 'newpassword'),PASSWORD_DEFAULT);
        $employee['password'] = $hash;
        $employee['admin'] = filter_input(INPUT_POST, 'admin');
        if ($employee['admin'] === null)$employee['admin'] = 0;
        return new EmployeeModel($employee);
    }

    private function findLogin() : bool {
        $query = "SELECT login from employee";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        foreach ($stmt as $key=>$value){
            if ($value->login === $this->employee->login){
                return false;
            }

        }
        return true;
    }

}

$page = new CreateEmployeePage();
$page->render();

