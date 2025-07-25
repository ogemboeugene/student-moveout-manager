<?php
/**
 * Student Model Class
 */

class Student {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function create($name, $grade) {
        $stmt = $this->db->prepare("INSERT INTO students (name, grade) VALUES (?, ?)");
        return $stmt->execute([$name, $grade]);
    }

    public function getAll($limit = null, $offset = 0) {
        $sql = "SELECT * FROM students WHERE status = 'active' ORDER BY grade, name";
        if ($limit) {
            $sql .= " LIMIT ? OFFSET ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$limit, $offset]);
        } else {
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
        }
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM students WHERE id = ? AND status = 'active'");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function search($query) {
        $stmt = $this->db->prepare("
            SELECT * FROM students 
            WHERE status = 'active' 
            AND (name LIKE ? OR grade LIKE ?) 
            ORDER BY grade, name
        ");
        $searchTerm = "%{$query}%";
        $stmt->execute([$searchTerm, $searchTerm]);
        return $stmt->fetchAll();
    }

    public function update($id, $name, $grade) {
        $stmt = $this->db->prepare("UPDATE students SET name = ?, grade = ? WHERE id = ?");
        return $stmt->execute([$name, $grade, $id]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("UPDATE students SET status = 'inactive' WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getCount() {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM students WHERE status = 'active'");
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'];
    }

    public function isCalled($studentId) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count FROM calls 
            WHERE student_id = ? AND expires_at > NOW()
        ");
        $stmt->execute([$studentId]);
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }
}
