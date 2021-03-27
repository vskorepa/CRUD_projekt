<?php
require "./includes/bootstrap.inc.php";


final class LogoutPage extends BasePage
{
    protected function setUp(): void
    {
        $this->sessionStorage->delete("login");
        header("Location: ./login.php");
        exit;
    }

    protected function body(): string
    {



    }


}
$page = new LogoutPage();
$page->render();
