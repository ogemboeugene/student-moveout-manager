<?php
/**
 * Call Model Class
 */

class Call {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function create($studentId, $teacherId) {
        try {
            $this->db->beginTransaction();
            
            // Remove any existing call for this student
            $this->removeByStudentId($studentId);
            
            // Create new call
            $stmt = $this->db->prepare("INSERT INTO calls (student_id, teacher_id) VALUES (?, ?)");
            $result = $stmt->execute([$studentId, $teacherId]);
            
            $this->db->commit();
            return $result;
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function getActiveCalls() {
        $stmt = $this->db->prepare("
            SELECT c.*, s.name, s.grade, u.username as teacher_name,
                   TIMESTAMPDIFF(SECOND, c.called_at, NOW()) as elapsed_seconds
            FROM calls c
            JOIN students s ON c.student_id = s.id
            JOIN users u ON c.teacher_id = u.id
            WHERE c.expires_at > NOW()
            ORDER BY c.called_at ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function removeExpired() {
        $stmt = $this->db->prepare("DELETE FROM calls WHERE expires_at <= NOW()");
        return $stmt->execute();
    }

    public function removeByStudentId($studentId) {
        $stmt = $this->db->prepare("DELETE FROM calls WHERE student_id = ?");
        return $stmt->execute([$studentId]);
    }

    public function getCallHistory($limit = 50) {
        $stmt = $this->db->prepare("
            SELECT c.*, s.name, s.grade, u.username as teacher_name
            FROM calls c
            JOIN students s ON c.student_id = s.id
            JOIN users u ON c.teacher_id = u.id
            ORDER BY c.called_at DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    public function isStudentCalled($studentId) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count FROM calls 
            WHERE student_id = ? AND expires_at > NOW()
        ");
        $stmt->execute([$studentId]);
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }

    public function getCallsCount() {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM calls WHERE expires_at > NOW()");
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'];
    }
}
