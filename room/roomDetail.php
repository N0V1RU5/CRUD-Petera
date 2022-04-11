<?php
session_start();
require_once "../_includes/bootstrap.inc.php";

final class Page extends BaseDBPage{

    public function __construct()
    {
        parent::__construct();
        $this->title = "Room Detail";
    }

    protected function body(): string
    {
        $room = RoomModel::getById(filter_input(INPUT_GET, "room_id", FILTER_VALIDATE_INT));

        if($_SESSION['logged']){
            return $this->m->render(
                "roomDetail",
                ["room" => $room]);
        } else {
            $return = "<a href='../login.php'><button type='button' class='btn btn-primary' style='float: '>Back to Login Page</button></a>";
            $return .= " You are not logged in";
            return $return;
        }
    }
}
(new Page())->render();