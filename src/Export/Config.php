<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Export;

use SixtyEightPublishers\TracyGitVersion\Exception\ExportConfigException;
use SixtyEightPublishers\TracyGitVersion\Repository\Command\GetHeadCommand;
use SixtyEightPublishers\TracyGitVersion\Export\PartialExporter\HeadExporter;
use SixtyEightPublishers\TracyGitVersion\Repository\Command\GetLatestTagCommand;
use SixtyEightPublishers\TracyGitVersion\Repository\LocalDirectory\GitDirectory;
use SixtyEightPublishers\TracyGitVersion\Export\PartialExporter\LatestTagExporter;
use SixtyEightPublishers\TracyGitVersion\Repository\LocalDirectory\CommandHandler\GetHeadCommandHandler;
use SixtyEightPublishers\TracyGitVersion\Repository\LocalDirectory\CommandHandler\GetLatestTagCommandHandler;

final class Config
{
	public const OPTION_GIT_DIRECTORY = 'git_directory';
	public const OPTION_COMMAND_HANDLERS = 'command_handlers';
	public const OPTION_EXPORTERS = 'exporters';
	public const OPTION_OUTPUT_FILE = 'output_file';

	private array $options = [];

	/**
	 * @return static
	 */
	public static function create(): self
	{
		return new self();
	}

	/**
	 * @return static
	 */
	public static function createDefault(): self
	{
		return self::create()
			->setGitDirectory(GitDirectory::createAutoDetected())
			->addCommandHandlers([
				GetHeadCommand::class => new GetHeadCommandHandler(),
				GetLatestTagCommand::class => new GetLatestTagCommandHandler(),
			])
			->addExporters([
				new HeadExporter(),
				new LatestTagExporter(),
			]);
	}

	/**
	 * @param string $name
	 * @param mixed  $value
	 *
	 * @return $this
	 */
	public function setOption(string $name, $value): self
	{
		$this->options[$name] = $value;

		return $this;
	}

	/**
	 * @param string $name
	 * @param mixed  $value
	 *
	 * @return $this
	 */
	public function mergeOption(string $name, $value): self
	{
		$this->options[$name] = array_merge((array) ($this->options[$name] ?? []), (array) $value);

		return $this;
	}

	/**
	 * @param string $name
	 *
	 * @return bool
	 */
	public function hasOption(string $name): bool
	{
		return array_key_exists($name, $this->options);
	}

	/**
	 * @param string $name
	 *
	 * @return mixed
	 * @throws \SixtyEightPublishers\TracyGitVersion\Exception\ExportConfigException
	 */
	public function getOption(string $name)
	{
		if (!$this->hasOption($name)) {
			throw ExportConfigException::missingOption($name);
		}

		return $this->options[$name];
	}

	/**
	 * @param \SixtyEightPublishers\TracyGitVersion\Repository\LocalDirectory\GitDirectory $gitDirectory
	 *
	 * @return $this
	 */
	public function setGitDirectory(GitDirectory $gitDirectory): self
	{
		return $this->setOption(self::OPTION_GIT_DIRECTORY, $gitDirectory);
	}

	/**
	 * @param \SixtyEightPublishers\TracyGitVersion\Repository\GitCommandHandlerInterface[] $handlers
	 *
	 * @return $this
	 */
	public function addCommandHandlers(array $handlers): self
	{
		return $this->mergeOption(self::OPTION_COMMAND_HANDLERS, $handlers);
	}

	/**
	 * @param \SixtyEightPublishers\TracyGitVersion\Export\ExporterInterface[] $exporters
	 *
	 * @return $this
	 */
	public function addExporters(array $exporters): self
	{
		return $this->mergeOption(self::OPTION_EXPORTERS, $exporters);
	}

	/**
	 * @param string $outputFile
	 *
	 * @return $this
	 */
	public function setOutputFile(string $outputFile): self
	{
		return $this->setOption(self::OPTION_OUTPUT_FILE, $outputFile);
	}
}
