<?php
namespace webfiori\cli\streams;

/**
 * An interface that can be used to implement input streams at which CLI
 * can read input from.
 *
 * @author Ibrahim
 * 
 * @since 2.3.1
 * 
 * @version 1.0
 */
interface InputStream {
    /**
     * Reads specific number of bytes from the stream.
     * 
     * @param int $bytes The number of bytes at which the method will read.
     * 
     * @return string The method should return the bytes as string. If nothing
     * was fetched from the stream, the method should return empty string.
     */
    public function read(int $bytes = 1) : string;
    /**
     * Reads bytes from a stream till end of line.
     * 
     * Usually, end of line is represented by the constant PHP_EOL. Also, end 
     * of line can be (CR) or (LF) in some cases.
     * 
     * @return string The method should return the bytes as string. If nothing
     * was fetched from the stream, the method should return empty string.
     */
    public function readLine() : string;
}
