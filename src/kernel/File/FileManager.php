<?php
namespace Lxh\File;

use Dotenv\Exception\InvalidFileException;
use Lxh\Config\Config;
use Lxh\Exceptions\Error;
use Lxh\Helper\Util;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileManager
{
    private $root = __ROOT__;

    /**
     * @var Permission
     */
    private $permission;

    private $separator = DIRECTORY_SEPARATOR;

    private $permissionDeniedList = [];

    public function __construct(Config $config = null)
    {
        $params = null;

        if ($config) {
            $params = array(
                'defaultPermissions' => $config->get('defaultPermissions'),
                'permissionMap' => $config->get('permissionMap'),
            );
        }

        $this->permission = new Permission($this, $params);
    }

    /**
     * Get a list of files in specified directory
     *
     * @param string $path string - Folder path, Ex. myfolder
     * @param bool | int $recursively - Find files in subfolders
     * @param string $filter - Filter for files. Use regular expression, Ex. \.json$
     * @param bool $onlyFileType [null, true, false] - Filter for type of files/directories. If TRUE - returns only file list, if FALSE - only directory list
     * @param bool $isReturnSingleArray - if need to return a single array of file list
     *
     * @return array
     */
    public function getFileList($path, $recursively = false, $filter = '', $onlyFileType = null, $isReturnSingleArray = false)
    {
        $path = $this->concatPaths($path);

        $result = array();

        if (!is_dir($path)) {
            return $result;
        }

        $cdir = scandir($path);
        foreach ($cdir as $key => & $value) {
            if (!in_array($value, array(".", ".."))) {
                $add = false;
                if (is_dir($path . $this->separator . $value)) {
                    if ($recursively || (is_int($recursively) && $recursively != 0)) {
                        $nextRecursively = is_int($recursively) ? ($recursively - 1) : $recursively;
                        $result[$value] = $this->getFileList($path . $this->separator . $value, $nextRecursively, $filter, $onlyFileType);
                    } else if (!isset($onlyFileType) || !$onlyFileType) { /*save only directories*/
                        $add = true;
                    }
                } else if (!isset($onlyFileType) || $onlyFileType) { /*save only files*/
                    $add = true;
                }

                if ($add) {
                    if (!empty($filter)) {
                        if (preg_match('/' . $filter . '/i', $value)) {
                            $result[] = $value;
                        }
                    } else {
                        $result[] = $value;
                    }
                }

            }
        }

        if ($isReturnSingleArray) {
            return $this->getSingeFileList($result, $onlyFileType);
        }

        return $result;
    }

    /**
     * Store the uploaded file on the disk with a given name.
     *
     * @param  string  $path
     * @param  UploadedFile  $file
     * @param  string  $name
     * @param  array  $options
     * @return string|false
     */
    public function putFileAs($path, $file, $name, $options = [])
    {
        $stream = fopen($file->getRealPath(), 'r+');

        // Next, we will format the path of the file and store the file using a stream since
        // they provide better performance than alternatives. Once we write the file this
        // stream will get closed automatically by us so the developer doesn't have to.
        $result = $this->putContents(
            $path = trim($path.'/'.$name, '/'), $stream, $options
        );

        if (is_resource($stream)) {
            fclose($stream);
        }

        return $result ? $path : false;
    }

    /**
     * Convert file list to a single array
     *
     * @param array $fileList
     * @param bool $onlyFileType [null, true, false] - Filter for type of files/directories.
     * @param string $parentDirName
     *
     * @return array
     */
    protected function getSingeFileList(array $fileList, $onlyFileType = null, $parentDirName = '')
    {
        $singleFileList = array();
        foreach ($fileList as $dirName => & $fileName) {

            if (is_array($fileName)) {
                $currentDir = $this->concatPath($parentDirName, $dirName);

                if (!isset($onlyFileType) || $onlyFileType == is_file($currentDir)) {
                    $singleFileList[] = $currentDir;
                }

                $singleFileList = array_merge($singleFileList, $this->getSingeFileList($fileName, $onlyFileType, $currentDir));

            } else {
                $currentFileName = $this->concatPath($parentDirName, $fileName);

                if (!isset($onlyFileType) || $onlyFileType == is_file($currentFileName)) {
                    $singleFileList[] = $currentFileName;
                }
            }
        }

        return $singleFileList;
    }

    public function concatPath($folderPath, $filePath = null)
    {
        if (is_array($folderPath)) {
            $fullPath = '';
            foreach ($folderPath as & $path) {
                $fullPath = $this->concatPath($fullPath, $path);
            }
            return $fullPath;
        }

        if (empty($filePath)) {
            return $folderPath;
        }
        if (empty($folderPath)) {
            return $filePath;
        }

        if (substr($folderPath, -1) == $this->separator) {
            return $folderPath . $filePath;
        }
        return $folderPath . $this->separator . $filePath;
    }

    public function fixPath($path)
    {
        return str_replace('/', $this->separator, $path);
    }

    /**
     * Reads entire file into a string
     *
     * @param  string | array $path Ex. 'path.php' OR array('dir', 'path.php')
     * @param  boolean $useIncludePath
     * @param  resource $context
     * @param  integer $offset
     * @param  integer $maxlen
     * @return mixed
     */
    public function getContents($path, $useIncludePath = false, $context = null, $offset = -1, $maxlen = null)
    {
        $fullPath = $this->concatPaths($path);

        if (is_file($fullPath)) {
            if (isset($maxlen)) {
                return file_get_contents($fullPath, $useIncludePath, $context, $offset, $maxlen);
            } else {
                return file_get_contents($fullPath, $useIncludePath, $context, $offset);
            }
        }

        return false;
    }

    /**
     * Get the contents of a file.
     *
     * @param  string  $path
     * @param  bool  $lock
     * @return string
     *
     * @throws \Error
     */
    public function get($path, $lock = false)
    {
        if (is_file($path)) {
            return $lock ? $this->sharedGet($path) : file_get_contents($path);
        }

        throw new Error("File does not exist at path {$path}");
    }

    /**
     * Get contents of a file with shared access.
     *
     * @param  string  $path
     * @return string
     */
    public function sharedGet($path)
    {
        $contents = '';

        $handle = fopen($path, 'rb');

        if ($handle) {
            try {
                if (flock($handle, LOCK_SH)) {
                    clearstatcache(true, $path);

                    $contents = fread($handle, filesize($path) ?: 1);

                    flock($handle, LOCK_UN);
                }
            } finally {
                fclose($handle);
            }
        }

        return $contents;
    }

    /**
     * Get PHP array from PHP file
     *
     * @param  string | array $path
     * @return array
     */
    public function getPhpContents($path)
    {
        $fullPath = $this->concatPaths($path);

        if (is_file($fullPath)) {
            return (array) include($fullPath);
        }

        return [];
    }

    /**
     * Write data to a file
     *
     * @param  string | array $path
     * @param  mixed $data
     * @param  integer $flags LOCK_EX
     * @param  resource $context
     *
     * @return bool
     */
    public function putContents($path, & $data, $lock = false, $context = null)
    {
        $fullPath = $this->concatPaths($path); //todo remove after changing the params

        if ($this->checkCreateFile($fullPath) === false) {
            throw new InvalidFileException('Permission denied for ' . $fullPath);
        }

        $res = (file_put_contents($fullPath, $data, $lock ? LOCK_EX : 0, $context) !== false);

        if ($res && function_exists('opcache_invalidate')) {
            // 清除文件缓存
            opcache_invalidate($fullPath);
        }

        return $res;
    }

    /**
     * Save PHP content to file
     *
     * @param string | array $path
     * @param bool $numericKey Output the numeric key
     * @param string $data
     * @param bool $readable 是否写入易读的数组格式（使用易读模式效率较低）
     * @return bool
     */
    public function putPhpContents($path, array $data, $readable = false)
    {
        if ($readable) {
            $txt = "<?php \nreturn " . Util::arrayToText($data) . ";\n";
        } else {
            $txt = "<?php \nreturn " . var_export($data, true) . ";\n";
        }

        return $this->putContents($path, $txt, LOCK_EX);
    }

    /**
     * Save JSON content to file
     *
     * @param string | array $path
     * @param string $data
     * @param  integer $flags
     * @param  resource $context
     *
     * @return bool
     */
    public function putContentsJson($path, $data)
    {
        if (is_array($data)) {
            $data = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }

        return $this->putContents($path, $data, LOCK_EX);
    }

    /**
     * Unset some element of content data
     *
     * @param  string | array $path
     * @param  array | string $unsets
     * @param  bool $isPHP
     * @param  bool $toJSON
     * @return bool
     */
    public function unsetContents($path, $unsets, $isPHP = true, $toJSON = false)
    {
        if ($isPHP) {
            $currentData = $this->getPhpContents($path);
        } else {
            $currentData = $this->getContents($path);
        }
        if ($currentData == false) {
            logger()->notice('FileManager::unsetContents: File [' . $currentData . '] does not exist.');
            return false;
        }

        $currentDataArray = &$currentData;

        $unsettedData = Util::unsetInArray($currentDataArray, $unsets);

        if (is_null($unsettedData) || (is_array($unsettedData) && empty($unsettedData))) {
            $fullPath = $this->concatPaths($path);
            return $this->unlink($fullPath);
        }

        if ($toJSON) {
            return $this->putContentsJson($path, $unsettedData);
        }

        if ($isPHP) {
            return $this->putPhpContents($path, $unsettedData);
        }

        return $this->putContents($path, $unsettedData);
    }


    /**
     * Merge PHP content and save it to a file
     *
     * @param string | array $path
     * @param string $content JSON string
     * @param string | array $removeOptions - List of unset keys from content
     * @param bool $readable
     * @return bool
     */
    public function mergePhpContents($path, & $content, $removeOptions = null, $readable = false)
    {
        return $this->mergeContents($path, $content, $removeOptions, true, $readable);
    }

    /**
     * Append the content to the end of the file
     *
     * @param string | array $path
     * @param mixed $data
     *
     * @return bool
     */
    public function appendContents($path, $data)
    {
        return $this->putContents($path, $data, FILE_APPEND | LOCK_EX);
    }

    /**
     * Concat paths
     * @param  string | array $paths Ex. array('pathPart1', 'pathPart2', 'pathPart3')
     * @return string
     */
    protected function concatPaths($paths)
    {
        if (is_string($paths)) {
            return $paths;
        }

        $fullPath = '';
        foreach ($paths as & $path) {
            $fullPath = $this->concatPath($fullPath, $path);
        }

        return $fullPath;
    }

    /**
     * Create a new dir
     *
     * @param  string | array $path
     * @param  int $permission - ex. 0755
     * @param  bool $recursive
     *
     * @return bool
     */
    public function mkdir($path, $permission = null, $recursive = false)
    {
        $fullPath = $this->concatPaths($path);

        if (is_file($fullPath) && is_dir($path)) {
            return true;
        }

        $defaultPermissions = $this->permission->getDefaultPermissions();

        if (!isset($permission)) {
            $permission = (string)$defaultPermissions['dir'];
            $permission = base_convert($permission, 8, 10);
        }

        $result = mkdir($fullPath, $permission, true);

        if (!empty($defaultPermissions['user'])) {
            $this->permission->chown($fullPath);
        }

        if (!empty($defaultPermissions['group'])) {
            $this->permission->chgrp($fullPath);
        }

        return isset($result) ? $result : false;
    }

    /**
     * Copy files from one direcoty to another
     * Ex. $sourcePath = 'data/uploads/extensions/file.json', $destPath = 'data/uploads/backup', result will be data/uploads/backup/data/uploads/backup/file.json.
     *
     * @param  string $sourcePath
     * @param  string $destPath
     * @param  boolean $recursively
     * @param  array $fileList - list of files that should be copied
     * @param  boolean $copyOnlyFiles - copy only files, instead of full path with directories, Ex. $sourcePath = 'data/uploads/extensions/file.json', $destPath = 'data/uploads/backup', result will be 'data/uploads/backup/file.json'
     * @return boolen
     */
    public function copy($sourcePath, $destPath, $recursively = false, array $fileList = null, $copyOnlyFiles = false)
    {
        $sourcePath = $this->concatPaths($sourcePath);
        $destPath = $this->concatPaths($destPath);

        if (isset($fileList)) {
            if (!empty($sourcePath)) {
                foreach ($fileList as &$fileName) {
                    $fileName = $this->concatPaths(array($sourcePath, $fileName));
                }
            }
        } else {
            $fileList = is_file($sourcePath) ? (array)$sourcePath : $this->getFileList($sourcePath, $recursively, '', true, true);
        }

        /** Check permission before copying */
        $permissionDeniedList = array();
        foreach ($fileList as & $file) {

            if ($copyOnlyFiles) {
                $file = pathinfo($file, PATHINFO_BASENAME);
            }

            $destFile = $this->concatPaths(array($destPath, $file));

            $isFileExists = is_file($destFile);

            if ($this->checkCreateFile($destFile) === false) {
                $permissionDeniedList[] = $destFile;
            } else if (!$isFileExists) {
                $this->removeFile($destFile);
            }
        }
        /** END */

        if (!empty($permissionDeniedList)) {
            $betterPermissionList = $this->permission->arrangePermissionList($permissionDeniedList);
            throw new InvalidFileException("Permission denied for " . implode(", ", $betterPermissionList));
        }

        $res = true;
        foreach ($fileList as & $file) {

            if ($copyOnlyFiles) {
                $file = pathinfo($file, PATHINFO_BASENAME);
            }

            $sourceFile = is_file($sourcePath) ? $sourcePath : $this->concatPaths(array($sourcePath, $file));
            $destFile = $this->concatPaths(array($destPath, $file));

            if (is_file($sourceFile) && is_file($sourceFile)) {
                $res &= copy($sourceFile, $destFile);
            }
        }

        return $res;
    }

    /**
     * @param $src
     * @param $dst
     */
    public function recurseCopy($src, $dst)
    {
        $dir = opendir($src);

        if (! is_dir($dst)) {
            $this->mkdir($dst, null, true);
        }

        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    $this->recurseCopy($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    /**
     * Merge file content and save it to a file
     *
     * @param string | array $path
     * @param string $content JSON string
     * @param string | array $removeOptions - List of unset keys from content
     * @param bool $isPhp - Is merge php files
     * @param bool $readable
     * @return bool | array
     */
    public function mergeContents($path, &$content, $removeOptions = null, $isPhp = true, $readable = false)
    {
        if ($isPhp) {
            $fileContent = $this->getPhpContents($path);
        } else {
            $fileContent = (array)$this->getContents($path);
        }

        $savedDataArray = &$fileContent;
        $newDataArray = &$content;

        if (isset($removeOptions)) {

        }

        Util::merge($savedDataArray, $newDataArray, true);

        if ($isPhp) {
            return $this->putPhpContents($path, $savedDataArray, $readable);
        }

        return $this->putContents($path, $savedDataArray);
    }

    /**
     * Create a new file if not exists with all folders in the path.
     *
     * @param string $filePath
     * @return string
     */
    public function checkCreateFile($filePath)
    {
        $defaultPermissions = $this->permission->getDefaultPermissions();

        if (is_file($filePath)) {

            if (!in_array($this->permission->getCurrentPermission($filePath), array($defaultPermissions['file'], $defaultPermissions['dir']))) {
                return $this->permission->setDefaultPermissions($filePath, true);
            }
            return true;
        }

        $pathParts = pathinfo($filePath);
        if (!is_dir($pathParts['dirname'])) {
            $dirPermission = $defaultPermissions['dir'];
            $dirPermission = is_string($dirPermission) ? base_convert($dirPermission, 8, 10) : $dirPermission;

            if (!$this->mkdir($pathParts['dirname'], $dirPermission, true)) {
                throw new InvalidFileException('Permission denied: unable to create a folder on the server - ' . $pathParts['dirname']);
            }
        }

        if (touch($filePath)) {
            return $this->permission->setDefaultPermissions($filePath, true);
        }

        return false;
    }

    /**
     * Remove file/files by given path
     *
     * @param array $filePaths - File paths list
     * @return bool
     */
    public function unlink($filePaths)
    {
        return $this->removeFile($filePaths);
    }

    public function rmdir($dirPaths)
    {
        $result = true;
        foreach ((array)$dirPaths as $dirPath) {
            if (is_dir($dirPath) && is_writable($dirPath)) {
                $result &= rmdir($dirPath);
            }
        }

        return (bool)$result;
    }

    /**
     * Remove file/files by given path
     *
     * @param array $filePaths - File paths list
     * @param string $dirPath - directory path
     * @return bool
     */
    public function removeFile($filePaths, $dirPath = null)
    {
        $result = true;
        foreach ((array)$filePaths as & $filePath) {
            if (isset($dirPath)) {
                $filePath = $this->concatPath($dirPath, $filePath);
            }

            if (is_file($filePath)) {
                $result &= unlink($filePath);
            }
        }

        return $result;
    }

    /**
     * Remove all files inside given path
     *
     * @param string $dirPath - directory path
     * @param bool $removeWithDir - if remove with directory
     *
     * @return bool
     */
    public function removeInDir($dirPath, $removeWithDir = false)
    {
        $fileList = $this->getFileList($dirPath, false);

        $result = true;
        foreach ((array)$fileList as & $file) {
            $fullPath = $this->concatPath($dirPath, $file);
            if (is_dir($fullPath)) {
                $result &= $this->removeInDir($fullPath, true);
            } else if (is_file($fullPath)) {
                $result &= unlink($fullPath);
            }
        }

        if ($removeWithDir) {
            $result &= $this->rmdir($dirPath);
        }

        return (bool)$result;
    }

    /**
     * Remove items (files or directories)
     *
     * @param  string | array $items
     * @param  string $dirPath
     * @return boolean
     */
    public function remove($items, $dirPath = null, $removeEmptyDirs = false)
    {
        $permissionDeniedList = array();
        foreach ((array)$items as & $item) {
            if (isset($dirPath)) {
                $item = $this->concatPath($dirPath, $item);
            }

            if (!is_writable($item)) {
                $permissionDeniedList[] = $item;
            } else if (!is_writable(dirname($item))) {
                $permissionDeniedList[] = dirname($item);
            }
        }

        if (!empty($permissionDeniedList)) {
            $betterPermissionList = $this->permission->arrangePermissionList($permissionDeniedList);
            throw new InvalidFileException('Permission denied for <br>' . implode(', <br>', $betterPermissionList));
        }

        $result = true;
        foreach ((array)$items as & $item) {
            if (isset($dirPath)) {
                $item = $this->concatPath($dirPath, $item);
            }

            if (is_dir($item)) {
                $result &= $this->removeInDir($item, true);
            } else {
                $result &= $this->removeFile($item);
            }

            if ($removeEmptyDirs) {
                $result &= $this->removeEmptyDirs($item);
            }
        }

        return (bool)$result;
    }

    /**
     * Remove empty parent directories if they are empty
     * @param  string $path
     * @return bool
     */
    protected function removeEmptyDirs($path)
    {
        $parentDirName = $this->getParentDirName($path);

        $res = true;
        if ($this->isDirEmpty($parentDirName)) {
            $res &= $this->rmdir($parentDirName);
            $res &= $this->removeEmptyDirs($parentDirName);
        }

        return (bool)$res;
    }

    /**
     * Check if directory is empty
     * @param  string $path
     * @return boolean
     */
    public function isDirEmpty($path)
    {
        if (is_dir($path)) {
            $fileList = $this->getFileList($path, true);

            if (is_array($fileList) && empty($fileList)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get a filename without the file extension
     *
     * @param string $filename
     * @param string $ext - extension, ex. '.json'
     *
     * @return array
     */
    public function getFileName($fileName, $ext = '')
    {
        if (empty($ext)) {
            $fileName = substr($fileName, 0, strrpos($fileName, '.', -1));
        } else {
            if (substr($ext, 0, 1) != '.') {
                $ext = '.' . $ext;
            }

            if (substr($fileName, -(strlen($ext))) == $ext) {
                $fileName = substr($fileName, 0, -(strlen($ext)));
            }
        }

        $exFileName = explode('/', $this->toFormat($fileName, '/'));

        return end($exFileName);
    }

    /**
     * Convert to format with defined delimeter
     * ex. Fox/Utils to Fox\Utils
     *
     * @param string $name
     * @param string $delim - delimiter
     *
     * @return string
     */
    public function toFormat($name, $delim = '/')
    {
        return preg_replace("/[\/\\\]/", $delim, $name);
    }

    /**
     * Get a directory name from the path
     *
     * @param string $path
     * @param bool $isFullPath
     *
     * @return array
     */
    public function getDirName($path, $isFullPath = true, $useIsDir = true)
    {
        $dirName = preg_replace('/\/$/i', '', $path);
        $dirName = ($useIsDir && is_dir($dirName)) ? $dirName : pathinfo($dirName, PATHINFO_DIRNAME);

        if (!$isFullPath) {
            $pieces = explode('/', $dirName);
            $dirName = $pieces[count($pieces) - 1];
        }

        return $dirName;
    }

    /**
     * Get parent dir name/path
     *
     * @param  string $path
     * @param  boolean $isFullPath
     * @return string
     */
    public function getParentDirName($path, $isFullPath = true)
    {
        return $this->getDirName($path, $isFullPath, false);
    }

    /**
     * Check if $paths are writable. Permission denied list are defined in getLastPermissionDeniedList()
     *
     * @param  array $paths
     *
     * @return boolean
     */
    public function isWritableList(array $paths)
    {
        $permissionDeniedList = array();

        $result = true;
        foreach ($paths as & $path) {
            $rowResult = $this->isWritable($path);
            if (!$rowResult) {
                $permissionDeniedList[] = $path;
            }
            $result &= $rowResult;
        }

        if (!empty($permissionDeniedList)) {
            $this->permissionDeniedList = $this->permission->arrangePermissionList($permissionDeniedList);
        }

        return (bool)$result;
    }

    /**
     * Get last permission denied list
     *
     * @return array
     */
    public function getLastPermissionDeniedList()
    {
        return $this->permissionDeniedList;
    }

    /**
     * Check if $path is writable
     *
     * @param  string | array $path
     *
     * @return boolean
     */
    public function isWritable($path)
    {
        $existFile = $this->getExistsPath($path);

        return is_writable($existFile);
    }

    /**
     * Get exists path. Ex. if check /var/www/answeredtime/custom/someFile.php and this file doesn't extist, result will be /var/www/answeredtime/custom
     *
     * @param  string | array $path
     *
     * @return string
     */
    protected function getExistsPath($path)
    {
        $fullPath = $this->concatPaths($path);

        if (!is_file($fullPath)) {
            $fullPath = $this->getExistsPath(pathinfo($fullPath, PATHINFO_DIRNAME));
        }

        return $fullPath;
    }

}
