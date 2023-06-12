<?php

$folderPath = '/path/to/folder/on/share';

$state = smbclient_state_new();
smbclient_state_set_credentials($state, 'public', 'PublicPassword');
smbclient_state_set_options($state, 'version', 'workgroup');

if (smbclient_state_connect($state, 'smb://gfranz.ch/Cube') === false) {
    die('Failed to connect to SMB share.');
}

$folderHandle = smbclient_state_opendir($state, $folderPath);

while (($file = smbclient_state_readdir($state, $folderHandle)) !== false) {
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif'])) {
        $filePath = $folderPath . $file['name'];
        $fileHandle = smbclient_state_open($state, $filePath);
        $fileContent = smbclient_state_read($state, $fileHandle);
        
        // Create image resource using GD
        $image = imagecreatefromstring($fileContent);
        
        // Display the image
        header('Content-Type: image/jpeg'); // Adjust the content type based on the image format
        imagejpeg($image);
        
        // Clean up
        imagedestroy($image);
        smbclient_state_close($state, $fileHandle);
    }
}

smbclient_state_closedir($state, $folderHandle);
smbclient_state_disconnect($state);
smbclient_state_free($state);
?>
