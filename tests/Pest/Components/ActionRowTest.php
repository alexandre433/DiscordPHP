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

use Discord\Builders\Components\ActionRow;
use Discord\Builders\Components\Button;
use Discord\Builders\Components\StringSelect;

describe('ActionRow', function () {
    describe('::new()', function () {
        it('creates a new instance', function () {
            expect(ActionRow::new())->toBeInstanceOf(ActionRow::class);
        });
    });

    describe('addComponent()', function () {
        it('adds a button component', function () {
            $button = Button::primary('btn')->setLabel('Click');
            $row = ActionRow::new()->addComponent($button);
            expect($row->getComponents())->toBe([$button]);
        });

        it('throws InvalidArgumentException when adding an ActionRow inside another ActionRow', function () {
            $inner = ActionRow::new();
            expect(fn () => ActionRow::new()->addComponent($inner))
                ->toThrow(\InvalidArgumentException::class, 'You cannot add another `ActionRow` to this action row.');
        });

        it('throws OverflowException when adding more than 5 components', function () {
            $row = ActionRow::new();
            for ($i = 0; $i < 5; $i++) {
                $row->addComponent(Button::primary("btn$i")->setLabel("Btn $i"));
            }

            expect(fn () => $row->addComponent(Button::primary('btn6')->setLabel('Too many')))
                ->toThrow(\OverflowException::class, 'You can only have 5 components per action row.');
        });

        it('throws InvalidArgumentException when adding a second select menu', function () {
            $select1 = StringSelect::new('select1');
            $select2 = StringSelect::new('select2');

            expect(fn () => ActionRow::new()->addComponent($select1)->addComponent($select2))
                ->toThrow(\InvalidArgumentException::class, 'You cannot add more than one select menu to an action row.');
        });
    });

    describe('removeComponent()', function () {
        it('removes a component by reference', function () {
            $button1 = Button::primary('btn1')->setLabel('First');
            $button2 = Button::primary('btn2')->setLabel('Second');

            $row = ActionRow::new()
                ->addComponent($button1)
                ->addComponent($button2);

            $row->removeComponent($button1);

            expect($row->getComponents())->toBe([$button2]);
        });

        it('does nothing when removing a component that is not in the row', function () {
            $button = Button::primary('btn')->setLabel('Click');
            $notAdded = Button::primary('other')->setLabel('Other');

            $row = ActionRow::new()->addComponent($button);
            $row->removeComponent($notAdded);

            expect($row->getComponents())->toBe([$button]);
        });
    });

    describe('clearComponents()', function () {
        it('removes all components', function () {
            $row = ActionRow::new()
                ->addComponent(Button::primary('btn1')->setLabel('First'))
                ->addComponent(Button::primary('btn2')->setLabel('Second'))
                ->clearComponents();

            expect($row->getComponents())->toBe([]);
        });
    });

    describe('getComponents()', function () {
        it('returns empty array when no components are added', function () {
            expect(ActionRow::new()->getComponents())->toBe([]);
        });

        it('returns all added components', function () {
            $btn1 = Button::primary('btn1')->setLabel('First');
            $btn2 = Button::secondary('btn2')->setLabel('Second');

            $row = ActionRow::new()
                ->addComponent($btn1)
                ->addComponent($btn2);

            expect($row->getComponents())->toBe([$btn1, $btn2]);
        });
    });

    describe('jsonSerialize()', function () {
        it('serializes with type 1 and components array', function () {
            $button = Button::primary('btn')->setLabel('Click');
            $json = ActionRow::new()->addComponent($button)->jsonSerialize();

            expect($json['type'])->toBe(1); // TYPE_ACTION_ROW
            expect($json['components'])->toBe([$button]);
        });

        it('serializes an empty action row', function () {
            $json = ActionRow::new()->jsonSerialize();

            expect($json['type'])->toBe(1);
            expect($json['components'])->toBe([]);
        });
    });
});
