<?php


require "../includes/bootstrap.inc.php";



final class RoomDetailPage extends BaseDBPage
{


    protected function setUp(): void
    {
        parent::setUp();



        if (!$this->sessionStorage->get("login")["authenticated"]) {
            $this->extraHeaders[] = "<meta http-equiv='refresh' content='3;url=../login.php'>";
            $this->title = "Přihlašte se";

        }else {
            $this->title = "Detail místnosti";
        }

    }

    protected function body(): string
    {

        if ($this->sessionStorage->get("login")["authenticated"]) {

            $query = 'SELECT no, room.name AS rname,phone FROM room WHERE room_id=?';
            $query2 = 'SELECT CONCAT(employee.name," ", employee.surname) AS ename, wage,  employee_id FROM employee JOIN room ON employee.room = room_id WHERE room_id=?';
            $query3 = 'SELECT `key`.employee,  CONCAT (name," ",surname) AS ename  FROM `key`, employee WHERE `key`.room=? AND employee_id = `key`.employee';


            $stmt = $this->pdo -> prepare($query);
            $stmt->execute([$this->getID("room")]);
            $stmt2 = $this->pdo -> prepare($query2);
            $stmt2->execute([$this->getID("room")]);
            $stmt3 = $this->pdo -> prepare($query3);
            $stmt3->execute([$this->getID("room")]);

            echo  $this->m->render("roomDetail_1",["room"=>$stmt]);
            echo  $this->m->render("roomDetail_employee",["employee"=>$stmt2]);
            return  $this->m->render("roomDetail_keys",["keys"=>$stmt3]);
        }else{
            $this->title = "Přihlašte se";
            return $this->m->render("notLogedFail" , ["root"=>"../login.php"]);

        }



    }

}

$page = new RoomDetailPage();
$page->render();



