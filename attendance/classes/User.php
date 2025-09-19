<?php
class User {
    protected $id;
    protected $name;
    protected $email;
    protected $role;
    protected $course_id;
    protected $year_level;

    public function __construct($id, $name, $email, $role, $course_id = null, $year_level = null) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->role = $role;
        $this->course_id = $course_id;
        $this->year_level = $year_level;
    }

    public function getName() {
        return $this->name;
    }
    public function getRole() {
        return $this->role;
    }
    public function getId() {
        return $this->id;
    }
    public function getCourseId() {
        return $this->course_id;
    }
    public function getYearLevel() {
        return $this->year_level;
    }
}