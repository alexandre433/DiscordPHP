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

use Discord\Builders\MessageBuilder;
use Discord\Parts\Channel\Message;

describe('MessageBuilder', function () {
    describe('::new()', function () {
        it('creates a new instance', function () {
            expect(MessageBuilder::new())->toBeInstanceOf(MessageBuilder::class);
        });
    });

    describe('content', function () {
        it('sets and gets the content', function () {
            $builder = MessageBuilder::new()->setContent('Hello, world!');
            expect($builder->getContent())->toBe('Hello, world!');
        });

        it('returns null when no content is set', function () {
            expect(MessageBuilder::new()->getContent())->toBeNull();
        });

        it('throws LengthException when content exceeds 2000 characters', function () {
            expect(fn () => MessageBuilder::new()->setContent(str_repeat('a', 2001)))
                ->toThrow(\LengthException::class, 'Message content must be less than or equal to 2000 characters.');
        });

        it('accepts exactly 2000 characters', function () {
            $builder = MessageBuilder::new()->setContent(str_repeat('a', 2000));
            expect($builder->getContent())->toHaveLength(2000);
        });
    });

    describe('nonce', function () {
        it('sets and gets an integer nonce', function () {
            $builder = MessageBuilder::new()->setNonce(12345);
            expect($builder->getNonce())->toBe(12345);
        });

        it('sets and gets a string nonce', function () {
            $builder = MessageBuilder::new()->setNonce('my-nonce');
            expect($builder->getNonce())->toBe('my-nonce');
        });

        it('returns null when no nonce is set', function () {
            expect(MessageBuilder::new()->getNonce())->toBeNull();
        });

        it('throws LengthException when string nonce exceeds 25 characters', function () {
            expect(fn () => MessageBuilder::new()->setNonce(str_repeat('a', 26)))
                ->toThrow(\LengthException::class, 'Message nonce must be less than or equal to 25 characters.');
        });

        it('accepts exactly 25 character string nonce', function () {
            $builder = MessageBuilder::new()->setNonce(str_repeat('a', 25));
            expect($builder->getNonce())->toHaveLength(25);
        });
    });

    describe('username', function () {
        it('sets and gets a username', function () {
            $builder = MessageBuilder::new()->setUsername('WebhookBot');
            expect($builder->getUsername())->toBe('WebhookBot');
        });

        it('returns null when no username is set', function () {
            expect(MessageBuilder::new()->getUsername())->toBeNull();
        });

        it('throws LengthException when username exceeds 80 characters', function () {
            expect(fn () => MessageBuilder::new()->setUsername(str_repeat('a', 81)))
                ->toThrow(\LengthException::class, 'Username can be only up to 80 characters.');
        });

        it('accepts exactly 80 character username', function () {
            $builder = MessageBuilder::new()->setUsername(str_repeat('a', 80));
            expect($builder->getUsername())->toHaveLength(80);
        });
    });

    describe('avatar_url', function () {
        it('sets and gets an avatar URL', function () {
            $url = 'https://example.com/avatar.png';
            $builder = MessageBuilder::new()->setAvatarUrl($url);
            expect($builder->getAvatarUrl())->toBe($url);
        });

        it('returns null when no avatar URL is set', function () {
            expect(MessageBuilder::new()->getAvatarUrl())->toBeNull();
        });
    });

    describe('tts', function () {
        it('defaults to false', function () {
            expect(MessageBuilder::new()->getTts())->toBeFalse();
        });

        it('can be set to true', function () {
            $builder = MessageBuilder::new()->setTts(true);
            expect($builder->getTts())->toBeTrue();
        });

        it('can be toggled back to false', function () {
            $builder = MessageBuilder::new()->setTts(true)->setTts(false);
            expect($builder->getTts())->toBeFalse();
        });
    });

    describe('embeds', function () {
        it('adds an embed as array', function () {
            $embed = ['title' => 'Test'];
            $builder = MessageBuilder::new()->addEmbed($embed);
            expect($builder->getEmbeds())->toBe([$embed]);
        });

        it('supports adding multiple embeds', function () {
            $builder = MessageBuilder::new()
                ->addEmbed(['title' => 'First'])
                ->addEmbed(['title' => 'Second']);
            expect($builder->getEmbeds())->toHaveCount(2);
        });

        it('returns null when no embeds are set', function () {
            expect(MessageBuilder::new()->getEmbeds())->toBeNull();
        });

        it('throws OverflowException when more than 10 embeds are added', function () {
            $builder = MessageBuilder::new();
            for ($i = 0; $i < 10; $i++) {
                $builder->addEmbed(['title' => "Embed $i"]);
            }
            expect(fn () => $builder->addEmbed(['title' => 'Too many']))
                ->toThrow(\OverflowException::class, 'You can only have 10 embeds per message.');
        });

        it('clears existing embeds when setEmbeds is called', function () {
            $builder = MessageBuilder::new()
                ->addEmbed(['title' => 'Old'])
                ->setEmbeds([['title' => 'New']]);
            expect($builder->getEmbeds())->toBe([['title' => 'New']]);
        });
    });

    describe('stickers', function () {
        it('adds a sticker by string ID', function () {
            $builder = MessageBuilder::new()->addSticker('sticker-id');
            expect($builder->getStickers())->toBe(['sticker-id']);
        });

        it('returns empty array when no stickers are set', function () {
            expect(MessageBuilder::new()->getStickers())->toBe([]);
        });

        it('throws OverflowException when more than 3 stickers are added', function () {
            $builder = MessageBuilder::new()
                ->addSticker('id1')
                ->addSticker('id2')
                ->addSticker('id3');
            expect(fn () => $builder->addSticker('id4'))
                ->toThrow(\OverflowException::class, 'You can only add 3 stickers to a message');
        });

        it('removes a sticker by string ID', function () {
            $builder = MessageBuilder::new()
                ->addSticker('id1')
                ->addSticker('id2')
                ->removeSticker('id1');
            expect($builder->getStickers())->toBe(['id2']);
        });

        it('replaces all stickers when setStickers is called', function () {
            $builder = MessageBuilder::new()
                ->addSticker('old-id')
                ->setStickers(['new1', 'new2']);
            expect($builder->getStickers())->toBe(['new1', 'new2']);
        });
    });

    describe('files', function () {
        it('counts zero files initially', function () {
            expect(MessageBuilder::new()->numFiles())->toBe(0);
        });

        it('counts files after adding from content', function () {
            $builder = MessageBuilder::new()->addFileFromContent('test.txt', 'hello');
            expect($builder->numFiles())->toBe(1);
        });

        it('returns files added from content', function () {
            $builder = MessageBuilder::new()->addFileFromContent('test.txt', 'hello content');
            expect($builder->getFiles())->toBe([['test.txt', 'hello content']]);
        });

        it('clears all files', function () {
            $builder = MessageBuilder::new()
                ->addFileFromContent('test.txt', 'hello')
                ->clearFiles();
            expect($builder->numFiles())->toBe(0);
        });

        it('requires multipart when files are attached', function () {
            $builder = MessageBuilder::new()->addFileFromContent('test.txt', 'content');
            expect($builder->requiresMultipart())->toBeTrue();
        });

        it('does not require multipart when no files are attached', function () {
            expect(MessageBuilder::new()->requiresMultipart())->toBeFalse();
        });
    });

    describe('flags', function () {
        it('returns 0 when no flags are set', function () {
            expect(MessageBuilder::new()->getFlags())->toBe(0);
        });

        it('sets and retrieves flags', function () {
            $builder = MessageBuilder::new()->setFlags(Message::FLAG_SUPPRESS_EMBEDS);
            expect($builder->getFlags())->toBe(Message::FLAG_SUPPRESS_EMBEDS);
        });

        it('sets the suppress embeds flag', function () {
            $builder = MessageBuilder::new()->setSuppressEmbedsFlag();
            expect($builder->getFlags() & Message::FLAG_SUPPRESS_EMBEDS)->not->toBe(0);
        });

        it('unsets the suppress embeds flag', function () {
            $builder = MessageBuilder::new()
                ->setSuppressEmbedsFlag(true)
                ->setSuppressEmbedsFlag(false);
            expect($builder->getFlags() & Message::FLAG_SUPPRESS_EMBEDS)->toBe(0);
        });

        it('sets the suppress notifications flag', function () {
            $builder = MessageBuilder::new()->setSuppressNotificationsFlag();
            expect($builder->getFlags() & Message::FLAG_SUPPRESS_NOTIFICATIONS)->not->toBe(0);
        });

        it('sets the is components v2 flag', function () {
            $builder = MessageBuilder::new()->setIsComponentsV2Flag();
            expect($builder->getFlags() & Message::FLAG_IS_COMPONENTS_V2)->not->toBe(0);
        });

        it('aliases setV2Flag to setIsComponentsV2Flag', function () {
            $builder = MessageBuilder::new()->setV2Flag();
            expect($builder->getFlags() & Message::FLAG_IS_COMPONENTS_V2)->not->toBe(0);
        });
    });

    describe('enforce nonce', function () {
        it('returns null when enforce_nonce is not set', function () {
            expect(MessageBuilder::new()->getEnforceNonce())->toBeNull();
        });

        it('sets enforce_nonce to true', function () {
            $builder = MessageBuilder::new()->setEnforceNonce(true);
            expect($builder->getEnforceNonce())->toBeTrue();
        });

        it('sets enforce_nonce to false', function () {
            $builder = MessageBuilder::new()->setEnforceNonce(false);
            expect($builder->getEnforceNonce())->toBeFalse();
        });
    });

    describe('jsonSerialize()', function () {
        it('includes content in serialized output', function () {
            $body = MessageBuilder::new()->setContent('Hello')->jsonSerialize();
            expect($body)->toHaveKey('content', 'Hello');
        });

        it('includes tts in serialized output when true', function () {
            $body = MessageBuilder::new()->setContent('Hi')->setTts(true)->jsonSerialize();
            expect($body)->toHaveKey('tts', true);
        });

        it('omits tts when false', function () {
            $body = MessageBuilder::new()->setContent('Hi')->jsonSerialize();
            expect($body)->not->toHaveKey('tts');
        });

        it('returns components array even when empty due to ComponentsTrait initialization', function () {
            $body = MessageBuilder::new()->jsonSerialize();
            expect($body)->toHaveKey('components');
            expect($body['components'])->toBe([]);
        });

        it('does not throw when flags are set', function () {
            $body = MessageBuilder::new()->setIsComponentsV2Flag()->jsonSerialize();
            expect($body)->toHaveKey('flags');
        });

        it('omits content when IS_COMPONENTS_V2 flag is set', function () {
            $body = MessageBuilder::new()
                ->setContent('This should be omitted')
                ->setIsComponentsV2Flag()
                ->jsonSerialize();
            expect($body)->not->toHaveKey('content');
        });
    });
});
