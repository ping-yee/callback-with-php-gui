<?php 

require_once __DIR__ . '/fileHandler.php';
require_once __DIR__ . '/vendor/autoload.php';

use Gui\Application;
use Gui\Components\Label;
use Gui\Components\Shape;

$application = new Application();
$fileHandle  = new FileHandler();

if ($argc != 3) {
    print("必須在引數中給予來源檔案與目標檔案的位置。");
    print(PHP_EOL);
    exit();
}

$resource = $argv[1];
$target   = $argv[2];

if (!is_file($resource)) {
    print("來源檔案並不存在。");
    print(PHP_EOL);
    exit();
}

if (is_file($target)) {
    print("目標檔案已經存在。");
    print(PHP_EOL);
    exit();
}

$application->on('start', function() use ($application, $fileHandle, $resource, $target) {
    $label = (new Label())
                ->setLeft(90)
                ->setTop(25)
                ->setFontSize(15)
                ->setAutoSize(true)
                ->setText('目前進度：0%');

    $shape = (new Shape())
                ->setBorderColor('#3498db')
                ->setTop(80)
                ->setWidth(230)
                ->setHeight(90)
                ->setLeft(40);

    $application->getLoop()->addPeriodicTimer(0.001,function() use ($application, $fileHandle, $resource, $target, $label, $shape)
    {
        $fileHandle->fileHandle($resource, $target, function ($data) use ($application, $label, $shape) {
            $label->setText("目前進度：{$data}%");

            $shape->setWidth(230 * ($data / 100));

            if($data >= 100){
                $label->setText("目前進度：100%");
                $shape->setWidth(230);
                $application->getLoop()->stop();
            }
        });
    });

});

$application->run();