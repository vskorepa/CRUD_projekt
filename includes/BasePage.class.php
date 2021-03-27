<?php


abstract class BasePage
{

    protected MustacheRunner $m;
    protected string $title;
    protected bool $rozcestnik = true;
    protected bool $isAdmin = false;

    protected array $employeeInfo = [];
    protected array $extraHeaders = [];
    protected SessionStorage $sessionStorage;


    /**
     * BasePage constructor.
     */
    public function __construct()
    {
        $this->m = new MustacheRunner();
        $this->sessionStorage = new SessionStorage();


    }


    public function render() {
        try {
            $this->setUp();
            $html = $this->header();
            $html .= $this->body();
            $html .= $this->footer();
            echo $html;
            $this->wrapUp();
        } catch (RequestException $e) {
            $ePage = new ErrorPage($e->getStatusCode());
            $ePage->render();
        } catch (Exception $e) {
            $ePage = new ErrorPage();
            $ePage->render();
        }
        exit;
    }


    protected function setUp() : void {
        if ($this->sessionStorage->get("login")["admin"] !== 1){
            $this->isAdmin=false;
        }
        else{
            $this->isAdmin=true;
        }


    }

    protected function loadLoginUser() : array{

    }

    protected function header() : string {
        if ($this->sessionStorage->get("login")){
            $this->employeeInfo = $this->sessionStorage->get("login");
        }
        return $this->m->render("head", ["title" => $this->title, 'extraHeaders' => $this->extraHeaders,'employee'=>$this->employeeInfo ,"rozcestnik"=>$this->rozcestnik]);
    }

    protected abstract function body() : string;

    protected function  footer() : string {
        if ($this->rozcestnik)
        return $this->m->render("foot", ["rozcestnik"=> "index.php"]);
        else
        return $this->m->render("foot");

    }

    protected function wrapUp() : void{}

}