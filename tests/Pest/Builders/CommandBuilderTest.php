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

use Discord\Builders\CommandBuilder;
use Discord\Parts\Interactions\Command\Command;

describe('CommandBuilder', function () {
    describe('::new()', function () {
        it('creates a new instance', function () {
            expect(CommandBuilder::new())->toBeInstanceOf(CommandBuilder::class);
        });
    });

    describe('type', function () {
        it('defaults to CHAT_INPUT type', function () {
            $builder = CommandBuilder::new()->setName('test');
            expect($builder->jsonSerialize()['type'])->toBe(Command::CHAT_INPUT);
        });

        it('sets a valid command type', function () {
            $builder = CommandBuilder::new()->setType(Command::USER);
            $array = $builder->setName('test command')->jsonSerialize();
            expect($array['type'])->toBe(Command::USER);
        });

        it('throws InvalidArgumentException for invalid command type', function () {
            expect(fn () => CommandBuilder::new()->setType(99))
                ->toThrow(\InvalidArgumentException::class, 'Invalid type provided.');
        });
    });

    describe('name', function () {
        it('sets a valid slash command name', function () {
            $builder = CommandBuilder::new()->setName('greet');
            expect($builder->jsonSerialize()['name'])->toBe('greet');
        });

        it('throws LengthException when name is empty', function () {
            expect(fn () => CommandBuilder::new()->setName(''))
                ->toThrow(\LengthException::class, 'Command name can not be empty.');
        });

        it('throws LengthException when name exceeds 32 characters', function () {
            expect(fn () => CommandBuilder::new()->setName(str_repeat('a', 33)))
                ->toThrow(\LengthException::class, 'Command name can be only up to 32 characters long.');
        });

        it('accepts exactly 32 character name', function () {
            $builder = CommandBuilder::new()->setName(str_repeat('a', 32));
            expect($builder->jsonSerialize()['name'])->toHaveLength(32);
        });

        it('throws DomainException for slash command name with invalid characters', function () {
            expect(fn () => CommandBuilder::new()->setName('invalid name!'))
                ->toThrow(\DomainException::class, 'Slash command name contains invalid characters.');
        });

        it('allows hyphens and underscores in slash command names', function () {
            $builder = CommandBuilder::new()->setName('my-command_name');
            expect($builder->jsonSerialize()['name'])->toBe('my-command_name');
        });
    });

    describe('description', function () {
        it('sets a valid description', function () {
            $builder = CommandBuilder::new()->setName('greet')->setDescription('Greets the user');
            expect($builder->jsonSerialize()['description'])->toBe('Greets the user');
        });

        it('throws LengthException when description is empty', function () {
            expect(fn () => CommandBuilder::new()->setDescription(''))
                ->toThrow(\LengthException::class, 'Command description can not be empty.');
        });

        it('throws LengthException when description exceeds 100 characters', function () {
            expect(fn () => CommandBuilder::new()->setDescription(str_repeat('a', 101)))
                ->toThrow(\LengthException::class, 'Command description can be only up to 100 characters long.');
        });

        it('accepts exactly 100 character description', function () {
            $builder = CommandBuilder::new()->setName('greet')->setDescription(str_repeat('a', 100));
            expect($builder->jsonSerialize()['description'])->toHaveLength(100);
        });
    });

    describe('options', function () {
        it('returns empty array when no options are set', function () {
            expect(CommandBuilder::new()->getOptions())->toBe([]);
        });

        it('clears all options', function () {
            $builder = CommandBuilder::new()
                ->setName('say')
                ->setDescription('Say something')
                ->clearOptions();

            expect($builder->getOptions())->toBe([]);
        });
    });

    describe('permissions', function () {
        it('sets default member permissions', function () {
            $builder = CommandBuilder::new()->setName('ban')->setDefaultMemberPermissions('8');
            expect($builder->jsonSerialize()['default_member_permissions'])->toBe('8');
        });

        it('converts integer permissions to string', function () {
            $builder = CommandBuilder::new()->setName('kick')->setDefaultMemberPermissions(0x2);
            expect($builder->jsonSerialize()['default_member_permissions'])->toBe('2');
        });

        it('sets dm_permission', function () {
            $builder = CommandBuilder::new()->setName('global-cmd')->setDmPermission(false);
            expect($builder->jsonSerialize()['dm_permission'])->toBeFalse();
        });

        it('sets nsfw flag', function () {
            $builder = CommandBuilder::new()->setName('adult-cmd')->setNsfw(true);
            expect($builder->jsonSerialize()['nsfw'])->toBeTrue();
        });
    });

    describe('jsonSerialize()', function () {
        it('includes name and description', function () {
            $body = CommandBuilder::new()
                ->setName('ping')
                ->setDescription('Replies with Pong!')
                ->jsonSerialize();

            expect($body['name'])->toBe('ping');
            expect($body['description'])->toBe('Replies with Pong!');
        });

        it('does not include options when no options are set for CHAT_INPUT commands', function () {
            $body = CommandBuilder::new()
                ->setName('ping')
                ->setDescription('Replies with Pong!')
                ->jsonSerialize();

            expect($body)->not->toHaveKey('options');
        });

        it('does not include options for non-CHAT_INPUT commands', function () {
            $body = CommandBuilder::new()
                ->setType(Command::USER)
                ->setName('Report User')
                ->jsonSerialize();

            expect($body)->not->toHaveKey('options');
        });
    });
});
