<?php
namespace app\api\controller\script;
use think\Request;

class CalculateNIR
{
    public function testScript()
    {
        $pixels = [34, 33, 33, 35, 34, 33, 35, 36, 37, 36, 38, 38, 36, 37, 34, 37, 37, 36, 36, 38, 38, 37, 39, 38, 36, 37, 37, 36, 37, 36, 36, 38, 37, 34, 37, 36, 34, 36, 35, 38, 37, 37, 39, 40, 40, 42, 40, 40, 42, 42, 42, 40, 40, 46, 43, 42, 46, 43, 42, 46, 44, 44, 44, 45, 43, 44, 43, 44, 45, 44, 48, 46, 46, 46, 46, 47, 45, 49, 47, 48, 48, 47, 48, 48, 47, 49, 50, 48, 50, 50, 50, 50, 52, 50, 50, 50, 50, 52, 53, 50, 52, 53, 51, 50, 50, 49, 53, 52, 51, 51, 53, 52, 54, 54, 52, 55, 56, 55, 56, 56, 57, 57, 56, 59, 58, 59, 60, 59, 60, 59, 60, 61, 59, 61, 64, 62, 60, 64, 64, 64, 64, 63, 64, 62, 63, 62, 63, 63, 66, 64, 64, 61, 64, 64, 65, 64, 62, 63, 64, 66, 64, 68, 64, 66, 66, 64, 66, 66, 68, 67, 68, 68, 70, 70, 66, 69, 70, 68, 72, 69, 71, 70, 71, 70, 72, 71, 72, 71, 73, 74, 70, 73, 72, 74, 73, 73, 74, 68, 72, 70, 71, 75, 71, 74, 72, 72, 73, 74, 76, 75, 74, 74, 75, 74, 76, 76, 74, 74, 74, 74, 75, 75, 74, 75, 76, 76, 76, 75, 78, 77, 75, 76, 76, 76, 79, 78, 75, 74, 74, 74, 76, 74, 73, 76, 71, 75, 76, 75, 72, 72, 74, 72, 73, 72, 76, 75, 74, 72, 74, 74, 74, 74, 73, 74, 73, 74, 75, 75, 76, 78, 75, 75, 77, 77, 73, 75, 75, 74, 76, 72, 74, 76, 74, 74, 75, 72, 72, 72, 71, 71, 70, 72, 67, 69, 70, 66, 67, 70, 66, 69, 67, 67, 64, 69, 64, 66, 64, 65, 67, 66, 64, 64, 62, 64, 64, 62, 62, 60, 59, 60, 57, 56, 58, 59, 58, 55, 55, 53, 51, 49, 51, 49, 49, 48, 46, 46, 46, 43, 44, 43, 41, 40, 40, 39, 39, 40, 38, 37, 36, 36, 36, 35, 35, 34, 32, 30, 30, 30, 30, 30, 27, 29, 28, 26, 26, 24, 25, 26, 24, 22, 22, 20, 21, 21, 19, 18, 18, 16, 16, 18, 16, 16, 16, 16, 14, 16, 16, 15, 14, 15, 14, 15, 12, 14, 13, 13, 11, 12, 13, 12, 14, 12, 11, 12, 11, 10, 10, 11, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 9, 8, 10, 10, 9, 9, 9, 9, 8, 10, 8, 9, 10, 9, 10, 10, 9, 9, 10, 8, 8, 9, 8, 10, 9, 8, 8, 8, 9, 9, 9, 10, 10, 8, 8, 9, 10, 8, 9, 9, 8, 8, 9, 9, 10, 8, 9, 10, 8, 9, 9, 8, 8, 9, 9, 8, 8, 8, 8, 8, 8, 10, 9, 9, 9, 9, 8, 9, 9, 8, 10, 8, 8, 8, 8, 8, 8, 8, 8, 8, 8, 9, 8, 10, 7, 9, 9, 8, 9, 9, 9, 9, 8, 8, 10, 8, 8, 10, 9, 8, 9, 9, 9, 10, 8, 8, 8, 10, 8, 9, 9, 8, 8, 10, 9, 8, 8, 8, 9, 7, 10, 9, 8, 9, 8, 9, 8, 9, 8, 9, 9, 8, 9, 10, 10, 8, 9, 7, 8, 9, 8, 8, 9, 10, 9, 8, 8, 8, 10, 8, 9, 8, 8, 9, 8, 8, 8, 9, 8, 8, 9, 9, 8, 8, 8, 9, 10, 7, 8, 8, 8, 9, 8, 8, 8, 8, 7, 9, 8, 9, 8, 8];
        $pixels_json = json_encode($pixels);
        $command = 'python3 /app/Python-script/calculate_similarity.py ' . escapeshellarg($pixels_json);
        $output = shell_exec($command);
        return json(['similarity' => trim($output)]);
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
        
        $pixels = $input['pixels'];
        $pixels_json = json_encode($pixels);
        
        // Build and execute the Python command
        $command = 'python3 /app/python-script/calculate_similarity.py ' . escapeshellarg($pixels_json);
        $output = shell_exec($command);
        
        // Check for execution errors
        if ($output === null) {
            error_log("Shell exec failed for command: " . $command);
            return json(['error' => 'Failed to execute Python script'], 500);
        }
        
        return json(['similarity' => trim($output)]);
    }
}