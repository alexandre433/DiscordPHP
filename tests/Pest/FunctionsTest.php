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

use function Discord\contains;
use function Discord\escapeMarkdown;
use function Discord\getColor;
use function Discord\getSnowflakeTimestamp;
use function Discord\poly_strlen;
use function Discord\studly;

describe('contains()', function () {
    it('returns true when string contains one of the given phrases', function () {
        expect(contains('hello, world!', ['hello']))->toBeTrue();
    });

    it('returns true when string contains multiple matching phrases', function () {
        expect(contains('phpunit tests', ['p', 'u']))->toBeTrue();
    });

    it('returns false when string contains none of the given phrases', function () {
        expect(contains('phpunit tests', ['a']))->toBeFalse();
    });

    it('returns false for an empty match array', function () {
        expect(contains('hello', []))->toBeFalse();
    });

    it('returns false for an empty string', function () {
        expect(contains('', ['hello']))->toBeFalse();
    });
});

describe('getColor()', function () {
    it('resolves color by html name', function () {
        expect(getColor('indianred'))->toBe(0xcd5c5c);
        expect(getColor('deepskyblue'))->toBe(0x00bfff);
    });

    it('returns integer color unchanged', function () {
        expect(getColor(0x00bfff))->toBe(0x00bfff);
        expect(getColor(0))->toBe(0);
    });

    it('parses hex string with 0x prefix', function () {
        expect(getColor('0x00bfff'))->toBe(0x00bfff);
    });

    it('parses hex string with # prefix', function () {
        expect(getColor('#ff0000'))->toBe(0xff0000);
    });

    it('returns 0 for unknown color name', function () {
        expect(getColor('notacolor'))->toBe(0);
    });

    it('is case-insensitive for color names', function () {
        expect(getColor('IndianRed'))->toBe(0xcd5c5c);
    });
});

describe('poly_strlen()', function () {
    it('returns the correct length for a normal string', function () {
        expect(poly_strlen('abcde'))->toBe(5);
    });

    it('returns 0 for an empty string', function () {
        expect(poly_strlen(''))->toBe(0);
    });

    it('returns 1 for a single space', function () {
        expect(poly_strlen(' '))->toBe(1);
    });
});

describe('studly()', function () {
    it('converts a multi-word string to StudlyCase', function () {
        expect(studly('trains are cool'))->toBe('TrainsAreCool');
        expect(studly('robo smells like bananas'))->toBe('RoboSmellsLikeBananas');
    });

    it('normalizes mixed-case input', function () {
        expect(studly('i LiKE TuRtLEs'))->toBe('ILikeTurtles');
    });

    it('handles a single word', function () {
        expect(studly('hello'))->toBe('Hello');
    });
});

describe('escapeMarkdown()', function () {
    it('escapes Discord markdown characters', function () {
        expect(escapeMarkdown('I ~~really~~ like ||trains||'))
            ->toBe('I \~\~really\~\~ like \|\|trains\|\|');
    });

    it('escapes bold markdown', function () {
        expect(escapeMarkdown('**Bananas**, in @@pyjamas'))
            ->toBe('\*\*Bananas\*\*, in \@\@pyjamas');
    });

    it('escapes blockquote and channel mention characters', function () {
        expect(escapeMarkdown('>Lopen naar de #zee'))
            ->toBe('\>Lopen naar de \#zee');
    });

    it('escapes colon characters', function () {
        expect(escapeMarkdown('hello there :D'))
            ->toBe('hello there \:D');
    });

    it('leaves plain text unchanged', function () {
        expect(escapeMarkdown('actually nothing should be changed now'))
            ->toBe('actually nothing should be changed now');
    });
});

describe('getSnowflakeTimestamp()', function () {
    it('returns a valid timestamp for a known Discord snowflake', function () {
        // Discord epoch: 2015-01-01T00:00:00.000Z = Unix 1420070400
        // Snowflake 175928847299117063 is a known Discord ID
        $timestamp = getSnowflakeTimestamp('175928847299117063');
        expect($timestamp)->not->toBeNull();
        expect($timestamp)->toBeFloat();
        // The timestamp should be after the Discord epoch
        expect($timestamp)->toBeGreaterThan(1420070400.0);
    });

    it('returns null for an invalid snowflake', function () {
        // A very large negative snowflake produces a pre-epoch timestamp
        expect(getSnowflakeTimestamp('-10000000000000000000000000'))->toBeNull();
    });
});
