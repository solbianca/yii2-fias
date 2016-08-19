<?php
namespace solbianca\fias\console\base;

use solbianca\fias\helpers\FileHelper;
use solbianca\fias\models\FiasUpdateLog;

/**
 * Class Loader
 * @package solbianca\fias\console\base
 *
 * Обертка для загрузки базы на сервер
 */
class Loader
{
    /**
     * @var string
     */
    protected $wsdlUrl;

    /**
     * Directory to upload file
     * @var string
     */
    protected $fileDirectory;

    /**
     * @var SoapResultWrapper
     */
    protected $fileInfoResult = null;

    /**
     * @param $wsdlUrl
     * @param $fileDirectory
     * @throws \yii\base\InvalidConfigException
     */
    public function __construct($wsdlUrl, $fileDirectory)
    {
        $this->wsdlUrl = $wsdlUrl;
        $this->fileDirectory = $fileDirectory;

        FileHelper::ensureIsDirectory($fileDirectory);
        FileHelper::ensureIsWritable($fileDirectory);
    }

    /**
     * Get actual fias base information: version and url's to download files
     *
     * @return SoapResultWrapper
     */
    public function getLastFileInfo()
    {
        if (!$this->fileInfoResult) {
            $this->fileInfoResult = $this->getLastFileInfoRaw();
        }

        return $this->fileInfoResult;
    }

    /**
     * @return SoapResultWrapper
     */
    protected function getLastFileInfoRaw()
    {
        $client = new \SoapClient($this->wsdlUrl);
        $rawResult = $client->__soapCall('GetLastDownloadFileInfo', []);

        return new SoapResultWrapper($rawResult);
    }

    /**
     * Download file from fias server
     *
     * @param $fileName
     * @param $url
     * @return string
     */
    protected function loadFile($fileName, $url)
    {
        $filePath = $this->fileDirectory . '/' . $fileName;

        if (file_exists($filePath)) {
            if ($this->isFileSizeCorrect($filePath, $url)) {
                return $filePath;
            }

            unlink($filePath);
        }

        $fp = fopen($filePath, 'w');
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        curl_exec($ch);

        curl_close($ch);
        fclose($fp);

        return $filePath;
    }

    /**
     * @param $path
     * @return Directory
     */
    protected function wrap($path)
    {
        $pathToDirectory = glob($path . '_*');
        if ($pathToDirectory) {
            $pathToDirectory = $pathToDirectory[0];
        } else {
            $pathToDirectory = Dearchiver::extract($this->fileDirectory, $path);
        }
        $this->addVersionId($pathToDirectory);

        return new Directory($pathToDirectory);
    }

    /**
     * @param $pathToDirectory
     */
    protected function addVersionId($pathToDirectory)
    {
        $versionId = $this->getLastFileInfo()->getVersionId();
        file_put_contents($pathToDirectory . '/VERSION_ID_' . $versionId, 'Версия: ' . $versionId);
    }

    /**
     * Check size for downloaded file and file in fias server
     *
     * @param $filePath
     * @param $url
     * @return bool
     */
    public function isFileSizeCorrect($filePath, $url)
    {
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);

        curl_exec($ch);

        $correctSize = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);

        curl_close($ch);

        return (filesize($filePath) == $correctSize);
    }

    /**
     * Check is update required
     *
     * @param $currentVersion
     * @return bool
     */
    public function isUpdateRequired($currentVersion)
    {
        $filesInfo = $this->getLastFileInfo();

        return ($currentVersion === $filesInfo->getVersionId()) ? false : true;
    }

    /**
     * @param SoapResultWrapper $filesInfo
     * @return Directory
     */
    public function loadInitFile(SoapResultWrapper $filesInfo)
    {
        return $this->load($filesInfo->getInitFileName(), $filesInfo->getInitFileUrl());
    }

    /**
     * @param SoapResultWrapper $filesInfo
     * @return Directory
     */
    public function loadUpdateFile(SoapResultWrapper $filesInfo)
    {
        return $this->load($filesInfo->getUpdateFileName(), $filesInfo->getUpdateFileUrl());
    }

    /**
     * @param string $filename
     * @param string $url
     * @return Directory
     */
    private function load($filename, $url)
    {
        return $this->wrap(
            $this->loadFile($filename, $url)
        );
    }

    public function wrapFile($file)
    {
        return $this->wrap($file);
    }

    public function wrapDirectory($pathToDirectory)
    {
        return new Directory($pathToDirectory);
    }
}