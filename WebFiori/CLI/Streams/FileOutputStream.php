<?php
namespace WebFiori\Cli\Streams;

use WebFiori\File\Exceptions\FileException;
use WebFiori\File\File;
/**
 * A class that implements output stream which can be based on files.
 *
 * @author Ibrahim
 */
class FileOutputStream implements OutputStream {
    private $file;

    /**
     * Creates new instance of the class.
     *
     * Note that the method will attempt to remove the file and re-create it.
     *
     * @param string $path The absolute path to the file that CLI engine
     * will send outputs to.
     *
     * @throws FileException If the class was not able to initiate output file.
     */
    public function __construct(string $path) {
        $this->file = new File($path);
        $this->reset();
    }
    /**
     * Send a line of string to the stream as output.
     * 
     * @param string $str The string that will be sent.
     * 
     * @param array $_ Any extra arguments to supply to the output.
     */
    public function println(string $str, ...$_) {
        $toPass = [
            $str."\n"
        ];

        foreach ($_ as $val) {
            $toPass[] = $val;
        }
        call_user_func_array([$this, 'prints'], $toPass);
    }

    /**
     * Send a line of string to the stream as output.
     *
     * Note that the given string will be appended to the string
     * where the pointer is currently at.
     *
     * @param string $str The string that will be sent.
     *
     * @param array $_ Any extra arguments to supply to the output.
     *
     * @throws FileException If the method was not able to send output.
     */
    public function prints(string $str, ...$_) {
        $arrayToPass = [
            $str
        ];

        foreach ($_ as $val) {
            $type = gettype($val);

            if ($type != 'array') {
                $arrayToPass[] = $val;
            }
        }

        $toWrite = call_user_func_array('sprintf', $arrayToPass);
        $this->file->setRawData($toWrite);
        $this->file->write();
    }

    /**
     * Removes the file that represents output stream and re-create it.
     * @throws FileException
     */
    public function reset() {
        $this->file->remove();
        $this->file->create(true);
    }
}
