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

use Discord\Builders\Components\Button;

describe('Button', function () {
    describe('construction', function () {
        it('creates a primary button via constructor', function () {
            $button = new Button(Button::STYLE_PRIMARY, 'btn_id');
            expect($button->getStyle())->toBe(Button::STYLE_PRIMARY);
        });

        it('creates a button via ::new()', function () {
            $button = Button::new(Button::STYLE_SECONDARY, 'my_button');
            expect($button)->toBeInstanceOf(Button::class);
            expect($button->getStyle())->toBe(Button::STYLE_SECONDARY);
        });

        it('throws InvalidArgumentException for invalid style', function () {
            expect(fn () => Button::new(99))->toThrow(\InvalidArgumentException::class, 'Invalid button style.');
        });
    });

    describe('factory methods', function () {
        it('creates a primary button with ::primary()', function () {
            $button = Button::primary('btn_primary');
            expect($button->getStyle())->toBe(Button::STYLE_PRIMARY);
        });

        it('creates a secondary button with ::secondary()', function () {
            $button = Button::secondary('btn_secondary');
            expect($button->getStyle())->toBe(Button::STYLE_SECONDARY);
        });

        it('creates a success button with ::success()', function () {
            $button = Button::success('btn_success');
            expect($button->getStyle())->toBe(Button::STYLE_SUCCESS);
        });

        it('creates a danger button with ::danger()', function () {
            $button = Button::danger('btn_danger');
            expect($button->getStyle())->toBe(Button::STYLE_DANGER);
        });

        it('creates a link button with ::link()', function () {
            $button = Button::link('https://example.com');
            expect($button->getStyle())->toBe(Button::STYLE_LINK);
            expect($button->getURL())->toBe('https://example.com');
        });

        it('creates a premium button with ::premium()', function () {
            $button = Button::premium('sku_123');
            expect($button->getStyle())->toBe(Button::STYLE_PREMIUM);
            expect($button->getSkuId())->toBe('sku_123');
        });
    });

    describe('label', function () {
        it('sets and gets a label', function () {
            $button = Button::primary('btn')->setLabel('Click me');
            expect($button->getLabel())->toBe('Click me');
        });

        it('throws LengthException when label exceeds 80 characters', function () {
            expect(fn () => Button::primary('btn')->setLabel(str_repeat('a', 81)))
                ->toThrow(\LengthException::class, 'Label must be maximum 80 characters.');
        });

        it('accepts exactly 80 character label', function () {
            $button = Button::primary('btn')->setLabel(str_repeat('a', 80));
            expect($button->getLabel())->toHaveLength(80);
        });

        it('clears label when set to null', function () {
            $button = Button::primary('btn')->setLabel('Click me')->setLabel(null);
            expect($button->getLabel())->toBeNull();
        });
    });

    describe('custom_id', function () {
        it('sets and validates custom ID', function () {
            $button = Button::primary('initial')->setLabel('Click me')->setCustomId('new_id');
            expect($button->jsonSerialize()['custom_id'])->toBe('new_id');
        });

        it('throws LengthException when custom ID exceeds 100 characters', function () {
            expect(fn () => Button::primary('btn')->setCustomId(str_repeat('a', 101)))
                ->toThrow(\LengthException::class, 'Custom ID must be maximum 100 characters.');
        });

        it('throws LogicException when setting custom ID on link button', function () {
            expect(fn () => Button::link('https://example.com')->setCustomId('custom'))
                ->toThrow(\LogicException::class, 'You cannot set the custom ID of a link or premium button.');
        });

        it('throws LogicException when setting custom ID on premium button', function () {
            expect(fn () => Button::premium('sku_123')->setCustomId('custom'))
                ->toThrow(\LogicException::class, 'You cannot set the custom ID of a link or premium button.');
        });
    });

    describe('url', function () {
        it('sets and gets URL for link button', function () {
            $button = Button::link('https://example.com');
            expect($button->getURL())->toBe('https://example.com');
        });

        it('throws LogicException when setting URL on non-link button', function () {
            expect(fn () => Button::primary('btn')->setUrl('https://example.com'))
                ->toThrow(\LogicException::class, 'You cannot set the URL of a non-link button.');
        });

        it('throws LengthException when URL exceeds 512 characters', function () {
            $longUrl = 'https://example.com/' . str_repeat('a', 500);
            expect(fn () => Button::link('https://example.com')->setUrl($longUrl))
                ->toThrow(\LengthException::class, 'URL cannot exceed 512 characters.');
        });
    });

    describe('disabled state', function () {
        it('sets the button as disabled', function () {
            $button = Button::primary('btn')->setLabel('Click me')->setDisabled(true);
            expect($button->isDisabled())->toBeTrue();
        });

        it('sets the button as enabled', function () {
            $button = Button::primary('btn')
                ->setLabel('Click me')
                ->setDisabled(true)
                ->setDisabled(false);
            expect($button->isDisabled())->toBeFalse();
        });
    });

    describe('emoji', function () {
        it('sets an emoji from a unicode string', function () {
            $button = Button::primary('btn')->setEmoji('👍');
            $emoji = $button->getEmoji();
            expect($emoji)->toBeArray();
            expect($emoji['name'])->toBe('👍');
            expect($emoji['id'])->toBeNull();
            expect($emoji['animated'])->toBeFalse();
        });

        it('sets an animated custom emoji', function () {
            $button = Button::primary('btn')->setEmoji('a:animated_emoji:123456789');
            $emoji = $button->getEmoji();
            expect($emoji['animated'])->toBeTrue();
            expect($emoji['name'])->toBe('animated_emoji');
            expect($emoji['id'])->toBe('123456789');
        });

        it('sets a non-animated custom emoji', function () {
            $button = Button::primary('btn')->setEmoji(':static_emoji:123456789');
            $emoji = $button->getEmoji();
            expect($emoji['animated'])->toBeFalse();
            expect($emoji['name'])->toBe('static_emoji');
            expect($emoji['id'])->toBe('123456789');
        });

        it('clears emoji when set to null', function () {
            $button = Button::primary('btn')
                ->setEmoji('👍')
                ->setEmoji(null);
            expect($button->getEmoji())->toBeNull();
        });
    });

    describe('jsonSerialize()', function () {
        it('serializes a primary button correctly', function () {
            $json = Button::primary('click_me')
                ->setLabel('Click Me!')
                ->jsonSerialize();

            expect($json['type'])->toBe(2); // TYPE_BUTTON
            expect($json['style'])->toBe(Button::STYLE_PRIMARY);
            expect($json['label'])->toBe('Click Me!');
            expect($json['custom_id'])->toBe('click_me');
        });

        it('serializes a link button correctly', function () {
            $json = Button::link('https://discord.com')
                ->setLabel('Visit Discord')
                ->jsonSerialize();

            expect($json['style'])->toBe(Button::STYLE_LINK);
            expect($json['url'])->toBe('https://discord.com');
        });

        it('serializes a premium button correctly', function () {
            $json = Button::premium('sku_123')->jsonSerialize();

            expect($json['style'])->toBe(Button::STYLE_PREMIUM);
            expect($json['sku_id'])->toBe('sku_123');
        });

        it('throws DomainException when non-premium button has no label', function () {
            expect(fn () => Button::primary('btn')->jsonSerialize())
                ->toThrow(\DomainException::class, "Non-Premium buttons must have a `label` field set.");
        });

        it('throws DomainException when link button has no URL', function () {
            $button = new Button(Button::STYLE_LINK);
            expect(fn () => $button->setLabel('Link')->jsonSerialize())
                ->toThrow(\DomainException::class, "Link buttons must have a `url` field set.");
        });

        it('throws DomainException when premium button has no SKU ID', function () {
            $button = new Button(Button::STYLE_PREMIUM);
            expect(fn () => $button->jsonSerialize())
                ->toThrow(\DomainException::class, "Premium buttons must have a `sku_id` field set.");
        });

        it('includes disabled flag when set', function () {
            $json = Button::primary('btn')
                ->setLabel('Disabled')
                ->setDisabled(true)
                ->jsonSerialize();
            expect($json['disabled'])->toBeTrue();
        });

        it('omits disabled when not set', function () {
            $json = Button::primary('btn')
                ->setLabel('Active')
                ->jsonSerialize();
            expect($json)->not->toHaveKey('disabled');
        });
    });
});
