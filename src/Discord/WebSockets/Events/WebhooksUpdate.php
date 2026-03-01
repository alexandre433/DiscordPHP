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

namespace Discord\WebSockets\Events;

use Discord\Parts\Channel\Channel;
use Discord\WebSockets\Event;

/**
 * @link https://discord.com/developers/docs/topics/gateway-events#webhooks-update
 *
 * @since 7.0.0
 */
class WebhooksUpdate extends Event
{
    /**
     * @inheritDoc
     */
    public function handle($data)
    {
        if ($guild = yield $this->discord->guilds->cacheGet($data->guild_id)) {
            /** @var ?Channel */
            if (! $channel = yield $guild->channels->cacheGet($data->channel_id)) {
                /** @var ?Thread */
                $channel = yield from $guild->getThread($data->channel_id);
            }

            return [$guild, $channel ?? (object) ['id' => $data->channel_id]];
        }

        return [(object) ['id' => $data->guild_id], (object) ['id' => $data->channel_id]];
    }
}
