<?php
class Course {
    public static function getAllCourses($conn) {
        $result = $conn->query("SELECT * FROM courses");
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}