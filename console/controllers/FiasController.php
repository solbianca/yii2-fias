<?php
namespace solbianca\fias\console\controllers;

use solbianca\fias\console\base\Loader;
use solbianca\fias\helpers\FileHelper;
use solbianca\fias\console\models\ImportModel;
use solbianca\fias\console\models\UpdateModel;
use Yii;
use yii\console\Controller;
use yii\console\Exception;
use yii\helpers\Console;

class FiasController extends Controller
{
    /**
     * Init fias data in base.
     * If given parameter $file is null try to download full file, else try to use given file.
     *
     * @param string|null $file
     * @throws Exception
     * @throws \Exception
     * @throws \yii\db\Exception
     */
    public function actionInstall($file = null)
    {

        $loader = $this->getLoader();

        return (new ImportModel($loader, $file))->run();
    }

    /**
     * Update fias data in base
     *
     * @param string|null $file
     * @throws \Exception
     * @throws \yii\db\Exception
     */
    public function actionUpdate($file = null)
    {
        $loader = $this->getLoader();

        return (new UpdateModel($loader, $file))->run();
    }

    /**
     * Clear directory for upload/extract files
     */
    public function actionClearDirectory()
    {
        $directory = Yii::$app->getModule('fias')->directory;
        FileHelper::clearDirectory($directory);
        Console::output("Очистка директории '{$directory}' завершена.");
    }

    /**
     * @return Loader
     */
    protected function getLoader()
    {
        return new Loader(
            'http://fias.nalog.ru/WebServices/Public/DownloadService.asmx?WSDL',
            Yii::$app->getModule('fias')->directory
        );
    }
}