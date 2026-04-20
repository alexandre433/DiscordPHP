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

use Discord\Builders\ChannelBuilder;
use Discord\Parts\Channel\Channel;

describe('ChannelBuilder', function () {
    describe('::new()', function () {
        it('creates a new instance with a name', function () {
            $builder = ChannelBuilder::new('general');
            expect($builder)->toBeInstanceOf(ChannelBuilder::class);
            expect($builder->jsonSerialize())->toHaveKey('name', 'general');
        });
    });

    describe('name', function () {
        it('sets a valid channel name', function () {
            $builder = ChannelBuilder::new('announcements');
            expect($builder->jsonSerialize()['name'])->toBe('announcements');
        });

        it('throws LengthException when name is empty', function () {
            expect(fn () => ChannelBuilder::new(''))->toThrow(\LengthException::class);
        });

        it('throws LengthException when name exceeds 100 characters', function () {
            expect(fn () => ChannelBuilder::new(str_repeat('a', 101)))
                ->toThrow(\LengthException::class, 'Channel name must be between 1 and 100 characters.');
        });

        it('accepts exactly 100 character name', function () {
            $builder = ChannelBuilder::new(str_repeat('a', 100));
            expect($builder->jsonSerialize()['name'])->toHaveLength(100);
        });
    });

    describe('type', function () {
        it('sets a valid channel type', function () {
            $builder = ChannelBuilder::new('voice')
                ->setType(Channel::TYPE_GUILD_VOICE);
            expect($builder->jsonSerialize()['type'])->toBe(Channel::TYPE_GUILD_VOICE);
        });

        it('throws InvalidArgumentException for invalid channel type', function () {
            expect(fn () => ChannelBuilder::new('test')->setType(999))
                ->toThrow(\InvalidArgumentException::class, 'Invalid channel type specified.');
        });

        it('omits type when not set', function () {
            $builder = ChannelBuilder::new('general');
            expect($builder->jsonSerialize())->not->toHaveKey('type');
        });
    });

    describe('topic', function () {
        it('sets a channel topic', function () {
            $builder = ChannelBuilder::new('general')->setTopic('Welcome!');
            expect($builder->jsonSerialize()['topic'])->toBe('Welcome!');
        });

        it('clears topic when set to null', function () {
            $builder = ChannelBuilder::new('general')
                ->setTopic('Old topic')
                ->setTopic(null);
            expect($builder->jsonSerialize())->not->toHaveKey('topic');
        });

        it('throws LengthException when topic exceeds 1024 characters', function () {
            expect(fn () => ChannelBuilder::new('general')->setTopic(str_repeat('a', 1025)))
                ->toThrow(\LengthException::class, 'Channel topic must be 0-1024 characters.');
        });

        it('accepts exactly 1024 character topic', function () {
            $builder = ChannelBuilder::new('general')->setTopic(str_repeat('a', 1024));
            expect($builder->jsonSerialize()['topic'])->toHaveLength(1024);
        });
    });

    describe('bitrate', function () {
        it('sets a valid bitrate', function () {
            $builder = ChannelBuilder::new('voice')->setBitrate(64000);
            expect($builder->jsonSerialize()['bitrate'])->toBe(64000);
        });

        it('throws OutOfRangeException when bitrate is below 8000', function () {
            expect(fn () => ChannelBuilder::new('voice')->setBitrate(7999))
                ->toThrow(\OutOfRangeException::class, 'Bitrate must be at least 8000.');
        });

        it('accepts the minimum bitrate of 8000', function () {
            $builder = ChannelBuilder::new('voice')->setBitrate(8000);
            expect($builder->jsonSerialize()['bitrate'])->toBe(8000);
        });

        it('clears bitrate when set to null', function () {
            $builder = ChannelBuilder::new('voice')
                ->setBitrate(64000)
                ->setBitrate(null);
            expect($builder->jsonSerialize())->not->toHaveKey('bitrate');
        });
    });

    describe('user_limit', function () {
        it('sets a user limit', function () {
            $builder = ChannelBuilder::new('voice')->setUserLimit(10);
            expect($builder->jsonSerialize()['user_limit'])->toBe(10);
        });

        it('clears user limit when set to null', function () {
            $builder = ChannelBuilder::new('voice')
                ->setUserLimit(10)
                ->setUserLimit(null);
            expect($builder->jsonSerialize())->not->toHaveKey('user_limit');
        });

        it('sets unlimited (0) user limit', function () {
            $builder = ChannelBuilder::new('voice')->setUserLimit(0);
            expect($builder->jsonSerialize()['user_limit'])->toBe(0);
        });
    });

    describe('position', function () {
        it('sets the channel position', function () {
            $builder = ChannelBuilder::new('general')->setPosition(3);
            expect($builder->jsonSerialize()['position'])->toBe(3);
        });

        it('omits position when not set', function () {
            $builder = ChannelBuilder::new('general');
            expect($builder->jsonSerialize())->not->toHaveKey('position');
        });
    });

    describe('nsfw', function () {
        it('sets the channel as NSFW', function () {
            $builder = ChannelBuilder::new('adult-content')->setNsfw(true);
            expect($builder->jsonSerialize()['nsfw'])->toBeTrue();
        });

        it('sets the channel as not NSFW', function () {
            $builder = ChannelBuilder::new('general')->setNsfw(false);
            expect($builder->jsonSerialize()['nsfw'])->toBeFalse();
        });

        it('omits nsfw when not set', function () {
            $builder = ChannelBuilder::new('general');
            expect($builder->jsonSerialize())->not->toHaveKey('nsfw');
        });
    });

    describe('video_quality_mode', function () {
        it('sets video quality mode to 1 (auto)', function () {
            $builder = ChannelBuilder::new('voice')->setVideoQualityMode(1);
            expect($builder->jsonSerialize()['video_quality_mode'])->toBe(1);
        });

        it('sets video quality mode to 2 (720p)', function () {
            $builder = ChannelBuilder::new('voice')->setVideoQualityMode(2);
            expect($builder->jsonSerialize()['video_quality_mode'])->toBe(2);
        });

        it('throws InvalidArgumentException for invalid video quality mode', function () {
            expect(fn () => ChannelBuilder::new('voice')->setVideoQualityMode(3))
                ->toThrow(\InvalidArgumentException::class, 'Invalid video quality mode specified.');
        });
    });

    describe('rate_limit_per_user', function () {
        it('sets rate limit', function () {
            $builder = ChannelBuilder::new('general')->setRateLimitPerUser(10);
            expect($builder->jsonSerialize()['rate_limit_per_user'])->toBe(10);
        });

        it('clears rate limit when set to null', function () {
            $builder = ChannelBuilder::new('general')
                ->setRateLimitPerUser(10)
                ->setRateLimitPerUser(null);
            expect($builder->jsonSerialize())->not->toHaveKey('rate_limit_per_user');
        });
    });

    describe('jsonSerialize()', function () {
        it('always includes the name', function () {
            $body = ChannelBuilder::new('my-channel')->jsonSerialize();
            expect($body)->toHaveKey('name', 'my-channel');
        });

        it('includes multiple set properties', function () {
            $body = ChannelBuilder::new('general')
                ->setType(Channel::TYPE_GUILD_TEXT)
                ->setTopic('A place to chat')
                ->setNsfw(false)
                ->jsonSerialize();

            expect($body['name'])->toBe('general');
            expect($body['type'])->toBe(Channel::TYPE_GUILD_TEXT);
            expect($body['topic'])->toBe('A place to chat');
            expect($body['nsfw'])->toBeFalse();
        });
    });
});
