<?php

namespace App\Libraries;

use Config\MongoDB as MongoConfig;
use Exception;

class MongoDB
{
    protected $client;
    protected $database;

    public function __construct()
    {
        try {
            $config = new MongoConfig();
            
            if (!class_exists('\MongoDB\Driver\Manager')) {
                throw new Exception('MongoDB PHP sürücüsü yüklü değil.');
            }
            
            $manager = new \MongoDB\Driver\Manager("mongodb://127.0.0.1:27017");
            
            // Test connection
            $command = new \MongoDB\Driver\Command(['ping' => 1]);
            $manager->executeCommand('admin', $command);
            
            $this->client = $manager;
            $this->database = $config->database;
            
        } catch (Exception $e) {
            log_message('error', 'MongoDB Connection Error: ' . $e->getMessage());
            throw new Exception('MongoDB bağlantı hatası: ' . $e->getMessage());
        }
    }

    public function getCollection($collection)
    {
        try {
            $ns = $this->database . '.' . $collection;
            return new \MongoDB\Driver\BulkWrite();
        } catch (Exception $e) {
            log_message('error', 'MongoDB Collection Error: ' . $e->getMessage());
            throw new Exception('MongoDB koleksiyon hatası: ' . $e->getMessage());
        }
    }
    
    public function executeInsert($collection, $document)
    {
        try {
            $bulk = new \MongoDB\Driver\BulkWrite();
            $bulk->insert($document);
            
            $writeConcern = new \MongoDB\Driver\WriteConcern(\MongoDB\Driver\WriteConcern::MAJORITY, 1000);
            return $this->client->executeBulkWrite($this->database . '.' . $collection, $bulk, $writeConcern);
        } catch (Exception $e) {
            log_message('error', 'MongoDB Insert Error: ' . $e->getMessage());
            throw new Exception('MongoDB veri ekleme hatası: ' . $e->getMessage());
        }
    }
    
    public function executeQuery($collection, $filter = [])
    {
        try {
            $query = new \MongoDB\Driver\Query($filter);
            return $this->client->executeQuery($this->database . '.' . $collection, $query);
        } catch (Exception $e) {
            log_message('error', 'MongoDB Query Error: ' . $e->getMessage());
            throw new Exception('MongoDB sorgu hatası: ' . $e->getMessage());
        }
    }
}
