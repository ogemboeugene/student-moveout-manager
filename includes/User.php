<?php
/**
 * User Model Class
 */

class User {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function create($username, $password, $role) {
        $passwordHash = Auth::hashPassword($password);
        $stmt = $this->db->prepare("INSERT INTO users (username, password_hash, role) VALUES (?, ?, ?)");
        return $stmt->execute([$username, $passwordHash, $role]);
    }

    public function getAll() {
        $stmt = $this->db->prepare("SELECT id, username, role, status, last_login, created_at FROM users ORDER BY username");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT id, username, role, status, last_login, created_at FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function update($id, $username, $role, $status) {
        $stmt = $this->db->prepare("UPDATE users SET username = ?, role = ?, status = ? WHERE id = ?");
        return $stmt->execute([$username, $role, $status, $id]);
    }

    public function updatePassword($id, $newPassword) {
        $passwordHash = Auth::hashPassword($newPassword);
        $stmt = $this->db->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
        return $stmt->execute([$passwordHash, $id]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("UPDATE users SET status = 'inactive' WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function usernameExists($username, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM users WHERE username = ?";
        $params = [$username];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }
}
