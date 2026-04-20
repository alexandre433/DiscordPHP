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

use Discord\Builders\Components\Label;
use Discord\Builders\ModalBuilder;

describe('ModalBuilder', function () {
    describe('::new()', function () {
        it('creates an instance with title, custom_id, and components', function () {
            $label = new Label();
            $builder = ModalBuilder::new('My Modal', 'modal_id', [$label]);

            expect($builder->getTitle())->toBe('My Modal');
            expect($builder->getCustomId())->toBe('modal_id');
            expect($builder->getComponents())->toBe([$label]);
        });
    });

    describe('title', function () {
        it('sets and gets the modal title', function () {
            $builder = new ModalBuilder();
            $builder->setTitle('Welcome!');
            expect($builder->getTitle())->toBe('Welcome!');
        });

        it('throws LengthException when title exceeds 45 characters', function () {
            expect(fn () => (new ModalBuilder())->setTitle(str_repeat('a', 46)))
                ->toThrow(\LengthException::class, 'Modal title can not be longer than 45 characters');
        });

        it('accepts exactly 45 character title', function () {
            $builder = new ModalBuilder();
            $builder->setTitle(str_repeat('a', 45));
            expect($builder->getTitle())->toHaveLength(45);
        });
    });

    describe('custom_id', function () {
        it('sets and gets the custom ID', function () {
            $builder = new ModalBuilder();
            $builder->setCustomId('my_modal_id');
            expect($builder->getCustomId())->toBe('my_modal_id');
        });

        it('throws LengthException when custom ID exceeds 100 characters', function () {
            expect(fn () => (new ModalBuilder())->setCustomId(str_repeat('a', 101)))
                ->toThrow(\LengthException::class, 'Custom ID must be maximum 100 characters.');
        });

        it('accepts exactly 100 character custom ID', function () {
            $builder = new ModalBuilder();
            $builder->setCustomId(str_repeat('a', 100));
            expect($builder->getCustomId())->toHaveLength(100);
        });
    });

    describe('components', function () {
        it('adds a component', function () {
            $label = new Label();
            $builder = new ModalBuilder();
            $builder->addComponent($label);
            expect($builder->getComponents())->toBe([$label]);
        });

        it('sets components array', function () {
            $label = new Label();
            $builder = new ModalBuilder();
            $builder->setComponents([$label]);
            expect($builder->getComponents())->toBe([$label]);
        });

        it('returns empty array when no components are set', function () {
            expect((new ModalBuilder())->getComponents())->toBe([]);
        });

        it('throws OverflowException after 5 components', function () {
            $builder = new ModalBuilder();
            $builder->setComponents([
                new Label(),
                new Label(),
                new Label(),
                new Label(),
                new Label(),
            ]);

            expect(fn () => $builder->addComponent(new Label()))
                ->toThrow(\OverflowException::class, 'You can only have 5 components per modal.');
        });

        it('removes a component', function () {
            $label1 = new Label();
            $label2 = new Label();

            $builder = new ModalBuilder();
            $builder->setComponents([$label1, $label2]);
            $builder->removeComponent($label1);

            expect($builder->getComponents())->toBe([$label2]);
        });
    });

    describe('jsonSerialize()', function () {
        it('includes type, custom_id, title, and components in output', function () {
            $label = new Label();
            $builder = ModalBuilder::new('Test Modal', 'test_id', [$label]);

            $json = $builder->jsonSerialize();

            expect($json)->toHaveKey('type');
            expect($json['data'])->toHaveKey('custom_id', 'test_id');
            expect($json['data'])->toHaveKey('title', 'Test Modal');
            expect($json['data']['components'])->toBe([$label]);
        });
    });
});
