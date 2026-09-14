<?php
require_once __DIR__ . '/../models/Student.php';

class StudentController {
    private $studentModel;

    public function __construct() {
        $this->studentModel = new Student();
    }

    public function index() {
        $students = $this->studentModel->getAll();
        require_once "app/views/students/index.php";
    }

    public function create() {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $name = $_POST["name"];
            $email = $_POST["email"];
            if ($this->studentModel->create($name, $email)) {
                header("Location: /");
                exit;
            }
        }
        require_once "app/views/students/create.php";
    }

    public function edit($id) {
        $student = $this->studentModel->getById($id);
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $name = $_POST["name"];
            $email = $_POST["email"];
            if ($this->studentModel->update($id, $name, $email)) {
                header("Location: /");
                exit;
            }
        }
        require_once "app/views/students/edit.php";
    }

    public function delete($id) {
        if ($this->studentModel->delete($id)) {
            header("Location: /");
            exit;
        }
    }
}
?>
