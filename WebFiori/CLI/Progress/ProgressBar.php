<?php
namespace WebFiori\Cli\Progress;

use WebFiori\Cli\Streams\OutputStream;

/**
 * A progress bar implementation for CLI applications.
 *
 * @author Ibrahim
 */
class ProgressBar {
    private int $current = 0;
    private bool $finished = false;
    private ProgressBarFormat $format;
    private float $lastUpdateTime = 0;
    private string $message = '';
    private OutputStream $output;
    private bool $overwrite = true;
    private array $progressHistory = [];
    private bool $started = false;
    private float $startTime;
    private ProgressBarStyle $style;
    private int $total = 100;
    private float $updateThrottle = 0.1; // Minimum seconds between updates
    private int $width = 50;

    /**
     * Creates a new progress bar.
     * 
     * @param OutputStream $output Output stream to write to
     * @param int $total Total number of steps
     */
    public function __construct(OutputStream $output, int $total = 100) {
        $this->output = $output;
        $this->total = max(1, $total);
        $this->style = new ProgressBarStyle();
        $this->format = new ProgressBarFormat();
        $this->startTime = microtime(true);
    }

    /**
     * Advances the progress bar by the specified number of steps.
     * 
     * @param int $step Number of steps to advance
     * @return ProgressBar
     */
    public function advance(int $step = 1): ProgressBar {
        $this->setCurrent($this->current + $step);

        return $this;
    }

    /**
     * Finishes the progress bar.
     * 
     * @param string $message Optional completion message
     * @return ProgressBar
     */
    public function finish(string $message = ''): ProgressBar {
        if (!$this->finished) {
            $this->current = $this->total;
            $this->finished = true;

            if ($message) {
                $this->message = $message;
            }

            $this->display();

            if ($this->overwrite) {
                $this->output->prints("%s", "\n");
            }
        }

        return $this;
    }

    /**
     * Gets the current progress value.
     * 
     * @return int
     */
    public function getCurrent(): int {
        return $this->current;
    }

    /**
     * Gets the progress percentage.
     * 
     * @return float
     */
    public function getPercent(): float {
        return ($this->current / $this->total) * 100;
    }

    /**
     * Gets the total number of steps.
     * 
     * @return int
     */
    public function getTotal(): int {
        return $this->total;
    }

    /**
     * Checks if the progress bar is finished.
     * 
     * @return bool
     */
    public function isFinished(): bool {
        return $this->finished;
    }

    /**
     * Sets the current progress value.
     * 
     * @param int $current Current progress value
     * @return ProgressBar
     */
    public function setCurrent(int $current): ProgressBar {
        $this->current = max(0, min($current, $this->total));

        if (!$this->started) {
            $this->started = true;
            $this->startTime = microtime(true);
            $this->progressHistory = [];
            $this->finished = false;
        }

        $this->recordProgress();
        $this->display();

        return $this;
    }

    /**
     * Sets the format string.
     * 
     * @param string $format Format string with placeholders
     * @return ProgressBar
     */
    public function setFormat(string $format): ProgressBar {
        $this->format->setFormat($format);

        return $this;
    }

    /**
     * Sets whether to overwrite the current line.
     * 
     * @param bool $overwrite
     * @return ProgressBar
     */
    public function setOverwrite(bool $overwrite): ProgressBar {
        $this->overwrite = $overwrite;

        return $this;
    }

    /**
     * Sets the progress bar style.
     * 
     * @param ProgressBarStyle|string $style Style object or predefined style name
     * @return ProgressBar
     */
    public function setStyle($style): ProgressBar {
        if (is_string($style)) {
            $this->style = ProgressBarStyle::fromName($style);
        } else {
            $this->style = $style;
        }

        return $this;
    }

    /**
     * Sets the total number of steps.
     * 
     * @param int $total Total steps
     * @return ProgressBar
     */
    public function setTotal(int $total): ProgressBar {
        $this->total = max(1, $total);
        $this->current = min($this->current, $this->total);

        return $this;
    }

    /**
     * Sets the update throttle time.
     * 
     * @param float $seconds Minimum seconds between updates
     * @return ProgressBar
     */
    public function setUpdateThrottle(float $seconds): ProgressBar {
        $this->updateThrottle = max(0, $seconds);

        return $this;
    }

    /**
     * Sets the progress bar width.
     * 
     * @param int $width Width in characters
     * @return ProgressBar
     */
    public function setWidth(int $width): ProgressBar {
        $this->width = max(1, $width);

        return $this;
    }

    /**
     * Starts the progress bar.
     * 
     * @param string $message Optional message to display
     * @return ProgressBar
     */
    public function start(string $message = ''): ProgressBar {
        $this->started = true;
        $this->startTime = microtime(true);
        $this->message = $message;
        $this->current = 0;
        $this->progressHistory = [];
        $this->finished = false;

        $this->display();

        return $this;
    }

    /**
     * Displays the progress bar.
     */
    private function display(): void {
        $now = microtime(true);

        // Throttle updates unless finished
        if (!$this->finished && ($now - $this->lastUpdateTime) < $this->updateThrottle) {
            return;
        }

        $this->lastUpdateTime = $now;

        $values = [
            'bar' => $this->renderBar(),
            'percent' => number_format($this->getPercent(), 1),
            'current' => $this->current,
            'total' => $this->total,
            'elapsed' => ProgressBarFormat::formatDuration($this->getElapsed()),
            'eta' => ProgressBarFormat::formatDuration($this->getEta()),
            'rate' => ProgressBarFormat::formatRate($this->getRate()),
            'memory' => ProgressBarFormat::formatMemory(memory_get_usage(true))
        ];

        $output = $this->format->render($values);

        if ($this->message) {
            $output = $this->message.' '.$output;
        }

        if ($this->overwrite && $this->started) {
            $this->output->prints("%s", "\r".$output);
        } else {
            $this->output->prints("%s", $output."\n");
        }
    }

    /**
     * Gets elapsed time since start.
     * 
     * @return float Elapsed seconds
     */
    private function getElapsed(): float {
        return microtime(true) - $this->startTime;
    }

    /**
     * Calculates estimated time to completion.
     * 
     * @return float Estimated seconds remaining
     */
    private function getEta(): float {
        $rate = $this->getRate();

        if ($rate <= 0 || $this->current >= $this->total) {
            return 0;
        }

        $remaining = $this->total - $this->current;

        return $remaining / $rate;
    }

    /**
     * Calculates the current rate of progress.
     * 
     * @return float Progress per second
     */
    private function getRate(): float {
        if (count($this->progressHistory) < 2) {
            return 0;
        }

        $first = reset($this->progressHistory);
        $last = end($this->progressHistory);

        $timeDiff = $last['time'] - $first['time'];
        $progressDiff = $last['progress'] - $first['progress'];

        return $timeDiff > 0 ? $progressDiff / $timeDiff : 0;
    }

    /**
     * Records progress for rate calculation.
     */
    private function recordProgress(): void {
        $now = microtime(true);
        $this->progressHistory[] = [
            'time' => $now,
            'progress' => $this->current
        ];

        // Keep only recent history (last 10 seconds)
        $cutoff = $now - 10;
        $this->progressHistory = array_filter($this->progressHistory, function ($entry) use ($cutoff) {
            return $entry['time'] >= $cutoff;
        });
    }

    /**
     * Renders the progress bar.
     * 
     * @return string Rendered progress bar
     */
    private function renderBar(): string {
        $percent = $this->getPercent();
        $filledWidth = (int)round(($percent / 100) * $this->width);
        $emptyWidth = $this->width - $filledWidth;

        $bar = str_repeat($this->style->getBarChar(), $filledWidth);
        $bar .= str_repeat($this->style->getEmptyChar(), $emptyWidth);

        return $bar;
    }
}
