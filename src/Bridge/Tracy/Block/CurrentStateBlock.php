<?php

declare(strict_types=1);

namespace SixtyEightPublishers\TracyGitVersion\Bridge\Tracy\Block;

use SixtyEightPublishers\TracyGitVersion\Repository\Entity\Tag;
use SixtyEightPublishers\TracyGitVersion\Repository\Entity\Head;
use SixtyEightPublishers\TracyGitVersion\Repository\Command\GetHeadCommand;
use SixtyEightPublishers\TracyGitVersion\Repository\GitRepositoryInterface;
use SixtyEightPublishers\TracyGitVersion\Repository\Command\GetLatestTagCommand;

final class CurrentStateBlock implements BlockInterface
{
	/**
	 * {@inheritDoc}
	 */
	public function render(GitRepositoryInterface $gitRepository): string
	{
		$head = $gitRepository->supports(GetHeadCommand::class) ? $gitRepository->handle(new GetHeadCommand()) : new Head(NULL, NULL);
		$latestTag = $gitRepository->supports(GetLatestTagCommand::class) ? $gitRepository->handle(new GetLatestTagCommand()) : NULL;

		$isHeadOnLatestTag = $latestTag instanceof Tag && NULL !== $head->getCommitHash() && $head->getCommitHash()->compare($latestTag->getCommitHash());

		$block = new SimpleTableBlock([
			'Branch' => $head->getBranch() ?? ($head->isDetached() ? 'detached' : 'not versioned'),
			'Commit' => NULL !== $head->getCommitHash() ? $head->getCommitHash()->getValue() : 'not versioned',
			'Latest tag' => $latestTag instanceof Tag ? sprintf('%s (%s)', $latestTag->getName(), $isHeadOnLatestTag ? 'current commit' : 'last known') : 'unknown',
		], 'Current state');

		return $block->render($gitRepository);
	}
}
