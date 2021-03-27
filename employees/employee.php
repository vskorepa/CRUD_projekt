<?php
require "../includes/bootstrap.inc.php";

final class EmployeeDetailPage extends BaseDBPage{





    protected function setUp(): void
    {
        parent::setUp();

        if (!$this->sessionStorage->get("login")["authenticated"]) {
            $this->extraHeaders[] = "<meta http-equiv='refresh' content='5;url=../login.php'>";
            $this->title = "Přihlašte se";

        }
        else {
            $this->title = "Detail zaměstnance";
        }




    }
    protected function body(): string
    {

        if ($this->sessionStorage->get("login")["authenticated"]) {

            $stmt = $this->pdo->prepare("SELECT  employee.name AS ename, surname,job,wage,room.name AS rname, room_id From employee JOIN  room On room_id = employee.room WHERE  employee_id=?");
            $stmt->execute([$this->getID("employee")]);
            $stmt2 = $this->pdo->prepare("SELECT `key`.room, room_id, `name`, employee FROM `key`, room WHERE employee=? AND room_id = `key`.room");
            $stmt2->execute([$this->getID("employee")]);

            echo $this->m->render("employeeDetail",["employee"=>$stmt]);
            return $this->m->render("employeeDetail_keys",["keys"=>$stmt2]);

        }else{
            $this->title = "Přihlašte se";
            return $this->m->render("notLogedFail" , ["root"=>"../login.php"]);

        }




    }

}
$page = new EmployeeDetailPage();
$page->render();

