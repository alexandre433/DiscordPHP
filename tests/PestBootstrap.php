<?php

declare(strict_types=1);

/*
 * This file is a part of the DiscordPHP project.
 *
 * Copyright (c) 2015-2022 David Cole <david.cole1340@gmail.com>
 * Copyright (c) 2020-present Valithor Obsidion <valithor@discordphp.org>
 *
 * This file is subject to the MIT license that is bundled
 * with this source code in the LICENSE.md file.
 */

/**
 * Lightweight bootstrap for PEST unit tests.
 *
 * Unlike the main bootstrap, this does not start the Discord event loop
 * or create any Discord connections, making it suitable for pure unit tests
 * that do not require a live Discord instance.
 */
require_once __DIR__ . '/../vendor/autoload.php';
