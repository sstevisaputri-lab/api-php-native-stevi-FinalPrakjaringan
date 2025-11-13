<?php
namespace Src\Repositories;

use PDO;

class UserRepository {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function paginate($page = 1, $perPage = 10, $search = '', $sort = 'id', $order = 'asc') {
        $allowedSort = ['id', 'name', 'email'];
        if (!in_array($sort, $allowedSort)) {
            $sort = 'id'; 
        }

        
        $order = strtolower($order) === 'desc' ? 'DESC' : 'ASC';

        
        $offset = ($page - 1) * $perPage;

        
        $where = '';
        $params = [];
        if (!empty($search)) {
            $where = "WHERE name LIKE :search OR email LIKE :search";
            $params[':search'] = "%$search%";
        }

        
        $countQuery = "SELECT COUNT(*) FROM users $where";
        $stmt = $this->db->prepare($countQuery);
        $stmt->execute($params);
        $total = $stmt->fetchColumn();

        
        $query = "SELECT * FROM users $where ORDER BY $sort $order LIMIT :perPage OFFSET :offset";
        $stmt = $this->db->prepare($query);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':perPage', (int)$perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

       
        $totalPages = ceil($total / $perPage);
        $meta = [
            'page' => (int)$page,
            'per_page' => (int)$perPage,
            'total' => (int)$total,
            'total_pages' => (int)$totalPages,
            'sort' => $sort,
            'order' => $order
        ];

        return [
            'meta' => $meta,
            'data' => $data
        ];
    }
}