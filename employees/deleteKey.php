<?php
require "../includes/bootstrap.inc.php";


final class DeleteKeyPage extends BaseCRUDPage {

    private ?int $key_id;

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
                    $this->title = "Klíč odebrán";
                } elseif ($this->result === self::RESULT_FAIL) {
                    $this->title = "Odebrání klíče selhalo";
                }
            } elseif ($this->state === self::STATE_DELETE_REQUESTED) {
                //načíst data
                $this->key_id = $this->readPost();
                //validovat data
                if (!$this->key_id) {
                    throw new RequestException(400);
                }

                //smazat a přesměrovat
                $token = bin2hex( random_bytes(20) );

                if (KeyModel::deleteById($this->key_id)) {
                    //přesměruj se zprávou "úspěch"
                    $this->sessionStorage->set($token, ['result' => self::RESULT_SUCCESS]);
                } else {
                    //přesměruj se zprávou "neúspěch"
                    $this->sessionStorage->set($token, ['result' => self::RESULT_FAIL]);
                }
                $this->redirect($token);




            }

        }

    }

    protected function body(): string
    {

        if ($this->sessionStorage->get("login")["authenticated"]) {


            if ($this->sessionStorage->get("login")["admin"] === 1){
                if ($this->result === self::RESULT_SUCCESS) {
                    return $this->m->render("employeeSuccess", ["message" => "Klíč byl úspěšně odstraněn."]);
                } elseif ($this->result === self::RESULT_FAIL) {
                    return $this->m->render("employeeFail", ["message" => "Odstranění klíče selhalo."]);
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
        $key_id = filter_input(INPUT_POST, 'key_id', FILTER_VALIDATE_INT);
        return $key_id;
    }

}

$page = new DeleteKeyPage();
$page->render();

