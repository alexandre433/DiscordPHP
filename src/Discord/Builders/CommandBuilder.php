<?php

/*
 * This file is a part of the DiscordPHP project.
 *
 * Copyright (c) 2015-present David Cole <david.cole1340@gmail.com>
 *
 * This file is subject to the MIT license that is bundled
 * with this source code in the LICENSE.md file.
 */

namespace Discord\Builders;

use Discord\Parts\Interactions\Command\Command;
use Discord\Parts\Interactions\Command\Option;
use JsonSerializable;

/**
 * Helper class used to build application commands.
 *
 * @author Mark `PeanutNL` Versluis
 */
class CommandBuilder implements JsonSerializable
{
    use CommandAttributes;

    /**
     * Type of the command. The type defaults to 1.
     *
     * @var int
     */
    protected int $type = Command::CHAT_INPUT;

    /**
     * Name of the command.
     *
     * @var string
     */
    protected string $name;

    /**
     * Localization dictionary for the name field. Values follow the same restrictions as name.
     *
     * @var string[]
     */
    protected array $name_localizations;

    /**
     * Description of the command. should be emtpy if the type is not CHAT_INPUT.
     *
     * @var string
     */
    protected string $description = '';

    /**
     * Localization dictionary for the description field. Values follow the same restrictions as description.
     *
     * @var string[]|null
     */
    protected array $description_localizations;

    /**
     * array with options.
     *
     * @var Option[]|null
     */
    protected array $options;

    /**
     * The default permission of the command. If true the command is enabled when the app is added to the guild.
     *
     * @var bool
     */
    protected bool $default_permission = true;

    /**
     * Creates a new command builder.
     *
     * @return $this
     */
    public static function new(): self
    {
        return new static();
    }

    /**
     * Returns all the options in the command.
     *
     * @return Option[]|null
     */
    public function getOptions(): ?array
    {
        return $this->options ?? null;
    }

    /**
     * Returns an array with all the options.
     *
     * @throws \LengthException
     * @throws \DomainException
     *
     * @return array
     */
    public function toArray(): array
    {
        $arrCommand = [
            'name' => $this->name,
            'name_localizations' => $this->name_localizations,
            'description' => $this->description,
            'description_localizations' => $this->name_localizations,
            'type' => $this->type,
            'default_permission' => $this->default_permission,
        ];

        foreach ($this->options ?? [] as $option) {
            $arrCommand['options'][] = $option->getRawAttributes();
        }

        return $arrCommand;
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
