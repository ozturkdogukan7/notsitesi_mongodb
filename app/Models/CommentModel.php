<?php

namespace App\Models;

use App\Libraries\MongoDB as MongoLib;
use Exception;

class CommentModel
{
    protected $mongo;
    protected $collection = 'comments';

    public function __construct()
    {
        try {
            $this->mongo = new MongoLib();
        } catch (Exception $e) {
            log_message('error', 'CommentModel Error: ' . $e->getMessage());
            throw new Exception('Yorum sistemi şu anda kullanılamıyor: ' . $e->getMessage());
        }
    }

    public function insert($data)
    {
        try {
            return $this->mongo->executeInsert($this->collection, $data);
        } catch (Exception $e) {
            log_message('error', 'Comment Insert Error: ' . $e->getMessage());
            throw new Exception('Yorum eklenirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    public function findByNoteId($noteId)
    {
        try {
            $cursor = $this->mongo->executeQuery($this->collection, ['note_id' => $noteId]);
            return $cursor->toArray();
        } catch (Exception $e) {
            log_message('error', 'Comment Find Error: ' . $e->getMessage());
            throw new Exception('Yorumlar yüklenirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $bulk = new \MongoDB\Driver\BulkWrite();
            $bulk->delete(['_id' => new \MongoDB\BSON\ObjectId($id)]);
            
            $writeConcern = new \MongoDB\Driver\WriteConcern(\MongoDB\Driver\WriteConcern::MAJORITY, 1000);
            return $this->mongo->client->executeBulkWrite($this->mongo->database . '.' . $this->collection, $bulk, $writeConcern);
        } catch (Exception $e) {
            log_message('error', 'Comment Delete Error: ' . $e->getMessage());
            throw new Exception('Yorum silinirken bir hata oluştu: ' . $e->getMessage());
        }
    }
}
