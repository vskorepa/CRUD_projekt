<?php
require "../includes/bootstrap.inc.php";


final class DeleteEmployeePage extends BaseCRUDPage {

    private ?int $employee_id;

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
                $this->title = "odebrání zaměstnance";
                $this->state = $this->getState();


                if ($this->state === self::STATE_PROCESSED) {
                    //je hotovo, reportujeme
                    if ($this->result === self::RESULT_SUCCESS) {
                        $this->extraHeaders[] = "<meta http-equiv='refresh' content='5;url=./index.php'>";
                        $this->title = "Zaměstnanec odebrán";
                    } elseif ($this->result === self::RESULT_FAIL) {
                        $this->title = "Odebrání zaměstnance selhalo";
                    }
                } elseif ($this->state === self::STATE_DELETE_REQUESTED) {
                    //načíst data
                    $token = bin2hex( random_bytes(20) );
                    $this->employee_id = $this->readPost();
                    //validovat data
                    if (!$this->employee_id) {
                        throw new RequestException(400);
                    }
                    elseif ($this->employee_id !== $this->sessionStorage->get("login")["id"]){
                        //smazat a přesměrovat

                        if (EmployeeModel::deleteById($this->employee_id)) {
                            //přesměruj se zprávou "úspěch"
                            $this->sessionStorage->set($token, ['result' => self::RESULT_SUCCESS]);
                        } else {
                            //přesměruj se zprávou "neúspěch"
                            $this->sessionStorage->set($token, ['result' => self::RESULT_FAIL]);
                        }
                        $this->redirect($token);
                    }
                    else{
                        $this->sessionStorage->set($token, ['result' => self::RESULT_FAIL]);
                        $this->redirect($token);

                    }

                }
        }

    }

    protected function body(): string
    {

        if ($this->sessionStorage->get("login")["authenticated"]) {


            if ($this->sessionStorage->get("login")["admin"] === 1){
                if ($this->result === self::RESULT_SUCCESS) {
                    return $this->m->render("employeeSuccess", ["message" => "Zaměstnanec byl úspěšně odstraněn."]);
                } elseif ($this->result === self::RESULT_FAIL) {
                    return $this->m->render("employeeFail", ["message" => "Odstranění zaměstnance selhalo."]);
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

        return self::STATE_DELETE_REQUESTED;
    }

    private function readPost() : ?int {
        $employee_id = filter_input(INPUT_POST, 'employee_id', FILTER_VALIDATE_INT);
        return $employee_id;
    }

}

$page = new DeleteEmployeePage();
$page->render();

