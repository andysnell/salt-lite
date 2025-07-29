<?php

declare(strict_types=1);

namespace App;

use PhoneBurner\SaltLite\Framework\App\EnvironmentLoader;

// This file is the bootstrap file for the application. It is responsible for
// setting up the environment, loading the necessary files, and defining constants
// that are used throughout the application. It is included by the main entry
// points of the application, such as the CLI and HTTP entry points, as well as
// the test bootstrap files. It should not be included directly by any other files
// in the application, as it is intended to be the first file loaded in the
// application lifecycle, but it is not a part of the application itself. (E.g.
// the application can be bootstrapped and torn down multiple times, but this
// file should only be loaded once per process.)

// Environment Bootstrapping & Normalization
EnvironmentLoader::init(\dirname(__DIR__));

// Application Specific Environment Setup & Normalization
