<?php

use Symfony\Component\Process\Process;

require __DIR__ . '/vendor/autoload.php';

$script = array_shift($argv);

if ($argc < 4) {
    printf('Usage: %s runs concurrency command' . PHP_EOL, $script);
    echo '  runs         The number of runs', PHP_EOL;
    echo '  concurrency  The number of concurrent processes', PHP_EOL;
    echo '  command      The command to execute', PHP_EOL;
    exit(1);
}

$runs = array_shift($argv);
$concurrency = array_shift($argv);

$checks = [
    'runs'        => $runs,
    'concurrency' => $concurrency
];

foreach ($checks as $name => $value) {
    if (! ctype_digit($value) || $value === '0') {
        printf('%s is not a valid value for %s.' . PHP_EOL, $value, $name);
        exit(1);
    }
}

$runs        = (int) $runs;
$concurrency = (int) $concurrency;

$command = implode(' ', array_map('escapeshellarg', $argv));

$processes = [];

$filter = function(Process $process) {
    if ($process->isRunning()) {
        return true;
    }

    if ($process->getExitCode() !== 0) {
        echo PHP_EOL, 'Received exit code ' . $process->getExitCode() . ', aborting.', PHP_EOL;
        exit;
    }

    return false;
};

$n = 0;
$t = microtime(true);

for (;;) {
    $time = microtime(true);
    $processes = array_values(array_filter($processes, $filter));
    $count = count($processes);
    if ($n !== $runs && $count < $concurrency) {
        $process = new Process($command);
        $process->start(function($type, $data) {
            if ($type === Process::OUT) {
                fwrite(STDOUT, $data);
            } elseif ($type === Process::ERR) {
                fwrite(STDERR, $data);
            } else {
                echo 'Unknown output type ', $type, PHP_EOL;
                exit(1);
            }
        });
        echo '.';
        $processes[] = $process;
        $count++;
        $n++;
    }

    if ($n === $runs && $count === 0) {
        break;
    }
}

echo PHP_EOL;

$totalTime = microtime(true) - $t;
$timePerRun = $totalTime / $runs;
$runsPerSecond = 1 / $timePerRun;

printf('Total time: %.3f seconds' . PHP_EOL, $totalTime);
printf('Average time per run: %.3f seconds' . PHP_EOL, $timePerRun);
printf('Runs per second: %.2f' . PHP_EOL, $runsPerSecond);
