<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Tests\Fixtures\Command;

use SixtyEightPublishers\TracyGitVersion\Repository\GitCommandInterface;

final class BarCommand implements GitCommandInterface
{
	private int $num;

	/**
	 * @param int $num
	 */
	public function __construct(int $num)
	{
		$this->num = $num;
	}

	/**
	 * @return int
	 */
	public function getNum(): int
	{
		return $this->num;
	}

	/**
	 * {@inheritDoc}
	 */
	public function __toString(): string
	{
		return sprintf('BAR(%d)', $this->num);
	}
}
