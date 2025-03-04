<?php
namespace app\api\controller\script;
use think\Request;

class CalculateNIR
{

    public function helloWorld() {
        return "yoman";
    }
    
    public function calculateSimilarity(Request $request) {
        // Get the raw POST data (JSON)
        $data = $request->getContent();
        
        // Decode the JSON into an associative array
        $input = json_decode($data, true);
        
        // Validate the input
        if (!isset($input['pixels']) || !is_array($input['pixels']) || empty($input['pixels'])) {
            return json(['error' => 'Invalid or missing pixel array'], 400);
        }

        // Validate category
        if (!isset($input['category']) || !is_string($input['category']) || empty($input['category'])) {
            return json(['error' => 'Invalid or missing category'], 400);
        }
        
        // Validate grade
        if (!isset($input['productName']) || !is_string($input['productName']) || empty($input['productName'])) {
            return json(['error' => 'Invalid or missing productName'], 400);
        }

        // Validate the input
        if (!isset($input['username']) || !is_string($input['username']) || empty($input['username'])) {
            return json(['error' => 'Invalid or missing username'], 400);
        }

        $pixels = $input['pixels'];
        $category = $input['category'];
        $productName = $input['productName'];
        $username = $input['username'];

        $pixels_json = json_encode($pixels);
        $category_escaped = escapeshellarg($category);
        $productName_escaped = escapeshellarg($productName);
        $username_escaped = escapeshellarg($username);
        
        // Build and execute the Python command
        $command = 'python3 /app/python-script/calculate_similarity.py ' . 
                   escapeshellarg($pixels_json) . ' ' . 
                   $category_escaped . ' ' . 
                   $productName_escaped;
        $output = shell_exec($command);

        $uploadScanData = new \app\api\controller\scan\UploadScanData();
        $uploadScanData->uploadScanData(trim($output), $productName, $category, $username);

        // Check for execution errors
        if ($output === null) {
            error_log("Shell exec failed for command: " . $command);
            return json(['error' => 'Failed to execute Python script'], 500);
        }

        return json(['similarity' => trim($output)]);
    }
}