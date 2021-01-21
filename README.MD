Laravel/PHP package for google drive API
-------------------

1) Authenticate to GoogleDrive
2) Add file/directory
4) Edit file/directory
5) Download file/directory
6) Delete file/directory

Installation
-------------

```bash
composer require miqoo1996/google-drive
``` 

* add this parameters in you .env file
```dotenv
GDrive_client_id=
GDrive_client_secret=
GDrive_client_redirect_url=
```

Instructions
-------------

```php
// Get the API client and construct the service object.
$api = new GoogleDriveRepository();
```

* to get URL to login or update account (access_token) see the below example
```html
  <a href="<?= $api->getClient()->createAuthUrl(); ?>">Click To Login or change current account.</a> 
```

```php

// or you can check this in session.
if ($api->getClient()->getAccessToken()) {
    
    /-------------------------- Example --------------------------/
    
    // get all files populated on the root path
    $api->getRootFiles(function (Google_Service_Drive_DriveFile $file, int $line) {
        if ($line === 1) {
            echo '<h1>All files/folders in root DIR.</h1>';
        }

        printf("<p>ID = %s || File = %s || mimeType = %s</p><hr>", $file->getId(), $file->getName(), $file->mimeType);
    });
    
    /-------------------------- Example --------------------------/
    
    
    /-------------------------- Example --------------------------/
    
    // get files with the given folder id.
    // as well as you can use with callback like the above example.
    $files = $api->getFilesByFolder('-- folder id --');
    
    /-------------------------- Example --------------------------/
    
    
    /-------------------------- Example --------------------------/

    // create new folder
    $file = $api->createFolder('folder_name', 'aaaa');

    // update Folder by ID
    $api->updateFolder('new-aaaa', $file->getId());
    
    /-------------------------- Example --------------------------/
    
    
    /-------------------------- Example --------------------------/

    // delete file/folder with the given id
    $api->deleteFile('1E8930HbZLjnl_shQWoIuudK0RZQWxpgb');
    
    /-------------------------- Example --------------------------/
    
    
    /-------------------------- Example --------------------------/

    // add file on drive with the given path
    // To create a file we can use this function as well
    $api->uploadFile('root', __DIR__, '00.html');
    
    /-------------------------- Example --------------------------/
    
    

    /*------- File Creation and Modifications  -------*/

    $file = $api->createFile([
        'name' => 'test.csv',
        'parents' => ['root'],
        'data' => '77777',
        'mimeType' => 'text/csv',
        'uploadType' => 'multipart'
    ]);

    $api->updateFile($file->getId(), [
        'name' => 'test_new.csv',
        'parents' => ['root'],
        'data' => 'new 77777',
        'mimeType' => 'text/csv',
        'uploadType' => 'multipart'
    ]);

    // file be stored/downloaded with this name: $id.mimetype
    // As its written on there documentation, Google Drive allows to upload binary files only.
    $api->downloadFile('125a2fLbBZwKTjj-D0IuoM3EQwtTCDQv2m6E9OW_iKOY', __DIR__);
}
```