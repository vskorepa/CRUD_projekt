<?php
require "includes/bootstrap.inc.php";


final class RozcestnikPage extends BasePage {

    protected function setUp(): void
    {
        parent::setUp();


        if (!$this->sessionStorage->get("login")["authenticated"]) {
            $this->extraHeaders[] = "<meta http-equiv='refresh' content='5;url=./login.php'>";
            $this->title = "Přihlašte se";
            $this->rozcestnik =  false ;

        }else{
            $this->title = "Rozcestník";
            $this->rozcestnik =  false ;

        }



    }

    protected function body(): string
    {


        if ($this->sessionStorage->get("login")["authenticated"]) {
            return $this->m->render("rozcestnik");
        }else{
            $this->title = "Přihlašte se";
            return $this->m->render("notLogedFail" , ["root"=>"./login.php"]);

        }

    }

}

$page = new RozcestnikPage();
$page->render();

