<?php

class CategoryModel {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function allWithProductCount(): array {
        $stmt = $this->db->query(
            'SELECT c.id, c.name, c.description, COUNT(p.id) AS product_count
             FROM categories c
             LEFT JOIN products p ON p.category_id = c.id
             GROUP BY c.id, c.name, c.description
             ORDER BY c.id'
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function names(): array {
        $stmt = $this->db->query('SELECT name FROM categories ORDER BY id');
        return array_map('strval', $stmt->fetchAll(PDO::FETCH_COLUMN));
    }

    public function idByName(string $name): ?int {
        $stmt = $this->db->prepare('SELECT id FROM categories WHERE name = :name LIMIT 1');
        $stmt->execute(['name' => $name]);
        $id = $stmt->fetchColumn();

        return $id === false ? null : (int) $id;
    }

    public function create(string $name, string $description): void {
        $stmt = $this->db->prepare(
            'INSERT INTO categories (name, description)
             VALUES (:name, :description)'
        );
        $stmt->execute([
            'name' => $name,
            'description' => $description,
        ]);
    }

    public function update(int $id, string $name, string $description): void {
        $stmt = $this->db->prepare(
            'UPDATE categories
             SET name = :name, description = :description
             WHERE id = :id'
        );
        $stmt->execute([
            'id' => $id,
            'name' => $name,
            'description' => $description,
        ]);
    }

    public function delete(int $id): void {
        $stmt = $this->db->prepare('DELETE FROM categories WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public function validateInput(array $input, ?int $excludeId = null): array {
        $name = trim((string) ($input['name'] ?? ''));
        $description = trim((string) ($input['description'] ?? ''));
        $errors = [];

        $nameLength = function_exists('mb_strlen') ? mb_strlen($name) : strlen($name);
        if ($name === '') {
            $errors[] = 'Tên danh mục là bắt buộc.';
        } elseif ($nameLength < 2 || $nameLength > 100) {
            $errors[] = 'Tên danh mục phải từ 2 đến 100 ký tự.';
        } elseif ($this->nameExists($name, $excludeId)) {
            $errors[] = 'Danh mục này đã tồn tại.';
        }

        return [$name, $description, $errors];
    }

    private function nameExists(string $name, ?int $excludeId = null): bool {
        $sql = 'SELECT COUNT(*) FROM categories WHERE name = :name';
        $params = ['name' => $name];

        if ($excludeId !== null) {
            $sql .= ' AND id <> :id';
            $params['id'] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn() > 0;
    }

}
?>
