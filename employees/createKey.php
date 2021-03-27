<?php
require "../includes/bootstrap.inc.php";



final class CreateKeyPage extends BaseCRUDPage {

    private KeyModel $key;
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
                    $this->title = "Klíč vytvořen";
                } elseif ($this->result === self::RESULT_FAIL) {
                    $this->title = "Vytvořené klíče selhalo";
                }
            } elseif ($this->state === self::STATE_FORM_SENT) {
                //načíst data
                $this->key = $this->readPost();
                //validovat data
                if ($this->key->isValid()){
                    if ($this->checkDuplicate()){
                        $token = bin2hex( random_bytes(20) );
                        //uložit a přesměrovat
                        if ($this->key->insert()) {
                            //přesměruj se zprávou "úspěch"
                            $this->sessionStorage->set($token, ['result' => self::RESULT_SUCCESS]);
                        } else {
                            //přesměruj se zprávou "neúspěch"
                            $this->sessionStorage->set($token, ['result' => self::RESULT_FAIL]);
                        }
                        $this->redirect($token);
                    }else{
                        echo $this->m->render("FormHint",["duplicate"=>" "]);

                        //jít na formulář nebo
                        $this->state = self::STATE_FORM_REQUESTED;
                        $this->title = "Vytvořit klíč : Neplatný formulář";

                    }

                } else {
                    echo $this->m->render("FormHint",["message"=>""]);

                    //jít na formulář nebo
                    $this->state = self::STATE_FORM_REQUESTED;
                    $this->title = "Vytvořit klíč : Neplatný formulář";
                }
            } else {
                //přejít na formulář
                $this->title = "Vytvořit klíč";
                $this->key = new KeyModel();
            }

        }

    }


    protected function body(): string
    {

        if ($this->sessionStorage->get("login")["authenticated"]) {

            if ($this->state === self::STATE_FORM_REQUESTED) {
                return $this->m->render("keyForm", ["rooms"=>$this->findRooms(),"employees"=>$this->findEmployees()]);
            } elseif ($this->state === self::STATE_PROCESSED) {
                if ($this->result === self::RESULT_SUCCESS) {
                    return $this->m->render("employeeSuccess", ["message" => "Klíč byl úspěšně vytvořen."]);
                } elseif ($this->result === self::RESULT_FAIL) {
                    return $this->m->render("employeeFail", ["message" => "Vytvoření klíče selhalo"]);
                }
            }

        }
        $this->title = "Přihlašte se";
        return $this->m->render("notLogedFail" , ["root"=>"../login.php"]);

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

    private function readPost() : KeyModel {
        $key = [];
        $key['employee'] = filter_input(INPUT_POST, 'employee');
        $key['room'] = filter_input(INPUT_POST, 'room');
        return new KeyModel($key);
    }
    private function checkDuplicate(){

        $query = "SELECT employee , key_id, room FROM `key` WHERE employee= :employee_id AND room = :room_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(":employee_id", $this->key->employee);
        $stmt->bindParam(":room_id", $this->key->room);
        $stmt->execute();
        if ($stmt->fetch(PDO::FETCH_ASSOC))
            return false;
        else
            return true;

    }

}

$page = new CreateKeyPage();
$page->render();

