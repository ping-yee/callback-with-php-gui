<?php

class FileHandler
{
    /**
     * 傳入每次處理大小
     *
     * @var integer
     */
    private int $eachHandleSize = 0;

    /**
     * 建構方法，傳入每次處理大小並賦值
     * 若無傳入，則定義為一次處理 1mb
     *
     * @param integer $eachHandleSize
     */
    function __construct(int $eachHandleSize = 1024)
    {
        $this->eachHandleSize = $eachHandleSize;
    }

    /**
     * 處理檔案讀入讀出
     *
     * @param string $resource
     * @param string $targe
     * @param Closure|null $call
     * @return boolean
     */
    public function fileHandle(string $resource, string $targe, Closure $call = null): bool
    {
        $nowSize = 0;
        $eachHandleSize = $this->eachHandleSize;
        $fileTotalSize = filesize($resource);

        while ($nowSize != $fileTotalSize) {

            if ($call instanceof Closure) $call($this->calPer($nowSize,$fileTotalSize));
            
            if ($fileTotalSize < $eachHandleSize && $nowSize == 0) $eachHandleSize = $fileTotalSize;

            $data = file_get_contents($resource, false, null, $nowSize, $eachHandleSize);
            file_put_contents($targe, $data, FILE_APPEND);

            if ($nowSize + $eachHandleSize >= $fileTotalSize) $eachHandleSize = $fileTotalSize - $nowSize;

            $nowSize += $eachHandleSize;
        }

        if ($call instanceof Closure) $call($this->calPer($nowSize,$fileTotalSize));
        
        return true;
    }

    /**
     * 計算目前 % 數
     *
     * @param integer $nowSize
     * @param integer $fileHandle
     * @return float
     */
    public function calPer(int $nowSize, int $fileHandle): float
    {
        return number_format(($nowSize / $fileHandle)* 100, 2);
    }
}
