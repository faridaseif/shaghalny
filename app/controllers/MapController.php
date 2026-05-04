<?php
// Add the constant file at the top of your main entry point (or here for testing)


 

class MapController {
    
    public function map() {
        
        // 1. INCLUDE DATABASE CONNECTION - NOW USING ABSOLUTE PATH
        require_once APP_ROOT . '/config/database.php';
        
        // 2. INCLUDE MODEL - NOW USING ABSOLUTE PATH
        require_once APP_ROOT . '/app/models/Map.php'; 

        // 3. INSTANTIATE MODEL
        // ... (rest of the code remains the same) ...
        $db = Database::getInstance()->getConnection();
        $mapModel = new Map($db);
        
        // 4. FETCH DATA
        $jobs = $mapModel->getAllJobs();

        // REMOVE THE TEMPORARY 'echo' and 'die()' when paths are fixed
        
        $data = [
            'jobs' => $jobs,
            'title' => 'Job Map'
        ];
        
        $this->view('map/jobs_map', $data); 
    }

    private function view($view, $data = []) {
        extract($data); 
        // 6. LOAD VIEW - Adjust path as needed relative to this file
        require_once APP_ROOT . '/app/views/' . $view . '.php'; 
    }
}
?>