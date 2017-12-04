# Benchmark

A command-line script to benchmark a command.

## What is it?

If you need to run a command several times across multiple processes, and check how much time it took, then this tool is for you.

## How to use it?

Ensure that you have PHP installed, and download [benchmark.phar](https://raw.githubusercontent.com/BenMorel/benchmark/0.1.0/bin/benchmark.phar).
Alternatively, you can install it with Composer.

Then run:

    ./benchmark.phar Runs Concurrency Command [...]

Where:

- `Runs` is the number of runs
- `Concurrency` is the number of concurrent processes
- `Command` is the command to execute, with its optional arguments

Example:

    ./benchmark.phar 50 8 php resize-image.php

You will get an output such as:

    ..................................................
    Total time: 8.467 seconds
    Average time per run: 0.423 seconds
    Runs per second: 2.36

To abort, just press <kbd>Ctrl</kbd> + <kbd>C</kbd>.
