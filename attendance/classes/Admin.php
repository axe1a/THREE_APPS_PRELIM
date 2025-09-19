<?php
require_once 'User.php';

class Admin extends User {
    public function addCourse($conn, $course_name) {
    $stmt = $conn->prepare("INSERT INTO courses (name) VALUES (?)");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("s", $course_name);

    try {
        return $stmt->execute();
    } catch (mysqli_sql_exception $e) {
        // Handle duplicate entry error
        if ($e->getCode() == 1062) {
            // You could return false or custom message here
            echo "<p style='color:red;'>Course already exists!</p>";
            return false;
        } else {
            throw $e; // rethrow other errors
        }
    }
    }


    public function editCourse($conn, $course_id, $course_name) {
        $stmt = $conn->prepare("UPDATE courses SET name = ? WHERE id = ?");
        $stmt->bind_param("si", $course_name, $course_id);
        return $stmt->execute();
    }

    public function getAttendancesByCourseYear($conn, $course_id, $year_level) {
        $stmt = $conn->prepare(
            "SELECT a.*, u.name FROM attendances a 
             JOIN users u ON a.user_id = u.id 
             WHERE u.course_id = ? AND u.year_level = ?"
        );
        $stmt->bind_param("ii", $course_id, $year_level);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}