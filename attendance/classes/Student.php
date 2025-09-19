<?php
require_once 'User.php';

class Student extends User {
    public function fileAttendance($conn) {
        $date = date('Y-m-d');
        $time_in = date('H:i:s');
        $status = (date('H:i:s') > '08:00:00') ? 'late' : 'on time';

        $stmt = $conn->prepare("INSERT INTO attendances (user_id, date, time_in, status) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $this->id, $date, $time_in, $status);
        return $stmt->execute();
    }

    public function getAttendanceHistory($conn) {
        $stmt = $conn->prepare("SELECT * FROM attendances WHERE user_id = ?");
        $stmt->bind_param("i", $this->id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    public function hasFiledToday($conn) {
    $today = date('Y-m-d');
    $stmt = $conn->prepare("SELECT COUNT(*) as cnt FROM attendances WHERE user_id = ? AND date = ?");
    $stmt->bind_param("is", $this->id, $today);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result['cnt'] > 0;
}

}