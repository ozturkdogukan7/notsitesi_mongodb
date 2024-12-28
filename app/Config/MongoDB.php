<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class MongoDB extends BaseConfig
{
    public $host = 'localhost';
    public $port = 27017;
    public $database = 'notes_db';
    public $username = '';
    public $password = '';
}
