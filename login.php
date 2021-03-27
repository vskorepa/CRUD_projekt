<?php
require "./includes/bootstrap.inc.php";



final class LoginPage extends BaseCRUDPage{

    private LoginModel $login;

    protected bool $rozcestnik = false;

    protected function setUp(): void
    {
        parent::setup();

        if (!$this->sessionStorage->get("login")["authenticated"]){
            $this->sessionStorage->set("authenticated",self::NOT_AUTHENTICATED);

            $this->state = $this->getState();
            if ($this->state === self::STATE_PROCESSED){
                if ($this->result === self::RESULT_SUCCESS){
                    $this->extraHeaders[] = "<meta http-equiv='refresh' content='5;url=./'>";
                    $this->title = "Úspěšně přihlášen";
                }elseif ($this->result === self::RESULT_FAIL){
                    $this->title = "Neuspěšné přihlášení";
                    $this->extraHeaders[] = "<meta http-equiv='refresh' content='5;url=./login.php'>";

                }
            }elseif ($this->state === self::STATE_FORM_SENT) {

                $this->login = $this->readPost();
                if ($this->login->isValid()){
                    $token = bin2hex( random_bytes(20) );
//
                    if ($this->login($this->login->login,$this->login->password)){
                        $this->sessionStorage->set($token, ['result' => self::RESULT_SUCCESS]);

                    }else{
                        $this->sessionStorage->set($token, ['result' => self::RESULT_FAIL]);

                    }
                    $this->redirect($token);

                }else{
                    $this->state = self::STATE_FORM_REQUESTED;
                    $this->title = "Vytvořit zaměstnance : Neplatný formulář";

                }
            }else{
                $this->title = "Login zaměstnance";
                $this->login = new LoginModel();

            }

        }else{
//            $this->sessionStorage->set("login" , ["authenticated"=>self::NOT_AUTHENTICATED, "name"=>null]);
            header("Location: ./");
            exit;
        }



    }


    protected function body(): string
    {
        if ($this->state === self::STATE_FORM_REQUESTED) {
            return $this->m->render("loginForm", []);
        } elseif ($this->state === self::STATE_PROCESSED) {

            if ($this->result === self::RESULT_SUCCESS) {
                return $this->m->render("loginSuccess", ["message" => "Přihlášení úspěšné"]);
            } elseif ($this->result === self::RESULT_FAIL) {
                return $this->m->render("loginFail", ["message" => "Chyba při přihlášení"]);
            }
        }
        return $this->m->render("loginForm", []);

    }

    protected function getState() : int {
        //rozpoznání processed
        if ($this->isProcessed())
            return self::STATE_PROCESSED;

        $action = filter_input(INPUT_POST, 'action');
        if ($action === 'login') {
            return self::STATE_FORM_SENT;
        }

        return self::STATE_FORM_REQUESTED;
    }

    private function readPost() : LoginModel {
        $login = [];
        $login['login'] = filter_input(INPUT_POST, 'login');
        $login['password'] = filter_input(INPUT_POST, 'password');
        return new LoginModel($login);
    }


    protected function login(string $login, string $password):bool{

        $query = "SELECT employee_id , login , password , admin  FROM employee WHERE login = :login";

        $values = array(':login'=>$login);
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($values);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (is_array($row)){
            if (password_verify($password,$row['password'])){
                $this->login->login = $login;
                $this->login->employee_id = $row['employee_id'];
                $this->login->authenticated = true;

//                if ($row["admin"] === 1){
                    $this->sessionStorage->set("login" , ["authenticated"=>self::AUTHENTICATED, "name"=>$login , "id"=>$this->login->employee_id , "admin"=>$row["admin"] ]);

//                }
//                else{
//                    $this->sessionStorage->set("login" , ["authenticated"=>self::AUTHENTICATED, "name"=>$login , "id"=>$this->login->employee_id ,"admin"=>$row["admin"]]);
//
//                }


                return true;
            }
        }
        return false;
    }





}
$page = new LoginPage();
$page->render();

