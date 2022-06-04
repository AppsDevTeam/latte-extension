<?php

declare(strict_types=1);

namespace ADT\LatteExtension;

use Latte;

final class LatteExtension extends Latte\Extension
{
	private ?string $timestamp = null;

	public function __construct(private readonly ?string $timestampFile = null)
	{
		if ($timestampFile && !is_file($timestampFile)) {
			throw new \Exception("File '$timestampFile' not found or is not a file!");
		}
	}

	public function getTags(): array
	{
		return [
			'v' => [$this, 'createV'],
			'vn' => [$this, 'createVn'],
		];
	}

	public function createV(Latte\Compiler\Tag $tag): Latte\Compiler\Node
	{
		return new Latte\Compiler\Nodes\AuxiliaryNode(
			fn (Latte\Compiler\PrintContext $context) => $context->format("echo '?v=". $this->getVersion() ."'")
		);
	}

	public function createVn(Latte\Compiler\Tag $tag): Latte\Compiler\Node
	{
		return new Latte\Compiler\Nodes\AuxiliaryNode(
			fn (Latte\Compiler\PrintContext $context) => $context->format("echo '" . $this->getVersion() . "'")
		);
	}

	private function getVersion(): string
	{
		if ($this->timestamp !== null) {
			return $this->timestamp;
		}

		$this->timestamp = '';

		if ($this->timestampFile) {
			$this->timestamp = (string) filemtime($this->$timestampFile);
		}

		return $this->timestamp;
	}
}
