<?php
require "../includes/bootstrap.inc.php";


final class ListRoomsPage extends BaseDBPage {

    protected function setUp(): void
    {
        parent::setUp();

        if (!$this->sessionStorage->get("login")["authenticated"]) {
            $this->extraHeaders[] = "<meta http-equiv='refresh' content='3;url=../login.php'>";
            $this->title = "Přihlašte se";

        }else {
            $this->title = "Seznam místností";
        }
    }

    protected function body(): string
    {

        if ($this->sessionStorage->get("login")["authenticated"]) {

            $stmt = $this->sortRooms();
            $stmt->execute([]);
            return $this->m->render("roomList", ["roomDetail" => "room.php", "rooms" => $stmt , "show"=>$this->isAdmin]);

        }else{
            $this->title = "Přihlašte se";
            return $this->m->render("notLogedFail" , ["root"=>"../login.php"]);

        }
    }

}

$page = new ListRoomsPage();
$page->render();

