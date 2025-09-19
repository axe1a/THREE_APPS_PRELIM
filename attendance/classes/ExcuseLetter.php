<?php
class ExcuseLetter {
    private $id;
    private $student_id;
    private $course_id;
    private $subject;
    private $reason;
    private $date_of_absence;
    private $supporting_document;
    private $status;
    private $admin_comment;
    private $submitted_at;
    private $reviewed_at;
    private $reviewed_by;

    public function __construct($id = null, $student_id = null, $course_id = null, $subject = null, 
                               $reason = null, $date_of_absence = null, $supporting_document = null, 
                               $status = 'pending', $admin_comment = null, $submitted_at = null, 
                               $reviewed_at = null, $reviewed_by = null) {
        $this->id = $id;
        $this->student_id = $student_id;
        $this->course_id = $course_id;
        $this->subject = $subject;
        $this->reason = $reason;
        $this->date_of_absence = $date_of_absence;
        $this->supporting_document = $supporting_document;
        $this->status = $status;
        $this->admin_comment = $admin_comment;
        $this->submitted_at = $submitted_at;
        $this->reviewed_at = $reviewed_at;
        $this->reviewed_by = $reviewed_by;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getStudentId() { return $this->student_id; }
    public function getCourseId() { return $this->course_id; }
    public function getSubject() { return $this->subject; }
    public function getReason() { return $this->reason; }
    public function getDateOfAbsence() { return $this->date_of_absence; }
    public function getSupportingDocument() { return $this->supporting_document; }
    public function getStatus() { return $this->status; }
    public function getAdminComment() { return $this->admin_comment; }
    public function getSubmittedAt() { return $this->submitted_at; }
    public function getReviewedAt() { return $this->reviewed_at; }
    public function getReviewedBy() { return $this->reviewed_by; }

    // Submit excuse letter
    public function submit($conn) {
        $stmt = $conn->prepare("INSERT INTO excuse_letters (student_id, course_id, subject, reason, date_of_absence, supporting_document) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iissss", $this->student_id, $this->course_id, $this->subject, $this->reason, $this->date_of_absence, $this->supporting_document);
        return $stmt->execute();
    }

    // Get excuse letters by student
    public static function getByStudent($conn, $student_id) {
        $stmt = $conn->prepare("
            SELECT excuse_letters.*, courses.name as course_name 
            FROM excuse_letters 
            JOIN courses ON excuse_letters.course_id = courses.id 
            WHERE excuse_letters.student_id = ? 
            ORDER BY excuse_letters.submitted_at DESC
        ");
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Get excuse letters by course (for admin)
    public static function getByCourse($conn, $course_id) {
        $stmt = $conn->prepare("
            SELECT excuse_letters.*, courses.name as course_name, users.name as student_name, users.year_level
            FROM excuse_letters 
            JOIN courses ON excuse_letters.course_id = courses.id 
            JOIN users ON excuse_letters.student_id = users.id 
            WHERE excuse_letters.course_id = ? 
            ORDER BY excuse_letters.submitted_at DESC
        ");
        $stmt->bind_param("i", $course_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Get all pending excuse letters
    public static function getPending($conn) {
        $stmt = $conn->prepare("
            SELECT excuse_letters.*, courses.name as course_name, users.name as student_name, users.year_level
            FROM excuse_letters 
            JOIN courses ON excuse_letters.course_id = courses.id 
            JOIN users ON excuse_letters.student_id = users.id 
            WHERE excuse_letters.status = 'pending' 
            ORDER BY excuse_letters.submitted_at ASC
        ");
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Get excuse letter by ID
    public static function getById($conn, $id) {
        $stmt = $conn->prepare("
            SELECT excuse_letters.*, courses.name as course_name, users.name as student_name, users.year_level
            FROM excuse_letters 
            JOIN courses ON excuse_letters.course_id = courses.id 
            JOIN users ON excuse_letters.student_id = users.id 
            WHERE excuse_letters.id = ?
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Approve excuse letter
    public static function approve($conn, $id, $admin_id, $comment = null) {
        $stmt = $conn->prepare("UPDATE excuse_letters SET status = 'approved', admin_comment = ?, reviewed_at = NOW(), reviewed_by = ? WHERE id = ?");
        $stmt->bind_param("sii", $comment, $admin_id, $id);
        return $stmt->execute();
    }

    // Reject excuse letter
    public static function reject($conn, $id, $admin_id, $comment = null) {
        $stmt = $conn->prepare("UPDATE excuse_letters SET status = 'rejected', admin_comment = ?, reviewed_at = NOW(), reviewed_by = ? WHERE id = ?");
        $stmt->bind_param("sii", $comment, $admin_id, $id);
        return $stmt->execute();
    }

    // Get excuse letters by status
    public static function getByStatus($conn, $status) {
        $stmt = $conn->prepare("
            SELECT excuse_letters.*, courses.name as course_name, users.name as student_name, users.year_level
            FROM excuse_letters 
            JOIN courses ON excuse_letters.course_id = courses.id 
            JOIN users ON excuse_letters.student_id = users.id 
            WHERE excuse_letters.status = ? 
            ORDER BY excuse_letters.submitted_at DESC
        ");
        $stmt->bind_param("s", $status);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Get excuse letters by course and status
    public static function getByCourseAndStatus($conn, $course_id, $status) {
        $stmt = $conn->prepare("
            SELECT excuse_letters.*, courses.name as course_name, users.name as student_name, users.year_level
            FROM excuse_letters 
            JOIN courses ON excuse_letters.course_id = courses.id 
            JOIN users ON excuse_letters.student_id = users.id 
            WHERE excuse_letters.course_id = ? AND excuse_letters.status = ? 
            ORDER BY excuse_letters.submitted_at DESC
        ");
        $stmt->bind_param("is", $course_id, $status);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>
