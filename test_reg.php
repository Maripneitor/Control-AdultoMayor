<?php
require 'config/config.php';
require 'config/Database.php';
require 'src/Core/Model.php';
require 'src/Models/Paciente.php';

try {
    $p = new App\Models\Paciente();
    $id = $p->createInitialProfile(3, "Expediente de Test");
    echo 'Profile OK ' . $id;
} catch (Exception $e) {
    echo 'ERR ' . $e->getMessage();
}
