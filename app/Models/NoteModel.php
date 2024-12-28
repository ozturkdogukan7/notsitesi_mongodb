<?php

namespace App\Models;

use CodeIgniter\Model;

class NoteModel extends Model
{
    protected $table = 'notes';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'category_id', 'title', 'content', 'is_private'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getAllNotes()
    {
        return $this->select('notes.*, users.username, categories.name as category_name')
                    ->join('users', 'users.id = notes.user_id')
                    ->join('categories', 'categories.id = notes.category_id')
                    ->findAll();
    }
}
