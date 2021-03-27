<?php
require "./includes/bootstrap.inc.php";



final class PasswordChange extends BaseCRUDPage{

    private PasswModel $password;

    protected bool $rozcestnik = false;

    protected function setUp(): void
    {
        parent::setup();

        if (!$this->sessionStorage->get("login")["authenticated"]) {
            $this->extraHeaders[] = "<meta http-equiv='refresh' content='3;url=./login.php'>";
            $this->title = "Přihlašte se";

        }else {


            $this->state = $this->getState();
            if ($this->state === self::STATE_PROCESSED){
                if ($this->result === self::RESULT_SUCCESS){
                    $this->extraHeaders[] = "<meta http-equiv='refresh' content='5;url=./'>";
                    $this->title = "Heslo úspěšně změněno";
                }elseif ($this->result === self::RESULT_FAIL){
                    $this->title = "Chyba při změně hesla";
                    $this->extraHeaders[] = "<meta http-equiv='refresh' content='5;url=./login.php'>";

                }
            }elseif ($this->state === self::STATE_FORM_SENT) {

                $this->password = $this->readPost();
                if ($this->password->isValid()){
                    $token = bin2hex( random_bytes(20) );
//
                    if ($this->passwordCheck($this->password->oldpassw,$this->password->password)){
                        $this->sessionStorage->set($token, ['result' => self::RESULT_SUCCESS]);
                        $this->changePassword();
                        $this->redirect($token);

                    }else{
                        $this->title = "Změna hesla - neplatný formulář";
                        echo $this->m->render("ChangeHint", ["message"=>"Špatně zadané staré heslo"]);

                    }

                }else{
                    echo $this->m->render("ChangeHint", ["message"=>"Pole heslo a nové heslo se neschodují."]);
                    $this->state = self::STATE_FORM_REQUESTED;
                    $this->title = "Změna hesla - neplatný formulář";

                }
            }else{
                $this->title = "Změna hesla";
                $this->login = new LoginModel();

            }

        }



    }


    protected function body(): string
    {

        if ($this->sessionStorage->get("login")["authenticated"]) {
            if ($this->state === self::STATE_FORM_REQUESTED) {
                return $this->m->render("changePasswordForm", []);
            } elseif ($this->state === self::STATE_PROCESSED) {

                if ($this->result === self::RESULT_SUCCESS) {
                    return $this->m->render("loginSuccess", ["message" => "Změna hesla úspěšná"]);
                } elseif ($this->result === self::RESULT_FAIL) {
                    return $this->m->render("loginFail", ["message" => "Změna hesla neúspěšná"]);

                }
            }
            return $this->m->render("changePasswordForm", []);
        }else{
            $this->title = "Přihlašte se";
            return $this->m->render("notLogedFail" , ["root"=>"./login.php"]);
        }

    }

    protected function getState() : int {
        //rozpoznání processed
        if ($this->isProcessed())
            return self::STATE_PROCESSED;

        $action = filter_input(INPUT_POST, 'action');
        if ($action === 'changepassw') {
            return self::STATE_FORM_SENT;
        }

        return self::STATE_FORM_REQUESTED;
    }

    private function readPost() : PasswModel {
        $password = [];
        $password['oldpassw'] = filter_input(INPUT_POST, 'oldpassw');
        $password['password'] = filter_input(INPUT_POST, 'password');
        $password['checkpassw'] = filter_input(INPUT_POST, 'checkpassw');

        return new PasswModel($password);
    }


    protected function passwordCheck(string $oldpassword, string $newPassword):bool{

        $id = $this->sessionStorage->get("login")["id"];

        $query = "SELECT  password , employee_id  FROM employee WHERE employee_id = $id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (is_array($row)){
            if (password_verify($oldpassword,$row['password'])){
                $this->password->password = $newPassword;
                $this->password->id = $row['employee_id'];

                return true;
            }
        }
        return false;
    }

    protected function changePassword() : void{

        $hash = password_hash($this->password->password,PASSWORD_DEFAULT);

        $query = "UPDATE employee SET password = :password WHERE employee_id = :employee_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':password',$hash);
        $stmt->bindParam(':employee_id',$this->password->id);
        $stmt->execute();

    }




}
$page = new PasswordChange();
$page->render();

