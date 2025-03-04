<?php
namespace app\api\controller\scan;
use think\Request;
use think\Db;

class UploadScanData
{
    public function uploadScanData($similarity, $productName, $category, $username)
    {
        // Get the current date and time in the desired format
        $dateAndTime = date('Y/m/d, g:ia');

        // Create a new entry in the scan_history table
        $result = Db::name('scan_history')->insert([
            'username' => $username,
            'category' => $category,
            'date_and_time' => $dateAndTime,
            'product_name' => $productName,
            'similarity' => $similarity
        ]);

        // Check for successful insertion
        if ($result) {
            return json(['success' => true, 'message' => 'Data uploaded successfully']);
        } else {
            return json(['success' => false, 'message' => 'Failed to upload data'], 500);
        }
    }
}