<?php

define('CRUNCHBASE_DEBUG', true);
define('CRUNCHBASE_API_KEY', YOUR_API_KEY_HERE);
require_once('Crunchbase-Php-Wrapper-Class/lib/Crunchbase.php');

// Entity Information 
$crunchbase = new Crunchbase;
$query = new StdClass();
$query->entity = 'person';
$query->name = 'Bill Gates';
$result = $crunchbase->entity_info($query);

// Entity List
$query = new StdClass();
$query->entity = 'companies';
$result = $crunchbase->entity_list($query);

// Search
$query = new StdClass();
$query->query = 'Hacker';
$result = $crunchbase->search($query);

// Posts
$query = new StdClass();
$query->entity = 'companies';
$query->name = 'microsoft';
$query->first_name = 'Bill';
$query->last_name = 'Gates';
$result = $crunchbase->posts($query);

?>