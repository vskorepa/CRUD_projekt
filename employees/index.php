<?php
require "../includes/bootstrap.inc.php";


final class ListEmployeesPage extends BaseDBPage {

    protected function setUp(): void
    {
        parent::setUp();
        $this->title = "Seznam zaměstnanců";

        if (!$this->sessionStorage->get("login")["authenticated"]) {
            $this->extraHeaders[] = "<meta http-equiv='refresh' content='5;url=../login.php'>";
            $this->title = "Přihlašte se";

        }



        }

    protected function body(): string
    {


        if ($this->sessionStorage->get("login")["authenticated"]) {

            $stmt = $this->sortEmployees();
            $stmt->execute([]);

            return $this->m->render("employeeList", ["employeeDetail" => "employee.php", "employees" => $stmt , "show"=>$this->isAdmin ]);

        }else{
            $this->title = "Přihlašte se";
            return $this->m->render("notLogedFail" , ["root"=>"../login.php"]);

        }


    }

}

$page = new ListEmployeesPage();
$page->render();

